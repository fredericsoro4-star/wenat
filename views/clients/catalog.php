<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>wenat</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/styles/styles.css">
    <link rel="manifest" href="manifest.json">
    <!-- Favicon principal -->
    <link rel="icon" href="assets/images/image.png" type="image/x-icon">
    <link rel="icon" href="assets/images/image.png" type="image/png" sizes="32x32">
    <link rel="apple-touch-icon" href="assets/images/image.png">
    <link rel="stylesheet" href="assets/styles/styles.css">
</head>
<body>
  <?php include('assets/header/header.php'); ?>
    <?php include('assets/header/bande.php'); ?>
    <?php include('assets/header/mobile-bar.php'); ?>
  
  <div class="search-bar">
    <div class="search-container">
      <i class="fas fa-search search-icon"></i>
      <form method="GET" style="margin: 0;">
        <input type="hidden" name="action" value="catalog">
        <input type="text" name="search" placeholder="Rechercher un produit" 
               value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" 
               onchange="this.form.submit()">
      </form>
    </div>
  </div>


  <!-- SECTION VIDÉO GUIDE D'UTILISATEUR -->
 <section class="video-guide-section">
  <div class="video-guide-container">
    <h2 class="video-guide-title">
      <i class="fas fa-play-circle"></i> Guide d'Utilisation Wenat
    </h2>
    <div class="video-guide-layout">

      <!-- Carrousel gauche : descend -->
      <!-- <div class="vg-car vg-car-left">
        <div class="vg-track">
          <img src="assets/images/epice1.png" alt="lapin">
          <img src="assets/images/m1.jpg" alt="lapin">
          <img src="assets/images/lapin3.jpg" alt="lapin">
          <img src="assets/images/lapin4.jpg" alt="lapin">
        
          <img src="assets/images/lapin1.jpg" alt="lapin"> 
          <img src="assets/images/lapin2.jpg" alt="lapin">
          <img src="assets/images/lapin3.jpg" alt="lapin">
          <img src="assets/images/lapin4.jpg" alt="lapin">
        </div>
      </div>  -->

      <!-- Vidéo centrale -->
      <div class="video-wrapper">
        <video class="guide-video" id="guideVideo" controls>
          <source src="assets/video/video.mp4" type="video/mp4">
        </video>
        <div class="video-overlay" id="videoOverlay"
             onclick="this.style.display='none'; document.getElementById('guideVideo').play()">
          <div class="play-button">
            <i class="fas fa-play"></i>
          </div>
        </div>
      </div>

  
       <!-- <div class="vg-car vg-car-right">
        <div class="vg-track">
          <img src="assets/images/lapin5.jpg" alt="lapin">
          <img src="assets/images/lapin6.jpg" alt="lapin">
          <img src="assets/images/lapin7.jpg" alt="lapin">
          <img src="assets/images/lapin8.jpg" alt="lapin">
       
          <img src="assets/images/lapin5.jpg" alt="lapin">
          <img src="assets/images/lapin6.jpg" alt="lapin">
          <img src="assets/images/lapin7.jpg" alt="lapin">
          <img src="assets/images/lapin8.jpg" alt="lapin">
        </div> -->
      </div> 

    </div>
  </div>
</section>
  
  <!-- LAPINS -->
 <section class="lapins">
    <h2 class="section-title">
        <?= !empty($_GET['search']) ? 'Résultats de recherche' : 'Bienvenue sur Wenat' ?>
    </h2>

    <div class="cards">
        <?php if (empty($lapins_js)): ?>
            <div class="empty-state">
                <i class="fas fa-search"></i>
                <h3>Aucun produit trouvé</h3>
                <p>
                    <?= !empty($_GET['search'])
                        ? 'Aucun produit ne correspond à votre recherche.'
                        : 'Aucun produit disponible pour le moment.'
                    ?>
                </p>
                <?php if (!empty($_GET['search'])): ?>
                    <a href="?action=catalog" class="btn btn-primary" style="margin-top: 15px;">
                        <i class="fas fa-list"></i> Voir tous les lapins
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
          
          <?php foreach ($lapins_js as $lapin): ?>
