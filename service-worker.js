/**
 * AgroSmart Market Service Worker
 * Provides offline functionality and caching for the platform
 */

// Cache names
const STATIC_CACHE_NAME = 'agromart-static-v1';
const DYNAMIC_CACHE_NAME = 'agromart-dynamic-v1';
const IMAGE_CACHE_NAME = 'agromart-images-v1';

// Resources to cache initially
const STATIC_ASSETS = [
  '/',
  '/index.php',
  '/marketplace.php',
  '/buyer-dashboard.php',
  '/cart.php',
  '/public/css/bootstrap.min.css',
  '/public/css/enhanced-dashboard.css',
  '/public/js/bootstrap.bundle.min.js',
  '/public/js/accessibility.js',
  '/public/img/default-product.jpg',
  '/public/img/logo.png',
  '/public/img/offline-banner.svg',
  '/offline.php'
];

// Install event - cache static assets
self.addEventListener('install', event => {
  console.log('[Service Worker] Installing');
  
  event.waitUntil(
    caches.open(STATIC_CACHE_NAME)
      .then(cache => {
        console.log('[Service Worker] Caching static assets');
        return cache.addAll(STATIC_ASSETS);
      })
      .then(() => {
        console.log('[Service Worker] Installation complete');
        return self.skipWaiting();
      })
  );
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
  console.log('[Service Worker] Activating');
  
  event.waitUntil(
    caches.keys()
      .then(keyList => {
        return Promise.all(
          keyList.map(key => {
            if (key !== STATIC_CACHE_NAME && key !== DYNAMIC_CACHE_NAME && key !== IMAGE_CACHE_NAME) {
              console.log('[Service Worker] Removing old cache', key);
              return caches.delete(key);
            }
          })
        );
      })
      .then(() => {
        console.log('[Service Worker] Activation complete');
        return self.clients.claim();
      })
  );
});

// Fetch event - serve from cache or network
self.addEventListener('fetch', event => {
  const requestUrl = new URL(event.request.url);
  
  // Handle API requests - network only with timeout fallback
  if (requestUrl.pathname.includes('api/') || event.request.method !== 'GET') {
    event.respondWith(
      fetch(event.request)
        .catch(error => {
          console.log('[Service Worker] Network request failed, returning offline page for API', error);
          return caches.match('/offline.php');
        })
    );
    return;
  }
  
  // Handle image requests with specific cache
  if (requestUrl.pathname.includes('/uploads/') || 
      requestUrl.pathname.includes('.jpg') || 
      requestUrl.pathname.includes('.png') || 
      requestUrl.pathname.includes('.svg') || 
      requestUrl.pathname.includes('.webp')) {
    event.respondWith(
      caches.match(event.request)
        .then(cachedResponse => {
          if (cachedResponse) {
            return cachedResponse;
          }
          
          return fetch(event.request)
            .then(networkResponse => {
              // Cache a copy of the response
              let responseToCache = networkResponse.clone();
              
              caches.open(IMAGE_CACHE_NAME)
                .then(cache => {
                  cache.put(event.request, responseToCache);
                });
              
              return networkResponse;
            })
            .catch(error => {
              console.log('[Service Worker] Failed to fetch image', error);
              // Return default image placeholder
              return caches.match('/public/img/default-product.jpg');
            });
        })
    );
    return;
  }
  
  // For all other requests - stale-while-revalidate strategy
  event.respondWith(
    caches.match(event.request)
      .then(cachedResponse => {
        // Return cached response if found
        const fetchPromise = fetch(event.request)
          .then(networkResponse => {
            // Cache latest version in dynamic cache
            caches.open(DYNAMIC_CACHE_NAME)
              .then(cache => {
                cache.put(event.request, networkResponse.clone());
              });
            
            return networkResponse;
          })
          .catch(error => {
            console.log('[Service Worker] Network fetch failed', error);
            
            // If it's a page request, return the offline page
            if (event.request.headers.get('accept').includes('text/html')) {
              return caches.match('/offline.php');
            }
            
            return null;
          });
        
        return cachedResponse || fetchPromise;
      })
  );
});

// Background sync for offline form submissions
self.addEventListener('sync', event => {
  console.log('[Service Worker] Background Sync', event.tag);
  
  if (event.tag === 'sync-pending-actions') {
    event.waitUntil(
      syncPendingActions()
    );
  }
});

// Function to sync pending actions
async function syncPendingActions() {
  try {
    // Get all clients
    const clients = await self.clients.matchAll();
    
    // Request pending data from a client
    if (clients && clients.length > 0) {
      // Send message to client to get pending data
      clients[0].postMessage({
        type: 'GET_PENDING_ACTIONS'
      });
      
      // The client will process this and call the API endpoints as needed
      console.log('[Service Worker] Requested pending actions from client');
    }
  } catch (error) {
    console.error('[Service Worker] Error syncing pending actions:', error);
  }
}

// Listen for messages from clients
self.addEventListener('message', event => {
  if (event.data && event.data.type === 'PENDING_ACTIONS') {
    const pendingActions = event.data.pendingActions;
    
    console.log('[Service Worker] Received pending actions', pendingActions);
    
    // Process each type of pending action
    // The actual processing happens in the client since we need access to CSRF tokens
  }
});
