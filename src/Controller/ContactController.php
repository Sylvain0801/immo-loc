<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\MessageRead;
use App\Form\ContactFormType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/contact", name="contact_")
 */
class ContactController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index(Request $request, TranslatorInterface $translator, UserRepository $userRepository): Response
    {

        $message = new Message();
        $agents = $userRepository->findUsersByRole("ROLE_AGENT");
        
        $form = $this->createForm(ContactFormType::class, $message);
        $contact = $form->handleRequest($request);

        
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            
            // Donne accès au message à tous les agents
            foreach ($agents as $agent) {
                
                $message->addRecipient($agent);

                $messageRead = new MessageRead();
                $messageRead->setUser($agent);
                $messageRead->setMessage($message);
                $messageRead->setNotRead(1);

                $em->persist($messageRead);

            }
            $message->setSender($contact->get('email_sender')->getData());
            $em->persist($message);
            $em->flush();
            
            $successmsg = $translator->trans('Your message has been sent successfully');

            $this->addFlash('message_user', $successmsg);
            return $this->redirectToRoute('contact_home');
        }
       
        return $this->render('contact/index.html.twig', [
            'contactForm' => $form->createView(),
            'active' => 'contact',
        ]);
    }
}
