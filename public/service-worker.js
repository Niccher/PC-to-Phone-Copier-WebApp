const CACHE_NAME = 'p2p-copier-v2';
const ASSETS = [
  '/assets/css/app.min.css',
  '/assets/css/icons.min.css',
  '/assets/js/app.min.js',
  '/favicon.png'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
          return Promise.all(
              ASSETS.map(url => {
                  return cache.add(url).catch(reason => {
                      console.log(`SW Cache Error on ${url}:`, reason);
                  });
              })
          );
      })
  );
  self.skipWaiting();
});

self.addEventListener('activate', event => {
  event.waitUntil(self.clients.claim());
});

self.addEventListener('fetch', event => {
  if (event.request.method !== 'GET') return;
  
  // Network First, Cache Fallback strategy
  event.respondWith(
    fetch(event.request)
      .then(response => {
        // Optionally cache new dynamic requests here, but for now we just return network
        return response;
      })
      .catch(() => {
        return caches.match(event.request);
      })
  );
});
