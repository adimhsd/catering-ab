/**
 * Service Worker — Catering Al-Bahjah PWA
 * Strategi: Cache First untuk assets statis, Network First untuk HTML/API
 */

const CACHE_NAME = 'catering-ab-v1';
const STATIC_CACHE = 'catering-ab-static-v1';

// Assets yang di-pre-cache saat install
const PRECACHE_URLS = [
    '/',
    '/dashboard',
    '/offline',
    '/manifest.json',
    '/icons/icon-192.png',
    '/icons/icon-512.png',
];

// ===== Install Event =====
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE).then((cache) => {
            return cache.addAll(PRECACHE_URLS).catch(() => {
                // Abaikan error pre-cache (mis. halaman yang membutuhkan auth)
                console.log('[SW] Pre-cache partial — some pages need auth');
            });
        })
    );
    // Langsung aktifkan tanpa menunggu tab lama tutup
    self.skipWaiting();
});

// ===== Activate Event =====
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames
                    .filter((name) => name !== CACHE_NAME && name !== STATIC_CACHE)
                    .map((name) => caches.delete(name))
            );
        })
    );
    // Ambil alih semua client yang aktif
    self.clients.claim();
});

// ===== Fetch Event =====
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip cross-origin requests
    if (url.origin !== location.origin) return;

    // Skip POST/PUT/DELETE/PATCH — jangan cache mutating requests
    if (request.method !== 'GET') return;

    // Skip Livewire AJAX requests
    if (url.pathname.startsWith('/livewire')) return;

    // Static assets (CSS, JS, images, fonts) — Cache First
    if (
        url.pathname.startsWith('/build/') ||
        url.pathname.startsWith('/storage/') ||
        url.pathname.startsWith('/icons/') ||
        /\.(css|js|png|jpg|jpeg|svg|woff2?|ttf)$/.test(url.pathname)
    ) {
        event.respondWith(
            caches.match(request).then((cachedResponse) => {
                if (cachedResponse) return cachedResponse;
                return fetch(request).then((response) => {
                    if (response.ok) {
                        const clone = response.clone();
                        caches.open(STATIC_CACHE).then((cache) => cache.put(request, clone));
                    }
                    return response;
                });
            })
        );
        return;
    }

    // HTML Pages — Network First, fallback ke cache, lalu offline page
    event.respondWith(
        fetch(request)
            .then((response) => {
                if (response.ok) {
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then((cache) => cache.put(request, clone));
                }
                return response;
            })
            .catch(() => {
                return caches.match(request).then((cachedResponse) => {
                    if (cachedResponse) return cachedResponse;
                    // Fallback ke halaman offline jika ada
                    return caches.match('/offline');
                });
            })
    );
});
