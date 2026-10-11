// Enregistrement du Service Worker
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/sw.js')
      .then(registration => {
        console.log('SW enregistré: ', registration);
      })
      .catch(error => {
        console.log('Échec enregistrement SW: ', error);
      });
  });
}

// Détection de la PWA
window.addEventListener('appinstalled', () => {
  console.log('PWA installée');
  // Masquer le bouton après installation
  const installButton = document.querySelector('.install-pwa-btn');
  if (installButton) {
    installButton.remove();
  }
});

// Fonction pour proposer l'installation
let deferredPrompt;
window.addEventListener('beforeinstallprompt', (e) => {
  e.preventDefault();
  deferredPrompt = e;
  
  // Créer le bouton d'installation avec styles
  const installButton = document.createElement('button');
  
  // Ajouter la classe CSS
  installButton.className = 'install-pwa-btn';
  
  // Contenu du bouton avec icône
  installButton.innerHTML = `
    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
      <path d="M5,20H19V18H5M19,9H15V3H9V9H5L12,16L19,9Z" />
    </svg>
    

  `;
  
  // Gestionnaire de clic
  installButton.addEventListener('click', async () => {
    if (deferredPrompt) {
      // Afficher animation de chargement
      installButton.innerHTML = `
        <div class="loading-spinner"></div>
        <span>Installation...</span>
      `;
      installButton.disabled = true;
      
      try {
        // Déclencher l'installation
        deferredPrompt.prompt();
        const choiceResult = await deferredPrompt.userChoice;
        
        if (choiceResult.outcome === 'accepted') {
          console.log('Utilisateur a accepté l\'installation');
          // Animation de succès
          installButton.innerHTML = `
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
              <path d="M9,20.42L2.79,14.21L5.62,11.38L9,14.77L18.88,4.88L21.71,7.71L9,20.42Z"/>
            </svg>
            <span>Installé !</span>
          `;
          installButton.classList.add('success');
          
          // Masquer après 2 secondes
          setTimeout(() => {
            installButton.style.opacity = '0';
            setTimeout(() => installButton.remove(), 300);
          }, 2000);
        } else {
          // Remettre le bouton normal si refusé
          installButton.innerHTML = `
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
              <path d="M5,20H19V18H5M19,9H15V3H9V9H5L12,16L19,9Z" />
            </svg>
          
            
          `;
          installButton.disabled = false;
        }
        
        deferredPrompt = null;
      } catch (error) {
        console.error('Erreur installation:', error);
        // Remettre le bouton en cas d'erreur
        installButton.innerHTML = `
          <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
            <path d="M5,20H19V18H5M19,9H15V3H9V9H5L12,16L19,9Z" />
          </svg>
        
          
        `;
        installButton.disabled = false;
      }
    }
  });
  
  // Ajouter le bouton à la page
  document.body.appendChild(installButton);
  
  // Animation d'apparition
  setTimeout(() => {
    installButton.classList.add('show');
  }, 100);
});
 

  

   // Fonction pour afficher tous les lapins
   function showAllLapins() {
     const cards = document.querySelectorAll('.card');
     
     cards.forEach(card => {
       card.style.display = 'block';
     });
     
     document.querySelector('.section-title').textContent = 'Tous nos lapins';
     toggleCatalogDropdown();
   }

   // Fonction pour obtenir le nom complet du type
   function getTypeName(type) {
     const types = {
       'rex': 'Rex',
       'nain': 'Nains',
       'belier': 'Béliers',
       'angora': 'Angoras',
       'geant': 'Géants',
       'caille': 'Cailles',
       'papillon': 'Papillons',
       'zibeline': 'Zibelines',
       'feu': 'Feu',
       'argente': 'Argentés'
     };
     return types[type] || type;
   }

   // Fonction pour afficher un message si aucun résultat
   function showNoResultsMessage(type) {
     // Vous pouvez personnaliser cette fonction selon vos besoins
     console.log(`Aucun lapin de type ${type} trouvé`);
   }

   // Fonctions pour le bouton de commande rapide
   let lastScrollY = window.scrollY;
   const quickBtn = document.querySelector('.quick-order-btn');
   
   window.addEventListener('scroll', () => {
     const currentScrollY = window.scrollY;
     
     if (currentScrollY > lastScrollY) {
       quickBtn.style.transform = 'scale(0.9)';
     } else {
       quickBtn.style.transform = 'scale(1)';
     }
     
     lastScrollY = currentScrollY;
   });

   // Fonction pour jouer la vidéo
   function playVideo() {
     const video = document.querySelector('.guide-video');
     const overlay = document.querySelector('.video-overlay');
     
     video.play();
     overlay.style.display = 'none';
   }

   // Gestion de l'affichage de l'overlay selon l'état de la vidéo
   const video = document.querySelector('.guide-video');
   const overlay = document.querySelector('.video-overlay');
   
   video.addEventListener('play', function() {
     overlay.style.display = 'none';
   });
   
   video.addEventListener('pause', function() {
     overlay.style.display = 'flex';
   });

   function voirCommandes() {
     alert('Redirection vers vos commandes...');
   }

   function toggleShare() {
     const shareOptions = document.getElementById('shareOptions');
     shareOptions.classList.toggle('show');
   }

   function shareWhatsApp() {
     const url = encodeURIComponent(window.location.href);
     const text = encodeURIComponent('Découvrez cette boutique en ligne ! Vous pouvez voir vos commandes facilement.');
     window.open(`https://wa.me/?text=${text} ${url}`, '_blank');
   }

   function shareEmail() {
     const url = encodeURIComponent(window.location.href);
     const subject = encodeURIComponent('Découvrez cette boutique en ligne');
     const body = encodeURIComponent(`Salut ! Je voulais partager avec toi cette boutique en ligne. Tu peux facilement voir tes commandes ici : ${window.location.href}`);
     window.location.href = `mailto:?subject=${subject}&body=${body}`;
   }

   function copyLink() {
     navigator.clipboard.writeText(window.location.href).then(function() {
       const feedback = document.getElementById('copyFeedback');
       feedback.style.display = 'block';
       setTimeout(() => {
         feedback.style.display = 'none';
       }, 3000);
     }).catch(function() {
       alert('Lien copié : ' + window.location.href);
     });
   }

   // Fermer les options de partage si on clique ailleurs
   document.addEventListener('click', function(event) {
     const shareOptions = document.getElementById('shareOptions');
     const shareButton = event.target.closest('.btn-success');
     
     if (!shareButton && !shareOptions.contains(event.target)) {
       shareOptions.classList.remove('show');
     }
   });
   
