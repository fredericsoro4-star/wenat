<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Livraison & Retour - MonApp</title>
  <link rel="stylesheet" href="../../assets/styles/livraison_retour.css">
</head>
<body>

  <!-- HEADER -->
  <header>
    <div class="logo">
      <img src="../../assets/images/image.png" alt="Logo de l'entreprise">
      
    </div>
  </header>

  <!-- HERO -->
  <section class="hero">
    <div class="hero-content">
      <h1>Livraison & Retour</h1>
      <p>Profitez d’un service de livraison rapide et fiable, et d’une politique de retour simple et transparente.</p>
    </div>
  </section>

  <!-- MAIN CONTENT -->
  <main>
    <h2>Nos Services</h2>

    <div class="card-section">
      <div class="card">
        <img src="../../assets/images/livreur.jpg" alt="Livraison rapide">
        <div class="card-content">
          <h3>Livraison Express</h3>
          <p>Nos agents assurent une livraison rapide, sécurisée et suivie, avec paiement à la réception.</p>
        </div>
      </div>

      <div class="card">
        <img src="../../assets/images/retour_colis.jpg" alt="Retour produit">
        <div class="card-content">
          <h3>Retour Simplifié</h3>
          <p>Signalez tout problème : un agent vérifie et procède à l’échange ou au remboursement selon le cas.</p>
        </div>
      </div>

      <div class="card">
        <img src="../../assets/images/assistance.jpg" alt="Service client">
        <div class="card-content">
          <h3>Assistance Client</h3>
          <p>Notre équipe vous accompagne à chaque étape pour une expérience fluide et sans souci.</p>
        </div>
      </div>
    </div>

    <div style="text-align:center;">
      <a href="../../index.php" class="btn">Retour à l'accueil</a>
    </div>
  </main>

  <!-- FOOTER -->
  <footer>
    <p>&copy; <span id="year"></span> MonApp. Tous droits réservés.</p>
  </footer>

  <script>
    document.getElementById("year").textContent = new Date().getFullYear();
  </script>

</body>
</html>
