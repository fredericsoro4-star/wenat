  <div class="mobile-menu-overlay" id="mobile-menu-overlay"></div>
  <div class="mobile-menu" id="mobile-menu">
   <div class="page-title-container">
    <div class="mobile-param">
    <a href="views/clients/paramettre.php">
        <img src="assets/images/paramettre (1).jpg" alt="Avatar utilisateur">
    </a>
  </div>
  <h1 class="page-title">Mon compte</h1>
</div>
    <?php if (isset($_SESSION['user'])): ?>
      <div class="mobile-user">
        <div class="mobile-user-avatar">
           <img src="assets/images/user.png" alt="Avatar utilisateur">
        </div>
        
        <div class="mobile-user-name">
    <i class="fas fa-user"></i> Nom: <?= htmlspecialchars($_SESSION['user']['prenom']) ?>
</div>
<div class="mobile-user-email">
    <i class="fas fa-id-card"></i> Prenom: <?= htmlspecialchars($_SESSION['user']['nom']) ?>
</div>
<div class="mobile-user-email">
    <i class="fas fa-phone"></i> Numero: <?= htmlspecialchars($_SESSION['user']['numero']) ?>
</div>
<div class="mobile-user-email">
    <i class="fas fa-envelope"></i> Email: <?= htmlspecialchars($_SESSION['user']['email']) ?>
</div>
      </div>
    <?php endif; ?>
    
    <div class="mobile-nav">
      <?php if (isset($_SESSION['user'])): ?>
        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
          <a href="LapinController/dashboard" class="btn btn-outline">
            <i class="fas fa-tachometer-alt"></i> Dashboard
          </a>
        <?php endif; ?>
        <a href="ClientController/deconnexion" class="btn btn-outline">
          <i class="fas fa-sign-out-alt"></i> Déconnexion
        </a>
      <?php else: ?>
        <a href="ClientController/inscription" class="btn btn-outline">
          <i class="fas fa-user-plus"></i> Inscription
        </a>
        <a href="ClientController/connexion" class="btn btn-outline">
          <i class="fas fa-sign-in-alt"></i> Connexion
        </a>
      <?php endif; ?>
    </div>
    <!-- Bande défilante pour les épices -->
<div class="marquee-container">
  <div class="marquee-content">
    <span class="marquee-text">
      <i class="fas fa-bolt" style="margin-right: 8px;"></i>
      Cliquez sur la commande rapide pour commander vos épices de cuisine
    </span>
    <span class="marquee-text">
      <i class="fas fa-bolt" style="margin-right: 8px;"></i>
      Cliquez sur la commande rapide pour commander vos épices de cuisine
    </span>
  </div>
</div>
    <div class="mobile-menu-footer">
      <button onclick="togglePanier()" class="btn btn-primary" style="width: 100%;">
        <i class="fas fa-shopping-cart"></i> Voir le panier <span id="mobile-cart-count">0</span>
      </button>
   <br>
   <br>
  <?php if(isset($_SESSION['user'])): ?>

<form method="POST" action="views/clients/commande_user.php">
    <input type="hidden" name="id" value="<?= htmlspecialchars($_SESSION['user']['id']) ?>">
    <button type="submit" class="btn btn-primary" style="width: 100%;" name="btn_voircommande">
        <i class="fas fa-box"></i> commandes
    </button>
</form>

    <?php endif; ?>
              <br>
<!-- Nouveau bouton de partage -->
        <button class="btn btn-success" onclick="toggleShare()">
            <i class="fas fa-share-alt"></i> Partager avec un ami
        </button>

        <!-- Options de partage -->
        <div class="share-options" id="shareOptions">
            <h4><i class="fas fa-share"></i> Partager ce lien :</h4>
            
            <a href="#" class="share-btn share-whatsapp" onclick="shareWhatsApp()">
                <i class="fab fa-whatsapp"></i> WhatsApp
            </a>
            
            <a href="#" class="share-btn share-email" onclick="shareEmail()">
                <i class="fas fa-envelope"></i> Email
            </a>
            
            <button class="share-btn share-copy" onclick="copyLink()">
                <i class="fas fa-copy"></i> Copier le lien
            </button>
            
            <div class="copy-feedback" id="copyFeedback">
                <i class="fas fa-check"></i> Lien copié dans le presse-papiers !
            </div>
        </div>
      <p>© <?= date('Y') ?> wenat</p>
    </div>
  </div>