<!-- FOOTER -->
  <footer>
    <div class="footer-container">
      <div class="footer-about">
        <div class="footer-logo">
          <img src="assets/images/image.png" alt="wenat">
          <span>wenat</span>
        </div>
        <p>Bien manger n’a jamais été aussi simple : Wenat vous apporte le meilleur de l’alimentation directement en ligne.</p>
         <div class="footer-social">
         <a href="https://www.facebook.com/profile.php?id=100094330994131" 
           class="social-link facebook" 
           target="_blank" 
           rel="noopener noreferrer" 
           aria-label="Suivez-nous sur Facebook">
            <img src="assets/images/facebook.png" 
                 alt="Facebook" 
                 width="30" 
                 height="30"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
            <i class="fab fa-facebook-f fallback-icon" style="display:none;"></i>
        </a>

        <!-- Instagram avec image locale + fallback -->
        <a href="https://instagram.com" 
           class="social-link instagram" 
           target="_blank" 
           rel="noopener noreferrer" 
           aria-label="Suivez-nous sur Instagram">
            <img src="assets/images/instag.jpg" 
                 alt="Instagram" 
                 width="24" 
                 height="24"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
            <i class="fab fa-instagram fallback-icon" style="display:none;"></i>
        </a>

        <!-- WhatsApp avec Font Awesome -->
        <a href="https://wa.me/2250161934847" 
           class="social-link whatsapp" 
           target="_blank" 
           rel="noopener noreferrer" 
           aria-label="Contactez-nous sur WhatsApp">
            <i class="fab fa-whatsapp"></i>
        </a>
    </div>
      </div>
      
      <div class="footer-links">
        <h3>Liens rapides</h3>
        <ul>
          <li><a href="#"><i class="fas fa-chevron-right"></i> Accueil</a></li>
          <li><a href="views/clients/assistance.php"><i class="fas fa-chevron-right"></i> assistance</a></li>
          <li><a href="#"><i class="fas fa-chevron-right"></i> À propos</a></li>
          <li><a href="#"><i class="fas fa-chevron-right"></i> Blog</a></li>
          <li><a href="#"><i class="fas fa-chevron-right"></i> FAQ</a></li>
        </ul>
      </div>
      
      <div class="footer-links">
        <h3>Informations</h3>
        <ul>
          <li><a href="views/clients/condition_generale.php"><i class="fas fa-chevron-right"></i> Conditions générales</a></li>
          <li><a href="views/clients/poltique_de_confidentilité.php"><i class="fas fa-chevron-right"></i> Politique de confidentialité</a></li>
          <li><a href="views/clients/livraison_retour.php"><i class="fas fa-chevron-right"></i> Livraison & Retours</a></li>
          <li><a href="views/clients/payement_securise.php"><i class="fas fa-chevron-right"></i> Paiements sécurisés</a></li>
          <li><a href="#"><i class="fas fa-chevron-right"></i> Mentions légales</a></li>
        </ul>
      </div>
      
      <div class="footer-contact">
        <h3>Contactez-nous</h3>
        <p><i class="fas fa-map-marker-alt"></i> Cote d'ivoire, Bouaké</p>
        <p><i class="fas fa-phone"></i> 07-48-72-83-08 / 01-61-93-48-47</p>
        <p><i class="fas fa-envelope"></i> contact@wenat.com</p>
        <p><i class="fas fa-clock"></i> Lun-Ven: 9h-18h / Sam: 10h-16h</p>
      </div>
    </div>
      <div class="ordi-footer-panier">

    <button onclick="togglePanier()" class="btn-panier">
        <i class="fas fa-shopping-cart"></i> Voir le panier 
    </button>

    <?php if(isset($_SESSION['user'])): ?>

<form method="POST" action="views/clients/commande_user.php">
    <input type="hidden" name="id" value="<?= htmlspecialchars($_SESSION['user']['id']) ?>">
    <button type="submit" class="btn-commandes" name="btn_voircommande">
        <i class="fas fa-box"></i> commandes
    </button>
</form>

<?php endif; ?>
    
    <button class="btn-partage" onclick="toggleShare()">
        <i class="fas fa-share-alt"></i> Partager avec un ami
    </button>

    <div class="share-options" id="shareOptions">

        <h4><i class="fas fa-share"></i> Partager ce lien</h4>

        <a href="#" class="share-btn whatsapp" onclick="shareWhatsApp()">
            <i class="fab fa-whatsapp"></i> WhatsApp
        </a>

        <a href="#" class="share-btn email" onclick="shareEmail()">
            <i class="fas fa-envelope"></i> Email
        </a>

        <button class="share-btn copy" onclick="copyLink()">
            <i class="fas fa-copy"></i> Copier le lien
        </button>

        <div class="copy-feedback" id="copyFeedback">
            <i class="fas fa-check"></i> Lien copié !
        </div>

    </div>

</div>
      <p>© <?= date('Y') ?> wenat</p>
    </div>
  </div>

  <!-- SEARCH -->

    <div class="footer-bottom">
      © <?= date('Y') ?> wenat. Tous droits réservés.
    </div>
  </footer>