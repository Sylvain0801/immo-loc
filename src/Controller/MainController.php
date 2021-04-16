<?php

namespace App\Controller;

use App\Repository\AnnounceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(AnnounceRepository $announceRepository)
    {

        $announces = $announceRepository->findBy([
                'active' => true,
                'firstpage' => true
            ],
            ['price' => 'ASC']
        );

        return $this->render('main/home/index.html.twig', [
            'active' => 'home',
            'announces' => $announces
        ]);

    }

    public function displayCookieBanner(Request $request): Response
    {
        
        $bannerCookie = false;

        $cookie = $request->cookies->get('accept-cookie');

        !$cookie ? $bannerCookie = true : $bannerCookie = false;

        return $this->render('include/_cookieBanner.html.twig', [
            'bannerCookie' => $bannerCookie
        ]);
    }

    /**
     * @Route("/accept", name="accept_cookie")
     */
    public function acceptCookie(Request $request):RedirectResponse
    {
        $cookie = new Cookie('accept-cookie', 'accept', strtotime('now') + 24 * 3600);
            
        $response = new Response();
        $response->headers->setcookie($cookie);
        $response->send();

       //redirige vers la page précédente
       return $this->redirect($request->headers->get('referer'));

    }

    /**
     * @Route("/refuse", name="refuse_cookie")
     */
    public function refuseCookie(Request $request):RedirectResponse
    {
        $cookie = new Cookie('accept-cookie', 'refuse', strtotime('now') + 24 * 3600);
            
        $response = new Response();
        $response->headers->setcookie($cookie);
        $response->send();

        //redirige vers la page précédente
        return $this->redirect($request->headers->get('referer'));

    }

    /**
     * @Route("/change-locale/{locale}", name="change_locale")
     */
    public function changeLocale($locale, Request $request)
    {
        // stocke la langue demandée dans la session
        $request->getSession()->set('_locale', $locale);

        //redirige vers la page précédente
        return $this->redirect($request->headers->get('referer'));
    }
}
