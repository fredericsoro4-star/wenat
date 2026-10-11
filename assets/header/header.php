 <header id="main-header">
    <div class="header-container">
      <div class="logo-container">
        <div class="logo">
          <img src="assets/images/image.png" alt="Logo wenat">
        </div>
       
      </div>
       <div class="logos-wenat">
      
            <img src="assets/images/epice.png" alt="Logo wenat">
            <img src="assets/images/epice2.png" alt="Logo wenat">
            <img src="assets/images/epice1.png" alt="Logo wenat">
            <img src="assets/images/epice3.png" alt="Logo wenat">
            <img src="assets/images/epice4.png" alt="Logo wenat">
      
           </div>
      <div class="header-actions">
        <?php if (isset($_SESSION['user']['prenom'])): ?>
          <div class="user-profile">
            <div class="user-avatar">
                <div class="ordi-user-avatar">
           <img src="assets/images/user.png" alt="Avatar utilisateur">
        </div>
            </div>
            
            <span class="user-name"><?= htmlspecialchars($_SESSION['user']['nom'] . ' ' . $_SESSION['user']['prenom']) ?></span>
          </div>
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
        <button onclick="togglePanier()" class="btn btn-outline">
          <i class="fas fa-shopping-cart"></i> Panier <span id="cart-count">0</span>
        </button>
        <div class="ordi-param">
           <a href="views/clients/paramettre.php">
        <img src="assets/images/paramettre (1).jpg" alt="Avatar utilisateur">
        </a>
        </div>
       
      </div>
      
      <!-- Bouton panier pour mobile -->
      <button class="mobile-cart-btn" onclick="togglePanier()">
        <i class="fas fa-shopping-cart"></i>
        <span id="mobile-cart-count-small">0</span>
      </button>
      
      <!-- Menu Burger pour mobile -->
      <div class="burger-menu" id="burger-menu">
        <span></span>
        <span></span>
        <span></span>
      </div>
    </div>
  </header>
   <!-- BANDE DÉFILANTE PRINCIPALE POUR LES ÉPICES -->

