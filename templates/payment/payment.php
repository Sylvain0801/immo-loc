<?php
if(isset($_POST['prix']) && !empty($_POST['prix'])){
    // Nous appelons l'autoloader pour avoir accès à Stripe
    require_once('vendor/autoload.php');

    // Nous instancions Stripe en indiquand la clé privée, pour prouver que nous sommes bien à l'origine de cette demande
    \Stripe\Stripe::setApiKey('VOTRE_CLE_PRIVEE_STRIPE');

    // Nous créons l'intention de paiement et stockons la réponse dans la variable $intent
    $intent = \Stripe\PaymentIntent::create([
        'amount' => $_POST['prix']*100, // Le prix doit être transmis en centimes
        'currency' => 'eur',
    ]);
}
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Paiement</title>

    </head>
    <body>

        <script src="js/scripts.js"></script>
    </body>
</html>