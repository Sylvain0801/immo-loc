<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\AdminMessageRead;
use App\Entity\Message;
use App\Entity\User;
use App\Form\UserRegistrationFormType;
use App\Security\EmailVerifier;
use App\Security\AppAuthenticator;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class UserRegistrationController extends AbstractController
{
    private $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, AppAuthenticator $authenticator, TranslatorInterface $translator): Response
    {
        $user = new User();
        $form = $this->createForm(UserRegistrationFormType::class, $user);
        $contact = $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);

            // demande admin modif profil
            if($contact->get('roles')->getData()) {
                $subject = $translator->trans('New request for profile change : ');
                $body = $translator->trans('Hello, thanks for taking my request into account.');
                $admins = $this->getDoctrine()->getRepository(Admin::class)->findAll();
                $message = new Message();
                $message->setFirstnameSender($contact->get('firstname')->getData());
                $message->setlastnameSender($contact->get('lastname')->getData());
                $message->setEmailSender($contact->get('email')->getData());
                $message->setSender($contact->get('email')->getData());
                $message->setSubject($subject.$contact->get('roles')->getData());
                $message->setBody($body);

                foreach($admins as $admin) {
                    
                    $message->addAdminRecipient($admin);

                    $messageRead = new AdminMessageRead();
                    $messageRead->setAdmin($admin);
                    $messageRead->setMessage($message);
                    $messageRead->setNotRead(1);
    
                    $em->persist($messageRead);
                }

                $em->persist($message);
                
            }

            $em->flush();

            // generate a signed url and email it to the user
            $subject = $translator->trans('Please Confirm your Email');
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('no-reply@locationimmo.fr', 'LocationImmo'))
                    ->to($user->getEmail())
                    ->subject($subject)
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('registration/userregister.html.twig', [
            'registrationForm' => $form->createView(),
            'active' => 'myspace'
        ]);
    }

    /**
     * @Route("/verify/email", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('home');
    }
}
