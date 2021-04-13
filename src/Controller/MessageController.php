<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Form\MessageFormType;
use App\Repository\MessageRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/message", name="message_")
 */
class MessageController extends AbstractController
{
    /**
     * @Route("/list/{header}/{sorting}", name="list", defaults={"header": "created_at", "sorting": "DESC"})
     */
    public function messageList($header, $sorting, Request $request, PaginatorInterface $paginator, TranslatorInterface $translator, MessageRepository $messageRepository): Response
    {

        $section = $translator->trans('messages');
        $firstname = $translator->trans('Firstname');
        $lastname = $translator->trans('Lastname');
        $subject = $translator->trans('Subject');
        $createdat = $translator->trans('Sent at');
        $read = $translator->trans('Read');

        $headers = [
            'message_read' => $read,
            'firstname_sender' => $firstname,
            'lastname_sender' => $lastname,
            'subject' => $subject,
            'created_at' => $createdat
        ];

        $roles = $this->getUser()->getRoles();
        // si l'utilisateur connecté est un agent, il a accès à tous les messages des agents
        if(in_array('ROLE_AGENT', $roles)) {
            $role = 'ROLE_AGENT';
            $data = $messageRepository->findMessagesByRecipientRole($role, $header, $sorting);
        }

        // si c'est un autre profil, il a accès uniquement aux messages qui lui sont propres
        if(in_array('ROLE_OWNER', $roles) || in_array('ROLE_LEASEOWNER', $roles) || in_array('ROLE_TENANT', $roles)) {
            $data = $messageRepository->findMessagesByUser($this->getUser()->getId(), $header, $sorting);
        }

        $messages = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            7
        );
        return $this->render('message/index.html.twig', [
            'messages' => $messages,
            'headers' => $headers,
            'section' => $section,
            'active' => 'myspace'
        ]);
    }

    /**
     * @Route("/read/{id}", name="read")
     */
    public function messageRead(Message $message):Response
    {
        $message->setMessageRead($message->getMessageRead() ? false : true);

        $em= $this->getDoctrine()->getManager();
        $em->persist($message);
        $em->flush();

        return new Response('true');
    }

    /**
     * @Route("/new", name="new")
     */
    public function messageNew(Request $request, TranslatorInterface $translator, MailerInterface $mailer):Response
    {

        $section = $translator->trans('messages');
        $sender = $request->request->get('sender');

        $message = new Message();
        $form = $this->createForm(MessageFormType::class, $message);
        $contact = $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            // Teste si l'expéditeur fait partie de la BDD user
            $senderInternal = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $sender]);

            if(!$senderInternal && $sender) {
                $email = (new TemplatedEmail())
                        ->from(new Address('no-reply@locationimmo.fr', 'LocationImmo'))
                        ->to($sender)
                        ->subject($contact->get('subject')->getData())
                        ->htmlTemplate('contact/email.html.twig')
                        ->context([
                            'firstname' => $this->getUser()->getFirstname(),
                            'lastname' => $this->getUser()->getLastname(),
                            'mail' => $this->getUser()->getUsername(),
                            'subject' => $contact->get('subject')->getData(),
                            'message' => $contact->get('body')->getData()
                        ]);
                $mailer->send($email);

            }
            
            if($senderInternal) { $message->addRecipient($senderInternal);}

            $recipients = $contact->get('recipient')->getData();
            if($recipients) {
                foreach ($recipients as $recipient) {
                    $message->addRecipient($recipient);
                }  
            }

            if($senderInternal || $recipients) {

                $message->setFirstnameSender($this->getUser()->getFirstname());
                $message->setLastnameSender($this->getUser()->getLastname());
                $message->setSender($this->getUser()->getUsername());
                $message->setMessageRead(0);
                
                $em= $this->getDoctrine()->getManager();
                $em->persist($message);
                $em->flush();
                
            }

            $successmsg = $translator->trans('Your message has been sent successfully');
            $this->addFlash('message_user', $successmsg);
            
            return $this->redirectToRoute('message_new');
            
        } 

        return $this->render('message/new.html.twig', [
            'messageForm' => $form->createView(),
            'sender' => $sender,
            'section' => $section,
            'active' => 'myspace'
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function messageEdit(Message $message, TranslatorInterface $translator):Response
    {
        $section = $translator->trans('messages');

        $message->setMessageRead(1);

        $em= $this->getDoctrine()->getManager();
        $em->persist($message);
        $em->flush();

        return $this->render('message/edit.html.twig', [
            'message' => $message,
            'section' => $section,
            'active' => 'myspace'
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function messageDelete(Message $message):Response
    {

        $em= $this->getDoctrine()->getManager();
        $em->remove($message);
        $em->flush();

        return new Response('true');
    }
}
