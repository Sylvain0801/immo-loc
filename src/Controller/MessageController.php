<?php

namespace App\Controller;

use App\Repository\MessageRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/message", name="message_")
 */
class MessageController extends AbstractController
{
    /**
     * @Route("/agent/list/{header}/{sorting}", name="agent_list", defaults={"header": "created_at", "sorting": "DESC"})
     */
    public function messageList($header, $sorting, Request $request, PaginatorInterface $paginator, TranslatorInterface $translator, MessageRepository $messageRepository): Response
    {

        $section = $translator->trans('messages');
        $firstname = $translator->trans('Firstname');
        $lastname = $translator->trans('Lastname');
        $subject =$translator->trans('Subject');
        $createdat = $translator->trans('Sent at');
        $read = $translator->trans('Read');

        $headers = [
            'message_read' => $read,
            'firstname_sender' => $firstname,
            'lastname_sender' => $lastname,
            'subject' => $subject,
            'created_at' => $createdat
        ];
        $data = $messageRepository->findMessagesByRecipientRole('ROLE_AGENT', $header, $sorting);
        $messages = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            7
        );
        return $this->render('agent/message/index.html.twig', [
            'messages' => $messages,
            'headers' => $headers,
            'section' => $section,
            'active' => 'myspace'
        ]);
    }

}
