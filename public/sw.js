const STATIC_CACHE = 'farmos-static-v1';
const API_CACHE    = 'farmos-api-v1';
const OFFLINE_QUEUE_KEY = 'offlineQueue';

const STATIC_URLS = [
  '/',
  '/manifest.json',
  '/assets/css/app.css',
  '/assets/js/app.js',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js',
  'https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js'
];

// Install
self.addEventListener('install', e => {
  e.waitUntil(
    caches.open(STATIC_CACHE).then(c => c.addAll(STATIC_URLS))
  );
  self.skipWaiting();
});

// Activate
self.addEventListener('activate', e => {
  e.waitUntil(
    caches.keys().then(keys =>
      Promise.all(keys.filter(k => k !== STATIC_CACHE && k !== API_CACHE).map(k => caches.delete(k)))
    )
  );
  self.clients.claim();
});

// Fetch
self.addEventListener('fetch', e => {
  const url = new URL(e.request.url);

  // API routes: network-first, cache fallback
  if (url.pathname.startsWith('/api/')) {
    e.respondWith(
      fetch(e.request.clone())
        .then(res => {
          if (res.ok) {
            const clone = res.clone();
            caches.open(API_CACHE).then(c => c.put(e.request, clone));
          }
          return res;
        })
        .catch(() => caches.match(e.request))
    );
    return;
  }

  // POST/PUT/PATCH/DELETE when offline — queue in IndexedDB
  if (['POST','PUT','PATCH','DELETE'].includes(e.request.method)) {
    e.respondWith(
      fetch(e.request.clone()).catch(async () => {
        const body = await e.request.clone().text();
        await queueOfflineRequest({ url: e.request.url, method: e.request.method, body });
        return new Response(JSON.stringify({ queued: true, message: 'Saved offline' }), {
          headers: { 'Content-Type': 'application/json' }
        });
      })
    );
    return;
  }

  // Static: cache-first
  e.respondWith(
    caches.match(e.request).then(cached => cached || fetch(e.request))
  );
});

// Background Sync
self.addEventListener('sync', e => {
  if (e.tag === 'sync-offline-queue') {
    e.waitUntil(flushOfflineQueue());
  }
});

async function queueOfflineRequest(req) {
  const db  = await openDB();
  const tx  = db.transaction('queue', 'readwrite');
  tx.objectStore('queue').add({ ...req, ts: Date.now() });
  await tx.complete;
}

async function flushOfflineQueue() {
  const db    = await openDB();
  const tx    = db.transaction('queue', 'readwrite');
  const store = tx.objectStore('queue');
  const items = await storeGetAll(store);

  for (const item of items) {
    try {
      const res = await fetch(item.url, { method: item.method, body: item.body, headers: { 'Content-Type': 'application/json' } });
      if (res.ok) { store.delete(item.id); }
    } catch (_) { /* retry next sync */ }
  }
  await tx.complete;
}

function openDB() {
  return new Promise((resolve, reject) => {
    const req = indexedDB.open('farmos-offline', 1);
    req.onupgradeneeded = e => {
      const db = e.target.result;
      if (!db.objectStoreNames.contains('queue')) {
        db.createObjectStore('queue', { keyPath: 'id', autoIncrement: true });
      }
    };
    req.onsuccess = e => resolve(e.target.result);
    req.onerror   = () => reject(req.error);
  });
}

function storeGetAll(store) {
  return new Promise((resolve, reject) => {
    const req = store.getAll();
    req.onsuccess = () => resolve(req.result);
    req.onerror   = () => reject(req.error);
  });
}
