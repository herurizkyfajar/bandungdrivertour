self.addEventListener('install', (event) => {
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(self.clients.claim());
});

self.addEventListener('notificationclick', (event) => {
  const targetUrl = event.notification?.data?.url || '/dashboard';
  event.notification.close();
  event.waitUntil((async () => {
    const allClients = await clients.matchAll({ type: 'window', includeUncontrolled: true });
    if (allClients.length > 0) {
      allClients[0].focus();
      return;
    }
    await clients.openWindow(targetUrl);
  })());
});

self.addEventListener('push', (event) => {
  let data = {};
  try {
    data = event.data ? event.data.json() : {};
  } catch (_) {
    data = {};
  }

  const title = data.title || 'Invoice Baru Masuk';
  const options = {
    body: data.body || 'Ada invoice baru di sistem.',
    badge: '/favicon.ico',
    icon: '/favicon.ico',
    vibrate: [120, 60, 120],
    tag: 'invoice-new',
    data: {
      url: data.url || '/dashboard',
    },
  };

  event.waitUntil(self.registration.showNotification(title, options));
});
