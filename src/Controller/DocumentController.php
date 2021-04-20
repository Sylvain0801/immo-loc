<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\AdminMessageRead;
use App\Entity\Document;
use App\Entity\Message;
use App\Form\DocumentFormType;
use App\Form\TransfertDocFormType;
use App\Repository\DocumentRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/document", name="document_")
 */
class DocumentController extends AbstractController
{
    /**
     * @Route("/list/{header}/{sorting}", name="list", defaults={"header": "updatedAt", "sorting": "DESC"})
     */
    public function documentList($header, $sorting, Request $request, PaginatorInterface $paginator, TranslatorInterface $translator, DocumentRepository $documentRepository): Response
    {

        $name = $translator->trans('Name');
        $category = $translator->trans('Category');
        $createdat = $translator->trans('Created at');

        $headers = [
            'id' => 'id',
            'name' => $name,
            'category' => $category,
            'updatedAt' => $createdat
        ];

        $roles = $this->getUser()->getRoles();
        // si l'utilisateur connecté est un agent, il a accès à tous les documents
        if($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_AGENT')) {
            $data = $documentRepository->findBy([], [$header => $sorting]);
        }

        // si c'est un autre profil, il a accès uniquement aux documents qui lui sont propres
        if(!$this->isGranted('ROLE_ADMIN') && ($this->isGranted('ROLE_OWNER') || $this->isGranted('ROLE_LEASEOWNER') || $this->isGranted('ROLE_TENANT'))) {
            $data = $documentRepository->findDocumentsByUser($this->getUser(), $header, $sorting);
        }

        $documents = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            7
        );
        return $this->render('document/index.html.twig', [
            'documents' => $documents,
            'headers' => $headers,
            'section' => 'documents',
            'active' => 'myspace'
        ]);
    }

    /**
     * @Route("/new", name="new")
     */
    public function documentNew(Request $request, TranslatorInterface $translator): Response
    {
        $document = new Document();
        $form = $this->createForm(DocumentFormType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $document->addDocUserAccess($this->getUser());

            $docName = $form->get('files')->getData();
             
            $file = md5(uniqid()).'.'.$docName->guessExtension();
            
            $docName->move($this->getParameter('documents_directory'), $file);
            
            $document->setDocumentName($file);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($document);
            $em->flush();

            $successmsg = $translator->trans('Your document has been saved successfully');
            $this->addFlash('message_user', $successmsg);

            return $this->redirectToRoute('document_new');

        }

        return $this->render('document/new.html.twig', [
            'active' => 'myspace',
            'section' => 'documents',
            'documentForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/transfert/{id}", name="transfert")
     */
    public function documentTransfert(Request $request, Document $document, TranslatorInterface $translator): Response
    {

        $form = $this->createForm(TransfertDocFormType::class, $document);
        $dataForm = $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $recipients = $dataForm->get('recipient')->getData();
            if($recipients) {
                foreach($recipients as $recipient) {
                    $document->addDocUserAccess($recipient);
                }
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($document);
            $em->flush();

            $successmsg = $translator->trans('Your document has been sent successfully');
            $this->addFlash('message_user', $successmsg);

            return $this->redirectToRoute('document_list');

        }

        return $this->render('document/transfert.html.twig', [
            'active' => 'myspace',
            'section' => 'documents',
            'transfertDocForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function documentDelete(Document $document, TranslatorInterface $translator)
    {
        if(!$this->isGranted('ROLE_ADMIN') && $this->isGranted('ROLE_AGENT')) {

            $subject = $translator->trans('Request for document deletion');
            $body = $translator->trans('Hello, thanks for deleting the document with id:');

            $em = $this->getDoctrine()->getManager();

            $message = new Message();
            $message->setSenderUser($this->getUser());
            $message->setSender($this->getUser()->getId());
            $message->setSubject($subject);
            $message->setBody($body.' '.$document->getId());

            $admins = $this->getDoctrine()->getRepository(Admin::class)->findAll();

            foreach($admins as $admin) {
                $messageRead = new AdminMessageRead();
                $messageRead->setAdmin($admin);
                $messageRead->setMessage($message);
                $messageRead->setNotRead(1);

                $em->persist($messageRead);
            }
            
            $em->persist($message);
            $em->flush();

            $successmsg = $translator->trans('Your request has been sent successfully');
            $this->addFlash('message_user', $successmsg);

            return $this->redirectToRoute('document_list');
        }

        if($this->isGranted('ROLE_ADMIN')) {

            $em = $this->getDoctrine()->getManager();
            $em->remove($document);
            $em->flush();

            $successmsg = $translator->trans('The document has been deleted successfully');
            $this->addFlash('message_user', $successmsg);

            return $this->redirectToRoute('document_list');
        }
    }
}
