<?php

namespace App\Controller;

use App\Entity\Announce;
use App\Entity\Image;
use App\Form\AnnounceFormType;
use App\Form\SearchAnnounceType;
use App\Repository\AnnounceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/announce", name="announce_")
 */
class AnnounceController extends AbstractController
{

    /**
     * @Route("/home", name="home")
     */
    public function index(AnnounceRepository $announceRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $data = $announceRepository->findBy(['active' => true]);
        $form = $this->createForm(SearchAnnounceType::class);
        $search = $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $city = null;
            if($search->get('city')->getData()) {
                $city = $search->get('city')->getData()->getCity();
            }
            
            $data = $announceRepository->searchAnnounceByWordsCriteria(
                $search->get('words')->getData(),
                $search->get('type')->getData(),
                $city,
                $search->get('priceMin')->getData(),
                $search->get('priceMax')->getData(),
                $search->get('areaMin')->getData(),
                $search->get('areaMax')->getData(),
                $search->get('roomsMin')->getData(),
                $search->get('roomsMax')->getData(),
                $search->get('bedroomsMin')->getData(),
                $search->get('bedroomsMax')->getData()
    
            );    

        }
        
        $announces = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            12
        );
    
        return $this->render('announce/index.html.twig', [
            'announces' => $announces,
            'searchAnnounceForm' => $form->createView(),
            'active' => 'announce'
        ]);
    }
    
    /**
     * @Route("/list/{header}/{sorting}", name="list", defaults={"header": "id", "sorting": "ASC"})
     */
    public function announceList($header, $sorting, Request $request, TranslatorInterface $translator, PaginatorInterface $paginator): Response
    {
        // gestion des accès
        if(!$this->isGranted('ROLE_AGENT') && !$this->isGranted('ROLE_OWNER') && !$this->isGranted('ROLE_LEASEOWNER')){
            $messageAccessDeny = $translator->trans('Not privileged to request the resource.');
            throw $this->createAccessDeniedException($messageAccessDeny);
        }

        $section = $translator->trans('properties');
        $title = $translator->trans('title');
        $city = $translator->trans('city');
        $price = $translator->trans('price');
        $tenant = $translator->trans('tenant');
        $firstpage = $translator->trans('firstpage');

        $headers = [
            'title' => $title,
            'city' => $city,
            'price' => $price,
            'active' => 'active',
            'firstpage' => $firstpage,
            'tenant' => $tenant
        ];

        $data = $this->getDoctrine()->getRepository(Announce::class)->findBy([], [$header => $sorting]);
        $announces = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            7
        );

        return $this->render('announce/list.html.twig', [
            'active' => 'announce',
            'section' => $section,
            'headers' => $headers,
            'announces' => $announces
        ]);
    }

    /**
     * @Route("/new", name="new")
     */
    public function announceNew(Request $request, TranslatorInterface $translator):Response
    {
        // gestion des accès
        if(!$this->isGranted('ROLE_AGENT') && !$this->isGranted('ROLE_LEASEOWNER')){
            $messageAccessDeny = $translator->trans('Not privileged to request the resource.');
            throw $this->createAccessDeniedException($messageAccessDeny);
        }

        $announce = new Announce();
        $form = $this->createForm(AnnounceFormType::class, $announce);
        $form->handleRequest($request);

        $section = $translator->trans('properties');

        if($form->isSubmitted() && $form->isValid()){

            $images = $form->get('images')->getData();
            
            foreach($images as $image){
                $file = md5(uniqid()).'.'.$image->guessExtension();
                
                $image->move($this->getParameter('images_directory'), $file);
                
                $img = new Image();
                $img->setImage($file);
                $announce->addImage($img);
            }

            $announce->setCreatedBy($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($announce);
            $em->flush();

            $successmsg = $translator->trans('Property created succesfully');
            $this->addFlash('announce_message', $successmsg);

            return $this->redirectToRoute('announce_list');
        }
        
        return $this->render('announce/new.html.twig', [
            'active' => 'myspace',
            'section' => $section,
            'announceForm' => $form->createView(),
            'images' => null
        ]);
    }
    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function announceEdit(Announce $announce, Request $request, TranslatorInterface $translator):Response
    {
        // gestion des accès
        if(!$this->isGranted('ROLE_AGENT') && !$this->isGranted('ROLE_LEASEOWNER')){
            $messageAccessDeny = $translator->trans('Not privileged to request the resource.');
            throw $this->createAccessDeniedException($messageAccessDeny);
        }

        $form = $this->createForm(AnnounceFormType::class, $announce);
        $form->handleRequest($request);
        $imgview = $announce->getImages();

        $section = $translator->trans('properties');

        if($form->isSubmitted() && $form->isValid()){

            $images = $form->get('images')->getData();
            
            foreach($images as $image){
                $file = md5(uniqid()).'.'.$image->guessExtension();
                
                $image->move($this->getParameter('images_directory'), $file);
                
                $img = new Image();
                $img->setImage($file);
                $announce->addImage($img);
            }

            $announce->setCreatedBy($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($announce);
            $em->flush();

            $successmsg = $translator->trans('Property modified succesfully');
            $this->addFlash('announce_message', $successmsg);

            return $this->redirectToRoute('announce_list');
        }
        
        return $this->render('announce/edit.html.twig', [
            'active' => 'myspace',
            'section' => $section,
            'announceForm' => $form->createView(),
            'images' => $imgview
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function announceDelete(Announce $announce):Response
    {

        $em= $this->getDoctrine()->getManager();
        $em->remove($announce);
        $em->flush();

        return new Response('true');
    }

    /**
     * @Route("/active/{id}", name="active")
     */
    public function activer(Announce $announce):Response
    {
        $announce->setActive( $announce->getActive() ? false : true );

        $em = $this->getDoctrine()->getManager();
        $em->persist($announce);
        $em->flush();

        return new Response("true");

    }
    
    /**
     * @Route("/firstpage/{id}", name="firstpage")
     */
    public function firstpage(Announce $announce):Response
    {
        $announce->setFirstpage( $announce->getFirstpage() ? false : true );

        $em = $this->getDoctrine()->getManager();
        $em->persist($announce);
        $em->flush();

        return new Response("true");

    }

}
