<!-- PWA Meta Tags & Manifest -->
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#206bc4">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="Zajęciownia">
<link rel="apple-touch-icon" href="/icons/icon-192x192.png">
<link rel="apple-touch-icon" sizes="152x152" href="/icons/icon-152x152.png">
<link rel="apple-touch-icon" sizes="192x192" href="/icons/icon-192x192.png">
<link rel="icon" type="image/png" sizes="192x192" href="/icons/icon-192x192.png">

<!-- PWA Service Worker Registration -->
<script>
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
      navigator.serviceWorker.register('/sw.js')
        .then((reg) => {
          console.log('[PWA] Service Worker zarejestrowany pomyślnie. Zakres:', reg.scope);
        })
        .catch((err) => {
          console.warn('[PWA] Rejestracja Service Workera nie powiodła się:', err);
        });
    });
  }
</script>
