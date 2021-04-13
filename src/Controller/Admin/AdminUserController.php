<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserEditType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/admin/user", name="admin_user_")
 */
class AdminUserController extends AbstractController
{

    /**
     * @Route("/list/{header}/{sorting}", name="list", defaults={"header": "id", "sorting": "ASC"})
     */
    public function usersList($header, $sorting, Request $request, PaginatorInterface $paginator, TranslatorInterface $translator): Response
    {
        $section = $translator->trans('users');
        $firstname = $translator->trans('Firstname');
        $lastname = $translator->trans('Lastname');

        $headers = [
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => 'Email'
        ];
        $data = $this->getDoctrine()->getRepository(User::class)->findBy([], [$header => $sorting]);
        $users = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            7
        );
    
        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
            'headers' => $headers,
            'section' => $section,
            'active' => 'myspace'
        ]);
    }

    /**
    * @Route("/edit/{id}", name="edit")
    */   
    public function editUser(User $user, Request $request, TranslatorInterface $translator): Response
    {

        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);

        $section = $translator->trans('users');

        if ($form->isSubmitted() && $form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $message = $translator->trans('User modified succesfully');
            $this->addFlash('message_admin', $message);

            return $this->redirectToRoute('admin_user_list');
        }

        return $this->render('admin/user/edit.html.twig', [
            'userEditForm' => $form->createView(),
            'createdAt' => $user->getCreatedAt(),
            'section' => $section,
            'active' => 'myspace'
        ]);
    }
    /**
    * @Route("/delete/{id}", name="delete")
    */   
    public function deleteUser(User $user, TranslatorInterface $translator): RedirectResponse
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();
        
        $message = $translator->trans('User deleted succesfully');
        $this->addFlash('message_admin', $message);

    return $this->redirectToRoute('admin_user_list');
    }
}
