/* Farm OS — app.js */

// PWA: Register service worker
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/sw.js').catch(err => console.warn('SW failed:', err));
  });
}

// Offline / Online indicator
const offlineEl = document.createElement('div');
offlineEl.id = 'offline-indicator';
offlineEl.textContent = '📵 Offline — data will sync';
document.body.appendChild(offlineEl);

function updateOnlineStatus() {
  if (!navigator.onLine) {
    offlineEl.classList.add('show');
  } else {
    offlineEl.classList.remove('show');
    // Trigger background sync if supported
    if ('serviceWorker' in navigator && 'SyncManager' in window) {
      navigator.serviceWorker.ready.then(reg => reg.sync.register('sync-offline-queue'));
    }
  }
}
window.addEventListener('online',  updateOnlineStatus);
window.addEventListener('offline', updateOnlineStatus);
updateOnlineStatus();

// IndexedDB helper (for offline form submissions from regular pages)
const FarmDB = (() => {
  let db = null;
  function open() {
    return new Promise((resolve, reject) => {
      if (db) return resolve(db);
      const req = indexedDB.open('farmos-offline', 1);
      req.onupgradeneeded = e => {
        const d = e.target.result;
        if (!d.objectStoreNames.contains('queue')) {
          d.createObjectStore('queue', { keyPath: 'id', autoIncrement: true });
        }
      };
      req.onsuccess = e => { db = e.target.result; resolve(db); };
      req.onerror   = () => reject(req.error);
    });
  }

  async function enqueue(url, method, body) {
    const d  = await open();
    const tx = d.transaction('queue', 'readwrite');
    tx.objectStore('queue').add({ url, method, body, ts: Date.now() });
  }

  async function flush() {
    const d     = await open();
    const tx    = d.transaction('queue', 'readwrite');
    const store = tx.objectStore('queue');
    const items = await new Promise((res, rej) => {
      const r = store.getAll(); r.onsuccess = () => res(r.result); r.onerror = () => rej(r.error);
    });
    for (const item of items) {
      try {
        const r = await fetch(item.url, { method: item.method, headers: { 'Content-Type': 'application/json' }, body: item.body });
        if (r.ok) store.delete(item.id);
      } catch (_) { /* retry later */ }
    }
  }

  return { enqueue, flush };
})();

// Offline form interceptor: intercept POST forms while offline
document.addEventListener('submit', async e => {
  if (navigator.onLine) return;
  const form = e.target;
  if (form.dataset.offlineDisabled) return;
  e.preventDefault();
  const data = Object.fromEntries(new FormData(form));
  const method = (data._method || form.method || 'POST').toUpperCase();
  await FarmDB.enqueue(form.action, method, JSON.stringify(data));
  alert('Saved offline! Will sync when connection is restored.');
});

// Flush on coming online
window.addEventListener('online', () => FarmDB.flush());

// Auto-dismiss flash alerts
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.alert[data-auto-dismiss]').forEach(el => {
    setTimeout(() => el.remove(), 4000);
  });
});
