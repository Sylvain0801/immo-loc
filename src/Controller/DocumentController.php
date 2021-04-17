<?php

namespace App\Controller;

use App\Entity\Document;
use App\Form\DocumentFormType;
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
    public function messageList($header, $sorting, Request $request, PaginatorInterface $paginator, TranslatorInterface $translator, DocumentRepository $documentRepository): Response
    {

        $name = $translator->trans('Name');
        $category = $translator->trans('Category');
        $owner = $translator->trans('Created by');
        $createdat = $translator->trans('Created at');

        $headers = [
            'name' => $name,
            'category' => $category,
            'owner' => $owner,
            'updatedAt' => $createdat
        ];

        $roles = $this->getUser()->getRoles();
        // si l'utilisateur connecté est un agent, il a accès à tous les documents
        if(in_array('ROLE_AGENT', $roles)) {
            $data = $documentRepository->findBy([], [$header => $sorting]);
        }

        // si c'est un autre profil, il a accès uniquement aux documents qui lui sont propres
        if(in_array('ROLE_OWNER', $roles) || in_array('ROLE_LEASEOWNER', $roles) || in_array('ROLE_TENANT', $roles)) {
            $data = $documentRepository->findBy(['owner' => $this->getUser()], [$header => $sorting]);
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
            
            $document->setOwner($this->getUser());

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
}
