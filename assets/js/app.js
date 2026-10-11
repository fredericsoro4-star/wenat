// ============================================================
// app.js — Logique PWA (Service Worker + Installation + Push)
// ============================================================

// ===== 1. ENREGISTREMENT DU SERVICE WORKER =====
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

// ===== 2. INSTALLATION PWA (Add to Home Screen) =====
let deferredPrompt;

window.addEventListener('beforeinstallprompt', (e) => {
  e.preventDefault();
  deferredPrompt = e;

  // Créer le bouton d'installation
  const installButton = document.createElement('button');
  installButton.className = 'install-pwa-btn';
  installButton.innerHTML = `
    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
      <path d="M5,20H19V18H5M19,9H15V3H9V9H5L12,16L19,9Z" />
    </svg>
    <span>Installer l'app</span>
  `;

  installButton.addEventListener('click', async () => {
    if (!deferredPrompt) return;

    installButton.innerHTML = `
      <div class="loading-spinner"></div>
      <span>Installation...</span>
    `;
    installButton.disabled = true;

    try {
      deferredPrompt.prompt();
      const choiceResult = await deferredPrompt.userChoice;

      if (choiceResult.outcome === 'accepted') {
        console.log('Utilisateur a accepté l\'installation');
        installButton.innerHTML = `
          <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
            <path d="M9,20.42L2.79,14.21L5.62,11.38L9,14.77L18.88,4.88L21.71,7.71L9,20.42Z"/>
          </svg>
          <span>Installé !</span>
        `;
        installButton.classList.add('success');

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
          <span>Installer l'app</span>
        `;
        installButton.disabled = false;
      }

      deferredPrompt = null;

    } catch (error) {
      console.error('Erreur installation:', error);
      installButton.innerHTML = `
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
          <path d="M5,20H19V18H5M19,9H15V3H9V9H5L12,16L19,9Z" />
        </svg>
        <span>Installer l'app</span>
      `;
      installButton.disabled = false;
    }
  });

  document.body.appendChild(installButton);

  // Animation d'apparition
  setTimeout(() => {
    installButton.classList.add('show');
  }, 100);
});

// Masquer le bouton après installation
window.addEventListener('appinstalled', () => {
  console.log('PWA installée');
  const installButton = document.querySelector('.install-pwa-btn');
  if (installButton) installButton.remove();
});

// ===== 3. NOTIFICATIONS PUSH =====
// Appelée par ton bouton "Activer les notifications"
function requestNotificationPermission() {
  if (!('Notification' in window)) {
    console.warn('Notifications non supportées');
    return;
  }

  Notification.requestPermission().then(permission => {
    if (permission === 'granted') {
      console.log('Permission notifications accordée');
    }
  });
}

// Afficher une notification via le Service Worker
function showNotification(title, body, icon = '/assets/images/image.png') {
  if (Notification.permission !== 'granted') return;

  navigator.serviceWorker.ready.then(registration => {
    registration.showNotification(title, {
      body,
      icon,
      vibrate: [100, 50, 100],
    });
  });
}