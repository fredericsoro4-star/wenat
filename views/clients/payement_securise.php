<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Paiement Sécurisé - MonApp</title>
  <style>
    :root {
      --primary: #00b300; /* vert pur */
      --primary-dark: #009900;
      --text: #2d3436;
      --muted: #636e72;
      --bg: #f5f7f9;
      --radius: 14px;
      --max-width: 1000px;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background: var(--bg);
      color: var(--text);
      line-height: 1.6;
    }

    /* HEADER */
    header {
   
      display: flex;
      align-items: center;
      padding: 1rem 2rem;
     
    }

    .logo img {
      height: 70px;
      width: auto;
    }

    /* HERO */
    .hero {
      position: relative;
      width: 100%;
      height: 250px;
      display: flex;
      justify-content: center;
      align-items: center;
      text-align: center;
      color: #fff;
      background: url('assets/images/paiement.jpg') center center / cover no-repeat;
      overflow: hidden;
    }

    .hero::before {
      content: "";
      position: absolute;
      inset: 0;
      
    }

    .hero-content {
      position: relative;
      z-index: 1;
      text-align: center;
      padding: 1rem;
    }

    .hero h1 {
      font-size: 2.3rem;
      color:green;
      margin-bottom: 0.5rem;
      font-weight: 700;
    }

    .hero p {
       color:green;
      font-size: 1.1rem;
      opacity: 0.95;
    }

    /* MAIN CONTENT */
    main {
      max-width: var(--max-width);
      margin: -60px auto 3rem;
      background: white;
      border-radius: var(--radius);
      box-shadow: 0 8px 25px rgba(0,0,0,0.1);
      overflow: hidden;
      position: relative;
      z-index: 2;
      padding: 2rem;
    }

    .section {
      display: flex;
      align-items: flex-start;
      gap: 2rem;
      justify-content: space-between;
    }

    .section .text {
      flex: 1;
    }

    .section .image-right {
      flex: 0 0 300px; /* largeur fixe pour l'image */
    }

    .section .image-right img {
      width: 100%;
      height: auto;
      border-radius: var(--radius);
      object-fit: cover;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    h2 {
      color: var(--primary);
      margin-bottom: 1rem;
      font-size: 1.6rem;
    }

    p, li {
      margin-bottom: 1rem;
    }

    ul {
      margin-left: 1.5rem;
    }

    a.btn {
      display: inline-block;
      background: var(--primary);
      color: white;
      text-decoration: none;
      padding: 0.8rem 1.5rem;
      border-radius: 8px;
      font-weight: 600;
      transition: background 0.3s;
      margin-top: 1.5rem;
    }

    a.btn:hover {
      background: var(--primary-dark);
    }

    footer {
      text-align: center;
      color: var(--muted);
      font-size: 0.9rem;
      padding: 2rem 1rem;
    }

    /* RESPONSIVE */
    @media (max-width: 850px) {
      .section {
        flex-direction: column;
        text-align: center;
      }
      .section .image-right {
        flex: 1 1 100%;
      }
      .section .image-right img {
        max-width: 90%;
        margin: 0 auto;
      }
      .hero h1 {
        font-size: 1.8rem;
      }
    }
  </style>
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
      <h1>Paiement Sécurisé</h1>
      <p>Profitez d’un service fiable avec paiement en espèces à la réception.</p>
    </div>
  </section>

  <!-- CONTENU PRINCIPAL -->
  <main>
    <div class="section">
      <div class="text">
        <h2>Comment ça fonctionne ?</h2>
        <p>
          Pour garantir votre sécurité, toutes les commandes sont réglées <strong>en espèces lors de la livraison</strong>.
          Vous avez ainsi la possibilité de vérifier votre commande avant de payer.
        </p>

        <h2>Sécurité et confiance</h2>
        <ul>
          <li>Vous ne payez qu’une fois votre produit reçu et vérifié.</li>
          <li>Nos livreurs sont formés pour assurer un service professionnel et fiable.</li>
          <li>En cas d’anomalie, le produit est immédiatement pris en charge.</li>
        </ul>

        <h2>Vos avantages</h2>
        <ul>
          <li>Aucun risque de paiement en ligne.</li>
          <li>Transparence et contrôle total sur vos achats.</li>
          <li>Un service simple, rapide et 100 % sécurisé.</li>
        </ul>

        <a href="../../index.php" class="btn">Retour à l’accueil</a>
      </div>

      <!-- IMAGE À DROITE -->
      <div class="image-right">
        <img src="../../assets/images/payement.png" alt="Illustration paiement sécurisé">
      </div>
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