function showDescription(description) {
    document.getElementById("modalText").innerText = description;
    document.getElementById("descriptionModal").style.display = "flex";
}

function closeDescription() {
    document.getElementById("descriptionModal").style.display = "none";
}

window.onclick = function(event) {
    let modal = document.getElementById("descriptionModal");
    if (event.target === modal) {
        modal.style.display = "none";
    }
}


   let panier = JSON.parse(localStorage.getItem('panier')) || [];
let favoris = JSON.parse(localStorage.getItem('favoris')) || [];

// Initialisation
document.addEventListener('DOMContentLoaded', () => {
  updateCartCount();
  updatePanier();
  initFavoris();
  initMobileMenu();
  initPanierEvents();
  startPanierSync();
});

// Synchroniser le panier avec le localStorage régulièrement
function startPanierSync() {
  setInterval(() => {
    const savedPanier = JSON.parse(localStorage.getItem('panier')) || [];
    if (JSON.stringify(panier) !== JSON.stringify(savedPanier)) {
      panier = savedPanier;
      updateCartCount();
    }
  }, 1000);
}

// Initialiser les événements du panier
function initPanierEvents() {
  document.getElementById('panier-overlay').addEventListener('click', () => {
    savePanier(); updateCartCount(); togglePanier();
  });
  document.querySelector('.close-panier').addEventListener('click', () => {
    savePanier(); updateCartCount(); togglePanier();
  });
}

// Gestion du menu mobile
function initMobileMenu() {
  const burgerMenu        = document.getElementById('burger-menu');
  const mobileMenu        = document.getElementById('mobile-menu');
  const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');

  burgerMenu.addEventListener('click', (e) => {
    e.stopPropagation();
    burgerMenu.classList.toggle('active');
    mobileMenu.classList.toggle('active');
    mobileMenuOverlay.classList.toggle('active');
  });

  mobileMenuOverlay.addEventListener('click', () => {
    burgerMenu.classList.remove('active');
    mobileMenu.classList.remove('active');
    mobileMenuOverlay.classList.remove('active');
  });

  mobileMenu.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => {
      burgerMenu.classList.remove('active');
      mobileMenu.classList.remove('active');
      mobileMenuOverlay.classList.remove('active');
    });
  });
}

