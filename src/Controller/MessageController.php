<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\AdminMessageRead;
use App\Entity\Message;
use App\Entity\MessageRead;
use App\Entity\User;
use App\Form\MessageFormType;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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

        $this->denyAccessUnlessGranted('ROLE_USER');

        $section = $translator->trans('messages');
        $sender = $translator->trans('Sender');
        $subject = $translator->trans('Subject');
        $createdat = $translator->trans('Sent at');
        $read = $translator->trans('Read');

        $headers = [
            'messageReads' => $read,
            'sender' => $sender,
            'subject' => $subject,
            'created_at' => $createdat
        ];

        if($this->IsGranted('ROLE_ADMIN')) {
            $data = $messageRepository->findMessagesByAdmin($this->getUser(), $header, $sorting);
        } else {
            $data = $messageRepository->findMessagesByUser($this->getUser(), $header, $sorting);
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
        if($this->isGranted('ROLE_ADMIN')) {
            $messageRead = $this->getDoctrine()->getRepository(AdminMessageRead::class)->findOneBy([
                'admin' => $this->getUser(),
                'message' => $message
            ]);
        } else {
            $messageRead = $this->getDoctrine()->getRepository(MessageRead::class)->findOneBy([
                'user' => $this->getUser(),
                'message' => $message
            ]);
        }

        $messageRead->setNotRead($messageRead->getNotRead() ? false : true);

        $em= $this->getDoctrine()->getManager();
        $em->persist($messageRead);
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
            $senderUser = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $sender]);
            
            // Teste si l'expéditeur fait partie de la BDD admin
            $senderAdmin = $this ->getDoctrine()->getRepository(Admin::class)->findOneBy(['email' => $sender]);

            if(!$senderUser && !$senderAdmin && $sender) {
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
            
            $em= $this->getDoctrine()->getManager();

            if($senderUser) { $message->addRecipient($senderUser);}
            if($senderAdmin) { $message->addAdminRecipient($senderAdmin);}

            $recipients = $contact->get('recipient')->getData();
            if($recipients) {
                foreach($recipients as $recipient) {
                    
                    $messageRead = new MessageRead();
                    $messageRead->setUser($recipient);
                    $messageRead->setMessage($message);
                    $messageRead->setNotRead(1);
    
                    $em->persist($messageRead);
                }
            }

            $adminRecipients = $contact->get('adminrecipient')->getData();
            if($adminRecipients) {
                foreach($adminRecipients as $adminRecipient) {
                    
                    $messageRead = new AdminMessageRead();
                    $messageRead->setAdmin($adminRecipient);
                    $messageRead->setMessage($message);
                    $messageRead->setNotRead(1);
    
                    $em->persist($messageRead);
                }
            }

            if($this->isGranted('ROLE_ADMIN')) {
                $message->setSenderAdmin($this->getUser());
            } else {
                $message->setSenderUser($this->getUser());
            }
            $message->setSender($this->getUser()->getUserName());

            $em->persist($message);
            $em->flush();
                
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

        if($this->isGranted('ROLE_ADMIN')) {
            $messageRead = $this->getDoctrine()->getRepository(AdminMessageRead::class)->findOneBy([
                'admin' => $this->getUser(),
                'message' => $message
                ]);
        } else {
            $messageRead = $this->getDoctrine()->getRepository(MessageRead::class)->findOneBy([
                'user' => $this->getUser(),
                'message' => $message
            ]);
        }

        $messageRead->setNotRead(0);

        $em= $this->getDoctrine()->getManager();
        $em->persist($messageRead);
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
        
        if($this->isGranted('ROLE_ADMIN')) {
            $message->removeAdminRecipient($this->getUser());
            $messageRead = $this->getDoctrine()->getRepository(AdminMessageRead::class)->findOneBy([
                'admin' => $this->getUser(),
                'message' => $message
                ]);
        } else {
            $message->removeRecipient($this->getUser());
            $messageRead = $this->getDoctrine()->getRepository(MessageRead::class)->findOneBy([
                'user' => $this->getUser(),
                'message' => $message
            ]);
        }

        $em= $this->getDoctrine()->getManager();
        $em->persist($message);
        $em->remove($messageRead);
        $em->flush();

        return new Response('true');
    }

    
    public function messageNotRead()
    {

        $messageNotRead = $this->getDoctrine()->getRepository(MessageRead::class)->findBy([
            'user' => $this->getUser(),
            'not_read' => true
        ]);

        return $this->render('dashboard/_messagenotread.html.twig', [
            'messageNotRead' => $messageNotRead
            ]);

    }

    public function adminMessageNotRead()
    {

        $messageNotRead = $this->getDoctrine()->getRepository(AdminMessageRead::class)->findBy([
            'admin' => $this->getUser(),
            'not_read' => true
        ]);

        return $this->render('dashboard/_messagenotread.html.twig', [
            'messageNotRead' => $messageNotRead
            ]);

    }

    /**
     * @Route("/contactus", name="contactus")
     */
    public function contacUs(Request $request, TranslatorInterface $translator, UserRepository $userRepository):Response
    {

        $section = $translator->trans('messages');

        $message = new Message();
        $form = $this->createForm(MessageFormType::class, $message);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $agents = $userRepository->findUsersByRole("ROLE_AGENT");
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

            $message->setSenderUser($this->getUser());

            $em->persist($message);
            $em->flush();
                

            $successmsg = $translator->trans('Your message has been sent successfully');
            $this->addFlash('message_user', $successmsg);
            
            return $this->redirectToRoute('message_list');
            
        } 

        return $this->render('message/contactus.html.twig', [
            'messageForm' => $form->createView(),
            'section' => $section,
            'active' => 'myspace'
        ]);
    }
    /**
     * @Route("/contact-tenant/{id}", name="contact_tenant")
     */
    public function contactTenant(Request $request, User $tenant, TranslatorInterface $translator):Response
    {

        $section = $translator->trans('messages');

        $message = new Message();
        $form = $this->createForm(MessageFormType::class, $message);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            
            $message->addRecipient($tenant);

            $messageRead = new MessageRead();
            $messageRead->setUser($tenant);
            $messageRead->setMessage($message);
            $messageRead->setNotRead(1);

            $em->persist($messageRead);

            $message->setSenderUser($this->getUser());
            $message->setSender($this->getUser()->getUsername());

            $em->persist($message);
            $em->flush();
                

            $successmsg = $translator->trans('Your message has been sent successfully');
            $this->addFlash('message_user', $successmsg);
            
            if(!$this->isGranted('ROLE_ADMIN') && $this->isGranted('ROLE_OWNER')) {
                return $this->redirectToRoute('announce_ownerview');
            } else {
                return $this->redirectToRoute('announce_list');
            }
            
        } 

        return $this->render('message/tenant.html.twig', [
            'messageForm' => $form->createView(),
            'recipient' => $tenant->getFirstname().' '.$tenant->getLastname().' '.$tenant->getUsername(),
            'section' => $section,
            'active' => 'myspace'
        ]);
    }
}
