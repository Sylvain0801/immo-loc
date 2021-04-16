<?php

namespace App\Controller\User;

use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tenant", name="tenant_")
 */
class TenantController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index(): Response
    {
        return $this->render('tenant/index.html.twig', [
            'active' => 'myspace',
        ]);
    }

    public function messageNotRead(MessageRepository $messageRepository)
    {
        $messageNotRead = $messageRepository->findMessageNotReadByUserID($this->getUser()->getId());

        return $this->render('dashboard/_messagenotread.html.twig', [
            'messageNotRead' => $messageNotRead
            ]);

    }
}