// Gestion du panier
function togglePanier() {
  const modal   = document.getElementById('panier-modal');
  const overlay = document.getElementById('panier-overlay');

  if (modal.classList.contains('show')) {
    savePanier();
    updateCartCount();
    modal.classList.remove('show');
    overlay.classList.remove('show');
    setTimeout(() => {
      modal.style.display   = 'none';
      overlay.style.display = 'none';
    }, 300);
  } else {
    modal.style.display   = 'block';
    overlay.style.display = 'block';
    setTimeout(() => {
      modal.classList.add('show');
      overlay.classList.add('show');
    }, 10);
    updatePanier();
  }

  document.getElementById('burger-menu').classList.remove('active');
  document.getElementById('mobile-menu').classList.remove('active');
  document.getElementById('mobile-menu-overlay').classList.remove('active');
}

function addToCart(lapinId, event) {
  event.preventDefault();

  const lapin = lapinsData.find(l => l.id == lapinId);
  if (!lapin) return;

  const existingItem = panier.find(item => item.id == lapinId);
  if (existingItem) {
    existingItem.quantity += 1;
  } else {
    panier.push({
      id:       lapin.id,
      name:     lapin.type,
      price:    parseFloat(lapin.prix),
      image:    lapin.image,
      quantity: 1
    });
  }

  savePanier();
  updateCartCount();
  updatePanier();

  const btn          = event.target.closest('.btn-primary');
  const originalHTML = btn.innerHTML;
  btn.innerHTML      = '<i class="fas fa-check"></i> Ajouté !';
  btn.style.background = 'linear-gradient(135deg, #28a745, #20c997)';
  createParticle(btn);

  setTimeout(() => {
    btn.innerHTML        = originalHTML;
    btn.style.background = '';
  }, 1000);

  animateCartCount();
}

function createParticle(element) {
  const rect     = element.getBoundingClientRect();
  const particle = document.createElement('div');
  particle.style.cssText = `
    position: fixed;
    width: 10px; height: 10px;
    background: var(--primary);
    border-radius: 50%;
    pointer-events: none;
    z-index: 1000;
    left: ${rect.left + rect.width / 2}px;
    top: ${rect.top + rect.height / 2}px;
    transform: translate(-50%, -50%);
    animation: particleFly 1s ease-out forwards;
  `;
  document.body.appendChild(particle);
  setTimeout(() => particle.remove(), 1000);
}

function animateCartCount() {
  ['cart-count', 'mobile-cart-count', 'mobile-cart-count-small'].forEach(id => {
    const el = document.getElementById(id);
    if (!el) return;
    el.style.animation = 'none';
    void el.offsetWidth;
    el.style.animation = 'pulse 0.5s ease-out';
  });
}

function updateQuantity(id, change) {
  const item = panier.find(item => item.id == id);
  if (!item) return;

  item.quantity += change;
  if (item.quantity <= 0) {
    panier = panier.filter(item => item.id != id);
  }

  savePanier();
  updateCartCount();
  updatePanier();

  const quantityElement = event.target.closest('.quantity-controls').querySelector('span');
  quantityElement.style.animation = 'pulse 0.3s ease-out';
  setTimeout(() => { quantityElement.style.animation = ''; }, 300);
}

function removeFromCart(id) {
  panier = panier.filter(item => item.id != id);
  savePanier();
  updateCartCount();
  updatePanier();
}

function updateCartCount() {
  const count = panier.reduce((total, item) => total + item.quantity, 0);
  ['cart-count', 'mobile-cart-count', 'mobile-cart-count-small'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.textContent = count;
  });
}

