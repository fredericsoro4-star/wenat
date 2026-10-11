// ============================================================
// service-worker.js — Gestion du cache offline + Push
// ============================================================

const CACHE_NAME = 'monapp-v1';
const ASSETS_TO_CACHE = [
  '/',
  '/index.html',
  '/style.css',
  '/app.js',
  '/manifest.json',
  '/icons/icon-192.png',
  '/icons/icon-512.png',
];

// ===== INSTALLATION : mise en cache des ressources statiques =====
self.addEventListener('install', (event) => {
  console.log('[SW] Installation — mise en cache des assets');
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(ASSETS_TO_CACHE);
    })
  );
  // Active immédiatement sans attendre le rechargement
  self.skipWaiting();
});

// ===== ACTIVATION : nettoyage des anciens caches =====
self.addEventListener('activate', (event) => {
  console.log('[SW] Activation');
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(
        keys
          .filter((key) => key !== CACHE_NAME)
          .map((key) => {
            console.log('[SW] Suppression ancien cache :', key);
            return caches.delete(key);
          })
      )
    )
  );
  // Prend le contrôle de toutes les pages immédiatement
  self.clients.claim();
});

// ===== FETCH : stratégie Cache-First avec fallback réseau =====
self.addEventListener('fetch', (event) => {
  // On ne gère que les requêtes GET
  if (event.request.method !== 'GET') return;

  event.respondWith(
    caches.match(event.request).then((cachedResponse) => {
      if (cachedResponse) {
        // Ressource trouvée dans le cache → réponse immédiate
        return cachedResponse;
      }

      // Pas en cache → on tente le réseau
      return fetch(event.request)
        .then((networkResponse) => {
          // On met en cache la nouvelle ressource pour plus tard
          const responseClone = networkResponse.clone();
          caches.open(CACHE_NAME).then((cache) => {
            cache.put(event.request, responseClone);
          });
          return networkResponse;
        })
        .catch(() => {
          // Pas de réseau + pas en cache → page offline de secours
          if (event.request.destination === 'document') {
            return caches.match('/index.html');
          }
        });
    })
  );
});

// ===== PUSH : réception d'une notification push =====
self.addEventListener('push', (event) => {
  console.log('[SW] Notification push reçue');

  let data = {
    title: 'MonApp',
    body: 'Vous avez une nouvelle notification !',
    icon: '/icons/icon-192.png',
    badge: '/icons/icon-192.png',
  };

  // Le serveur peut envoyer des données JSON
  if (event.data) {
    try {
      data = { ...data, ...event.data.json() };
    } catch {
      data.body = event.data.text();
    }
  }

  event.waitUntil(
    self.registration.showNotification(data.title, {
      body: data.body,
      icon: data.icon,
      badge: data.badge,
      vibrate: [100, 50, 100],
      data: { url: data.url || '/' },
      actions: [
        { action: 'open', title: 'Ouvrir' },
        { action: 'dismiss', title: 'Ignorer' },
      ],
    })
  );
});

// ===== NOTIFICATION CLICK : action au clic =====
self.addEventListener('notificationclick', (event) => {
  event.notification.close();

  if (event.action === 'dismiss') return;

  const targetUrl = event.notification.data?.url || '/';

  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
      // Si l'app est déjà ouverte, on focus
      for (const client of clientList) {
        if (client.url === targetUrl && 'focus' in client) {
          return client.focus();
        }
      }
      // Sinon on ouvre une nouvelle fenêtre
      if (clients.openWindow) return clients.openWindow(targetUrl);
    })
  );
});