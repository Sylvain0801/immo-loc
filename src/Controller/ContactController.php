<?php

namespace App\Controller;

use App\Entity\Message;
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
        $form->handleRequest($request);

        
        if ($form->isSubmitted() && $form->isValid()) {

            // Donne accès au message à tous les agents
            foreach ($agents as $agent) {
                $message->addRecipient($agent);
            }

            $message->setMessageRead(0);
            
            $em = $this->getDoctrine()->getManager();
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
