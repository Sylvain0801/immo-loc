<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserEditType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;


/**
 * @Route("/admin/user", name="admin_user_")
 */
class AdminUserController extends AbstractController
{

    /**
     * @Route("/list/{header}/{sorting}", name="list", defaults={"header": "id", "sorting": "ASC"})
     */
    public function usersList($header, $sorting, Request $request, PaginatorInterface $paginator): Response
    {
        $headers = [
            'firstname' => 'Prénom',
            'lastname' => 'Nom',
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
            'section' => 'utilisateurs',
            'active' => 'myspace'
        ]);
    }

    /**
    * @Route("/edit/{id}", name="edit")
    */   
    public function editUser(User $user, Request $request): Response
    {

        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('message_admin', 'L\'utilisateur a été modifié avec succès');

            return $this->redirectToRoute('admin_user_list');
        }

        return $this->render('admin/user/edit.html.twig', [
            'userEditForm' => $form->createView(),
            'createdAt' => $user->getCreatedAt(),
            'section' => 'utilisateurs',
            'active' => 'myspace'
        ]);
    }
    /**
    * @Route("/delete/{id}", name="delete")
    */   
    public function deleteUser(User $user): RedirectResponse
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();
        
        $this->addFlash('message_admin', 'L\'utilisateur a été supprimé avec succès');

    return $this->redirectToRoute('admin_user_list');
    }
}