<div class="card" data-type="<?= htmlspecialchars(strtolower($lapin['type'])) ?>">

    <?php if (!empty($lapin['promotion'])): ?>
        <span class="card-badge"><?= htmlspecialchars($lapin['promotion']) ?></span>
    <?php endif; ?>

    <div class="card-image-container">
        <img src="<?= htmlspecialchars($lapin['image'], ENT_QUOTES) ?>" alt="Lapin <?= htmlspecialchars($lapin['type']) ?>">
    </div>

    <div class="card-content">
        <h3><?= htmlspecialchars($lapin['type']) ?></h3>

        <p>
            <i class="fas fa-info-circle"></i>
         <a href="#"
          onclick='showDescription(<?= json_encode($lapin["description"]) ?>); return false;'
          style="text-decoration:none; color:inherit;">
          Voir la description
         </a>
        </p>

        <p>
            <i class="fas fa-weight"></i> 
            Poids : <?= htmlspecialchars($lapin['poids']) ?>
        </p>

        <p class="price">
            <i class="fas fa-tag"></i> 
            <?= htmlspecialchars($lapin['prixAffichage']) ?>
        </p>

        <div class="card-actions">
            <button class="btn btn-primary" onclick="addToCart(<?= $lapin['id'] ?>, event)">
                <i class="fas fa-shopping-cart"></i> Acheter
            </button>
        </div>
    </div>

</div>
      <?php endforeach; ?>
   <div id="descriptionModal" class="modal">
     <div class="modal-content">
     <span class="close" onclick="closeDescription()">&times;</span>
     <h3>Description </h3>
     <p id="modalText"></p>
     </div>
 </div>
        <?php endif; ?>  
    </div>
</section>

    <?php if (!empty($lapins_js)): ?>
      <p style="margin-top: 60px; color: var(--text); font-size: 16px; opacity: 0.8; text-align: center;">
        <i class="fas fa-info-circle"></i> 
        <?= count($lapins_js) ?> produit<?= count($lapins_js) > 1 ? 's' : '' ?> affiché<?= count($lapins_js) > 1 ? 's' : '' ?>
      </p>
    <?php endif; ?>
  </section>

  <!-- PANIER MODAL -->
  <div id="panier-overlay" onclick="togglePanier()"></div>
  <div id="panier-modal">
    <div class="panier-header">
      <h3><i class="fas fa-shopping-cart"></i> Votre Panier</h3>
      <button class="close-panier" onclick="togglePanier()">
        <i class="fas fa-times"></i>
      </button>
    </div>
    
    <div class="panier-content" id="panier-content">
      <div class="empty-state" style="padding: 60px 20px;">
        <i class="fas fa-shopping-cart" style="font-size: 50px; color: var(--primary-light);"></i>
        <p style="font-size: 16px; margin-top: 20px;">Votre panier est vide</p>
      </div>
    </div>
    
    <div class="panier-footer">
      <div class="panier-total">
        <span>Total :</span>
        <span id="panier-total">0,00 FCFA</span>
      </div>
      <button id="commander-btn" class="btn btn-primary" onclick="commander()">
        <i class="fas fa-check"></i> Commander
      </button>
    </div>
  </div>

  <!-- BOUTON DE COMMANDE RAPIDE FLOTTANT -->
   <a href="views/clients/commande_rapide.php">
    <button class="quick-order-btn" title="Commande rapide">
    <i class="fas fa-bolt"></i>
    <span class="btn-text">Commande rapide</span>
    <span class="quick-order-badge" id="quick-order-badge" style="display: none;">!</span>
    </button>
   </a>

    <?php include "assets/footer/footerbar.php";?>
    <?php include "assets/js/script.php";?>
    <script src="assets/js/script.js"></script>
  
</body>
</html>