function updatePanier() {
  const panierContent = document.getElementById('panier-content');
  const panierTotal   = document.getElementById('panier-total');

  if (panier.length === 0) {
    panierContent.innerHTML = `
      <div class="empty-state" style="padding: 60px 20px;">
        <i class="fas fa-shopping-cart" style="font-size: 50px; color: var(--primary-light);"></i>
        <p style="font-size: 16px; margin-top: 20px;">Votre panier est vide</p>
        <p style="font-size: 14px; opacity: 0.7; margin-top: 10px;">Ajoutez des produits à votre panier !</p>
      </div>
    `;
    panierTotal.textContent = '0,00 FCFA';
    return;
  }

  let html  = '';
  let total = 0;

  panier.forEach(item => {
    const itemTotal = item.price * item.quantity;
    total += itemTotal;
    html  += `
      <div class="panier-item">
        <img src="${item.image}" alt="${item.name}">
        <div class="panier-item-details">
          <h4>${item.name}</h4>
          <div class="quantity-controls">
            <button class="quantity-btn" onclick="updateQuantity(${item.id}, -1)">
              <i class="fas fa-minus"></i>
            </button>
            <span>${item.quantity}</span>
            <button class="quantity-btn" onclick="updateQuantity(${item.id}, 1)">
              <i class="fas fa-plus"></i>
            </button>
            <span style="margin-left: auto; font-weight: bold;">${itemTotal.toFixed(2)} FCFA</span>
          </div>
        </div>
        <button class="remove-btn" onclick="removeFromCart(${item.id})">
          <i class="fas fa-trash"></i>
        </button>
      </div>
    `;
  });

  panierContent.innerHTML = html;
  panierTotal.textContent = `${total.toFixed(2)} FCFA`;
}

function savePanier() {
  localStorage.setItem('panier', JSON.stringify(panier));
}

function commander() {
  if (panier.length === 0) {
    showToast('Votre panier est vide !', 'error');
    return;
  }

  const btn          = document.getElementById('commander-btn');
  const originalHTML = btn.innerHTML;
  btn.innerHTML      = '<i class="fas fa-spinner fa-spin"></i> Commande en cours...';
  btn.disabled       = true;

  const commande = {
    user_id:    SESSION_USER.user_id,
    first_name: SESSION_USER.first_name,
    last_name:  SESSION_USER.last_name,
    email:      SESSION_USER.email,
    phone:      SESSION_USER.phone,
    items:      panier,
    total:      panier.reduce((sum, item) => sum + (item.price * item.quantity), 0),
    date:       new Date().toISOString()
  };

  fetch('views/clients/traitement_commande.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(commande)
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {

      // 1️⃣ Toast succès
      showToast('Commande effectuée avec succès !', 'success');

      // 2️⃣ Notification navigateur avec les noms des produits
      setTimeout(() => {
        if ("Notification" in window) {
          Notification.requestPermission().then(permission => {
            if (permission === "granted") {
              const nomsProduits = commande.items
                .map(item => `• ${item.name} x${item.quantity}`)
                .join('\n');

              new Notification("🎉 Commande validée sur wenat.shop !", {
                body: `Merci ${commande.first_name} ! Vous avez commandé :\n${nomsProduits}`,
                icon: "http://localhost/projets/wenat/assets/images/image.png"
              });
            }
          });
        }
      }, 2000);

      // 3️⃣ Reset panier
      panier = [];
      savePanier();
      updateCartCount();
      updatePanier();

      // 4️⃣ Reload page après la notification
      setTimeout(() => { window.location.reload(); }, 7000);

    } else {
      showToast('Erreur: ' + (data.message || 'Problème lors de la commande'), 'error');
    }
  })
  .catch(error => {
    console.error('Erreur:', error);
    showToast('Une erreur est survenue lors de la commande', 'error');
  })
  .finally(() => {
    btn.innerHTML = originalHTML;
    btn.disabled  = false;
  });
}

// Gestion des favoris
function initFavoris() {
  favoris.forEach(id => {
    const btn = document.querySelector(`.btn-icon[onclick="toggleFavorite(${id})"]`);
    if (btn) {
      btn.querySelector('i').classList.replace('far', 'fas');
      btn.style.color = '#131212ff';
    }
  });
}

function toggleFavorite(lapinId) {
  const btn  = event.target.closest('.btn-icon');
  const icon = btn.querySelector('i');

  if (icon.classList.contains('fas')) {
    icon.classList.replace('fas', 'far');
    btn.style.color = '';
    favoris = favoris.filter(id => id != lapinId);
    btn.style.animation = 'heartBreak 0.6s ease-out';
  } else {
    icon.classList.replace('far', 'fas');
    btn.style.color = '#0e0d0dff';
    favoris.push(lapinId);
    btn.style.animation = 'heartBeat 0.6s ease-out';
  }

  setTimeout(() => { btn.style.animation = ''; }, 600);
  localStorage.setItem('favoris', JSON.stringify(favoris));
}

