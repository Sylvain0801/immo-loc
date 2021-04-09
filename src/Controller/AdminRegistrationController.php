<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Form\AdminRegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminRegistrationController extends AbstractController
{
    /**
     * @Route("/admin/register", name="admin_app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new Admin();
        $form = $this->createForm(AdminRegistrationFormType::class, $user);
        $form->handleRequest($request);

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
            $this->addFlash('message_admin', 'L\'administrateur a été créé avec succès');

            return $this->redirectToRoute('admin_useradmin_list');
        }

        return $this->render('registration/adminregister.html.twig', [
            'registrationForm' => $form->createView(),
            'createdAt' => null,
            'section' => 'administrateurs',
            'active' => 'myspace'
        ]);
    }
}
