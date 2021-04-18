<?php

namespace App\Controller\Admin;

use App\Entity\Admin;
use App\Entity\AdminMessageRead;
use App\Entity\Message;
use App\Form\AdminMessageFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/messageadmin", name="messageadmin_")
 */
class AdminMessageController extends AbstractController
{
    /**
     * @Route("/list", name="list")
     */
    public function messageAdminList(TranslatorInterface $translator):Response
    {

        $section = $translator->trans('messages administrator');

        $messages = $this->getDoctrine()->getRepository(Message::class)->findBy(['admin_recipient' => !null]);

        return $this->render('message/admin/new.html.twig', [
            'adminMessages' => $messages,
            'section' => $section,
            'active' => 'myspace'
        ]);
    }

    /**
     * @Route("/new", name="new")
     */
    public function messageAdminNew(Request $request, TranslatorInterface $translator):Response
    {
        $this->denyAccessUnlessGranted('ROLE_AGENT');
        $messages = $this->getDoctrine()->getRepository(Message::class)->findBy(['admin_recipient' => !null]);
        dump($messages);

        $section = $translator->trans('messages administrator');

        $message = new Message();
        $agents = $this->getDoctrine()->getRepository(Admin::class)->findAll();
        $form = $this->createForm(AdminMessageFormType::class, $message);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            
            $em= $this->getDoctrine()->getManager();

            foreach($agents as $agent) {
                $message->addAdminRecipient($agent);

                $adminMessageRead = new AdminMessageRead();
                $adminMessageRead->setAdmin($agent);
                $adminMessageRead->setMessage($message);
                $adminMessageRead->setNotRead(1);

                $em->persist($adminMessageRead);

            }
            
            $message->setFirstnameSender($this->getUser()->getFirstname());
            $message->setLastnameSender($this->getUser()->getLastname());
            $message->setSender($this->getUser()->getUsername());

            $em->persist($message);
            $em->flush();

            $successmsg = $translator->trans('Your message has been sent successfully');
            $this->addFlash('message_user', $successmsg);
            
            return $this->redirectToRoute('messageadmin_new');
            
        } 

        return $this->render('message/admin/new.html.twig', [
            'AdminMessageForm' => $form->createView(),
            'section' => $section,
            'active' => 'myspace'
        ]);
    }

}
