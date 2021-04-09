<?php

namespace App\Controller;

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
    public function index()
    {

        return $this->render('main/home/index.html.twig', [
            'active' => 'home'
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
    public function acceptCookie():RedirectResponse
    {
        $cookie = new Cookie('accept-cookie', 'accept', strtotime('now') + 24 * 3600);
            
        $response = new Response();
        $response->headers->setcookie($cookie);
        $response->send();

        return $this->redirectToRoute('home');

    }

    /**
     * @Route("/refuse", name="refuse_cookie")
     */
    public function refuseCookie():RedirectResponse
    {
        $cookie = new Cookie('accept-cookie', 'refuse', strtotime('now') + 24 * 3600);
            
        $response = new Response();
        $response->headers->setcookie($cookie);
        $response->send();

        return $this->redirectToRoute('home');

    }
}
