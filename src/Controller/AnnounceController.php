<?php

namespace App\Controller;

use App\Entity\Announce;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;


/**
 * @Route("/announce", name="announce_")
 */
class AnnounceController extends AbstractController
{

    /**
     * @Route("/list", name="list")
     */
    public function announcesList(Request $request, PaginatorInterface $paginator): Response
    {
       
        $data = $this->getDoctrine()->getRepository(Announce::class)->findAll();
        $announces = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            12
        );
    
        return $this->render('announce/index.html.twig', [
            'announces' => $announces,
            'active' => 'announce'
        ]);
    }
    
}
