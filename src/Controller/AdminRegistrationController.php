<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Form\AdminRegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdminRegistrationController extends AbstractController
{
    /**
     * @Route("/admin/register", name="admin_app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, TranslatorInterface $translator): Response
    {
        $user = new Admin();
        $form = $this->createForm(AdminRegistrationFormType::class, $user);
        $form->handleRequest($request);

        $section = $translator->trans('administrators');

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setRoles(["ROLE_ADMIN"]);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email
            $message = $translator->trans('Administrator created succesfully');
            $this->addFlash('message_admin', $message);

            return $this->redirectToRoute('admin_useradmin_list');
        }

        return $this->render('registration/adminregister.html.twig', [
            'registrationForm' => $form->createView(),
            'createdAt' => null,
            'section' => $section,
            'active' => 'myspace'
        ]);
    }
}
