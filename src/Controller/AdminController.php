<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Form\AdminRegistrationFormType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/admin", name="admin_")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index(): Response
    {
        return $this->render('dashboard/index.html.twig', [
            'active' => 'myspace'
        ]);
    }

    /**
     * @Route("/useradmin/list/{header}/{sorting}", name="useradmin_list", defaults={"header": "id", "sorting": "ASC"})
     */
    public function usersAdminList($header, $sorting, Request $request, PaginatorInterface $paginator, TranslatorInterface $translator): Response
    {
        $section = $translator->trans('administrators');
        $firstname = $translator->trans('Firstname');
        $lastname = $translator->trans('Lastname');

        $headers = [
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => 'Email'
        ];
        $data = $this->getDoctrine()->getRepository(Admin::class)->findBy([], [$header => $sorting]);
        $adminusers = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            7
        );
        return $this->render('admin/admin-user/index.html.twig', [
            'adminusers' => $adminusers,
            'headers' => $headers,
            'section' => $section,
            'active' => 'myspace'
        ]);
    }

    /**
    * @Route("/useradmin/edit/{id}", name="useradmin_edit")
    */
    public function editUser(Admin $admin, Request $request, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(AdminRegistrationFormType::class, $admin);
        $form->handleRequest($request);

        $section = $translator->trans('administrators');

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($admin);
            $em->flush();

            $message = $translator->trans('Administrator modified succesfully');

            $this->addFlash('message_admin', $message);
            return $this->redirectToRoute('admin_useradmin_list');
        }
        return $this->render('admin/admin-user/edit.html.twig', [
            'registrationForm' => $form->createView(),
            'createdAt' => $admin->getCreatedAt(),
            'section' => $section,
            'active' => 'myspace'
        ]);
    }
    
    /**
    * @Route("/useradmin/delete/{id}", name="useradmin_delete")
    */   
    public function delete(Admin $admin, TranslatorInterface $translator): RedirectResponse
    {

        $em = $this->getDoctrine()->getManager();
        $em->remove($admin);
        $em->flush();
        
        $message = $translator->trans('Administrator deleted succesfully');

        $this->addFlash('message_admin', $message);

    return $this->redirectToRoute('admin_useradmin_list');
    }

}

