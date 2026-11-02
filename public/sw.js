const CACHE_NAME = 'zajeciownia-cache-v1';
const OFFLINE_URL = '/offline.html';

const ASSETS_TO_CACHE = [
  OFFLINE_URL,
  '/manifest.json',
  '/icons/icon-192x192.png',
  '/icons/icon-512x512.png',
  'https://cdn.jsdelivr.net/npm/@tabler/core@1.6.0/dist/css/tabler.min.css',
  'https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.48.0/dist/tabler-icons.min.css'
];

// Instalacja Service Workera i cache'owanie kluczowych zasobów offline
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(ASSETS_TO_CACHE);
    }).then(() => self.skipWaiting())
  );
});

// Aktywacja nowego SW i czyszczenie starych wersji cache
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys.map((key) => {
          if (key !== CACHE_NAME) {
            return caches.delete(key);
          }
        })
      );
    }).then(() => self.clients.claim())
  );
});

// Obsługa zapytań sieciowych (Network-first dla nawigacji HTML, cache fallback na offline.html)
self.addEventListener('fetch', (event) => {
  // Ignorujemy zapytania inne niż GET oraz specyficzne protokoły (np. chrome-extension)
  if (event.request.method !== 'GET' || !event.request.url.startsWith('http')) {
    return;
  }

  // Zapytania nawigacyjne (żądanie strony HTML)
  if (event.request.mode === 'navigate') {
    event.respondWith(
      fetch(event.request).catch(() => {
        return caches.match(OFFLINE_URL);
      })
    );
    return;
  }

  // Dla statycznych zasobów (obrazy, czcionki, ikony, skrypty) - Stale-While-Revalidate lub Cache-first
  event.respondWith(
    caches.match(event.request).then((cachedResponse) => {
      if (cachedResponse) {
        // Zwracamy z pamięci podręcznej i w tle odświeżamy
        fetch(event.request).then((networkResponse) => {
          if (networkResponse && networkResponse.status === 200) {
            caches.open(CACHE_NAME).then((cache) => cache.put(event.request, networkResponse));
          }
        }).catch(() => {});
        return cachedResponse;
      }

      return fetch(event.request).then((networkResponse) => {
        // Cache'ujemy wybrane statyczne zasoby (np. ikony)
        if (
          networkResponse &&
          networkResponse.status === 200 &&
          (event.request.url.includes('/icons/') || event.request.url.includes('/images/'))
        ) {
          const responseClone = networkResponse.clone();
          caches.open(CACHE_NAME).then((cache) => cache.put(event.request, responseClone));
        }
        return networkResponse;
      });
    })
  );
});

// Obsługa powiadomień Push (Web Push API)
self.addEventListener('push', (event) => {
  let notificationData = {
    title: 'Zajęciownia.pl',
    body: 'Otrzymałeś nowe powiadomienie.',
    icon: '/icons/icon-192x192.png',
    badge: '/icons/icon-192x192.png',
    data: {
      url: '/pulpit'
    }
  };

  if (event.data) {
    try {
      const parsed = event.data.json();
      notificationData = {
        title: parsed.title || notificationData.title,
        body: parsed.body || notificationData.body,
        icon: parsed.icon || notificationData.icon,
        badge: parsed.badge || notificationData.badge,
        data: {
          url: (parsed.data && parsed.data.url) || parsed.url || '/pulpit'
        }
      };
    } catch (e) {
      notificationData.body = event.data.text();
    }
  }

  const options = {
    body: notificationData.body,
    icon: notificationData.icon,
    badge: notificationData.badge,
    data: notificationData.data,
    vibrate: [100, 50, 100],
  };

  event.waitUntil(
    self.registration.showNotification(notificationData.title, options)
  );
});

// Kliknięcie w powiadomienie Push
self.addEventListener('notificationclick', (event) => {
  event.notification.close();

  const targetUrl = (event.notification.data && event.notification.data.url) ? event.notification.data.url : '/pulpit';

  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then((windowClients) => {
      for (const client of windowClients) {
        if (client.url.includes(targetUrl) && 'focus' in client) {
          return client.focus();
        }
      }
      if (clients.openWindow) {
        return clients.openWindow(targetUrl);
      }
    })
  );
});
