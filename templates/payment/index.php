{% extends 'base.html.twig' %}

{% block title %}Hello PaymentController!{% endblock %}

{% block body %}
<form action="paiement.php" method="post">
    <label for="prix">Prix</label>
    <input type="text" name="prix" id="prix">
    <button>Procéder au paiement</button>
</form>
{% endblock %}
