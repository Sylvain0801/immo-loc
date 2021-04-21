<?php

namespace App\Controller;

use App\Form\PaymentFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class PaymentController extends AbstractController
{
    /**
     * @Route("/payment", name="payment")
     */
    public function index(Request $request): Response
    {
        $form = $this->createForm(PaymentFormType::class);
        $formData = $form->handleRequest($request);
        
        
        if($form->isSubmitted() && $form->isValid()) {

            $price = $formData->get('price')->getData();
            // Appel de l'autoloader pour avoir accès à stripe
            require_once dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';

            // Instanciation de stripe avec la clé privée
            \Stripe\Stripe::setApiKey('');

            // Création de l'intention de paiement et stockage dans $intent
            $intent = \Stripe\PaymentIntent::create([
                    'amount' => $price * 100, // Le prix doit être transmis en centimes
                    'currency' => 'eur',
            ]);
            
            return $this->render('payment/payment.html.twig', [
                'active' => 'myspace',
                'intent' => $intent
            ]);
        }
    
        return $this->render('payment/index.html.twig', [
            'active' => 'myspace',
            'paymentForm' => $form->createView()
        ]);
    }
    
    /**
     * @Route("/payment-success", name="payment_success")
     */
    public function paymentSuccess(TranslatorInterface $translator)
    {

        $successmsg = $translator->trans('Succesfull transaction');
        $this->addFlash('message_user', $successmsg);

        return $this->redirectToRoute('payment');
    }
    
}
