<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Conditions Générales - MonApp</title>
<link rel="stylesheet" href="../../assets/styles/condition_generale.css">
    
</head>
<body>

  <!-- Header -->
  <header>
    <div class="logo">
      <img src="../../assets/images/image.png" alt="Logo de l'entreprise">
    </div>
  </header>

  <!-- HERO -->
  <section class="hero">
    <div class="hero-content">
      <h1>Conditions Générales de Vente</h1>
      <p>Découvrez nos engagements et votre sécurité lors de chaque commande.</p>
    </div>
  </section>

  <!-- CONTENU PRINCIPAL AVEC IMAGE À DROITE -->
  <main>
    <div class="section">
      <div class="text">
        <h2>1. Inscription</h2>
        <p>Pour effectuer une commande, le client doit s'inscrire sur le site. L'inscription permet de suivre la commande et d'assurer une communication efficace.</p>

        <h2>2. Commande</h2>
        <p>Une fois inscrit, le client peut passer sa commande sur la plateforme. Un récapitulatif est affiché avant validation.</p>
        <ul>
          <li>Les informations fournies doivent être exactes.</li>
          <li>Le client reçoit une confirmation par e-mail ou SMS.</li>
          <li>Le client peut suivre l’état de sa commande depuis son espace personnel.</li>
        </ul>

        <h2>3. Paiement</h2>
        <p>Le paiement s'effectue exclusivement <strong>en espèces à la livraison</strong>. Le client règle le montant directement au livreur lors de la réception du colis.</p>
        <ul>
          <li>Le livreur remet le colis uniquement après paiement complet.</li>
          <li>Aucune avance n’est demandée.</li>
          <li>En cas d’absence, la livraison peut être reprogrammée.</li>
        </ul>

        <h2>4. Livraison</h2>
        <p>La livraison est effectuée par nos agents ou partenaires dans les meilleurs délais. Vérifiez l’état du colis à la réception.</p>

        <h2>5. Service client</h2>
        <p>Pour toute question ou réclamation, contactez notre support via la page <a href="assistance.php">Signaler un problème</a> ou par e-mail à <strong>support@monapp.com</strong>.</p>

        <a href="../../index.php" class="btn">Retour à l'accueil</a>
      </div>

      <!-- Image réduite et placée à droite -->
      <img src="../../assets/images/condition_generale.jpg" alt="Illustration des conditions générales">
    </div>
  </main>

  <footer>
    <p>&copy; <span id="year"></span> MonApp. Tous droits réservés.</p>
  </footer>

  <script>
    document.getElementById("year").textContent = new Date().getFullYear();
  </script>

</body>
</html>