// Système de notifications toast
function showToast(message, type = 'info') {
  const styles = {
    success: { bg: 'linear-gradient(135deg, #28a745, #20c997)', icon: 'check-circle' },
    error:   { bg: 'linear-gradient(135deg, #dc3545, #e74c3c)', icon: 'exclamation-circle' },
    info:    { bg: 'linear-gradient(135deg, var(--primary), var(--primary-dark))', icon: 'info-circle' }
  };
  const s = styles[type] || styles.info;

  const toast = document.createElement('div');
  toast.style.cssText = `
    position: fixed; top: 30px; right: 30px;
    padding: 18px 25px;
    border-radius: var(--radius-sm);
    color: white; font-weight: 600;
    z-index: 10000;
    animation: slideInRight 0.4s cubic-bezier(0.22, 1, 0.36, 1), fadeOut 0.4s ease 3s forwards;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    display: flex; align-items: center; gap: 15px;
    background: ${s.bg};
  `;
  toast.innerHTML = `<i class="fas fa-${s.icon}"></i> ${message}`;
  document.body.appendChild(toast);
  setTimeout(() => toast.remove(), 3400);
}

// Animations CSS
const styleSheet = document.createElement('style');
styleSheet.textContent = `
  @keyframes slideInRight {
    from { opacity: 0; transform: translateX(100px); }
    to   { opacity: 1; transform: translateX(0); }
  }
  @keyframes fadeOut {
    to { opacity: 0; transform: translateY(-20px); }
  }
  @keyframes heartBeat {
    0%   { transform: scale(1); }
    25%  { transform: scale(1.2); }
    50%  { transform: scale(1); }
    75%  { transform: scale(1.1); }
    100% { transform: scale(1); }
  }
  @keyframes heartBreak {
    0%   { transform: scale(1) rotate(0deg); }
    25%  { transform: scale(0.8) rotate(-10deg); }
    50%  { transform: scale(1.1) rotate(5deg); }
    75%  { transform: scale(0.9) rotate(-3deg); }
    100% { transform: scale(1) rotate(0deg); }
  }
  @keyframes particleFly {
    0%   { transform: translate(-50%, -50%) scale(1); opacity: 1; }
    100% { transform: translate(0, -60px) scale(0); opacity: 0; }
  }
  @keyframes pulse {
    0%   { transform: scale(1); }
    50%  { transform: scale(1.1); }
    100% { transform: scale(1); }
  }
`;
document.head.appendChild(styleSheet);

// Animation des cartes à l'écran
const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.style.animationPlayState = 'running';
    }
  });
}, { threshold: 0.1 });

document.querySelectorAll('.card').forEach(card => observer.observe(card));
const texts = document.querySelectorAll(".main-marquee-text");

let index = 0;
let position = window.innerWidth;
let speed = 8;
let isPaused = false;
let isSlowing = false;

function initText(i) {
    texts.forEach((el, idx) => {
        el.style.display = idx === i ? "inline-flex" : "none";
        el.classList.remove("blink"); // enlever clignotement
        el.style.transform = "translateX(0)";
    });

    position = window.innerWidth;
    speed = 8;
    isPaused = false;
    isSlowing = false;
}

function animate() {
    const current = texts[index];

    if (!isPaused) {
        position -= speed;
        current.style.transform = `translateX(${position}px)`;

        const rect = current.getBoundingClientRect();
        const center = window.innerWidth / 2;
        const textCenter = rect.left + rect.width / 2;

        const distance = Math.abs(textCenter - center);

        // 🔽 ralentir avant centre
        if (distance < 150 && !isSlowing) {
            speed = 2;
            isSlowing = true;
        }

        // ⛔ arrêt au centre + clignotement
        if (distance < 3 && !isPaused) {
            isPaused = true;

            // 👉 ajouter clignotement
            current.classList.add("blink");

            setTimeout(() => {
                isPaused = false;
                current.classList.remove("blink");
                speed = 4;
            }, 5000);
        }

        // 🔁 changement message
        if (rect.right < 0) {
            index = (index + 1) % texts.length;
            initText(index);
        }
    }

    requestAnimationFrame(animate);
}

initText(index);
animate();
