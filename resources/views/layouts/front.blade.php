<!doctype html>
<html lang="pl">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- Google Tag Manager -->
  <script>(function (w, d, s, l, i) {
      w[l] = w[l] || []; w[l].push({
        'gtm.start':
          new Date().getTime(), event: 'gtm.js'
      }); var f = d.getElementsByTagName(s)[0],
        j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : ''; j.async = true; j.src =
          'https://www.googletagmanager.com/gtm.js?id=' + i + dl; f.parentNode.insertBefore(j, f);
    })(window, document, 'script', 'dataLayer', 'GTM-TWG3S5S3');</script>
  <!-- End Google Tag Manager -->
  <!-- Primary Meta Tags -->
  <title>@yield('title', 'Zajęciownia.pl - Miejsce dla Twoich Zajęć | Zarządzanie, Zapisy i Płatności Online')</title>
  <meta name="title"
    content="@yield('title', 'Zajęciownia.pl - Miejsce dla Twoich Zajęć | Zarządzanie, Zapisy i Płatności Online')">
  <meta name="description"
    content="@yield('meta_description', 'Kompleksowa platforma do zarządzania zajęciami: tancami, sportem, korepetytorow, sztuką i innymi. Automatyczne rozliczenia, listy obecności i błyskawiczne płatności online dla opiekunów.')">
  <meta name="keywords"
    content="system obsługi zajęć, domy kultury, szkoły tańca, korepetycje, zajęcia sportowe, ewidencja obecności, płatności online za zajęcia, oprogramowanie MDK, zapis na zajęcia">
  <meta name="author" content="Zajęciownia.pl">
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="{{ url()->current() }}" />

  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="{{ url()->current() }}">
  <meta property="og:title" content="@yield('title', 'Zajęciownia.pl - Miejsce dla Twoich Zajęć')">
  <meta property="og:description"
    content="@yield('meta_description', 'Automatyzacja zapisów, ewidencji obecności i płatności online w placówkach. Wypróbuj za darmo!')">
  <meta property="og:image" content="{{ asset('images/hero-mockup.jpg') }}">

  <!-- Twitter Card -->
  <meta property="twitter:card" content="summary_large_image">
  <meta property="twitter:url" content="{{ url()->current() }}">
  <meta property="twitter:title" content="@yield('title', 'EduZajęcia.net - System Obsługi Zajęć Pozalekcyjnych')">
  <meta property="twitter:description"
    content="@yield('meta_description', 'Platforma SaaS dla szkół i rodziców. Zapisy, frekwencja i płatności online w jednym miejscu.')">
  <meta property="twitter:image" content="{{ asset('images/hero-mockup.jpg') }}">

  <!-- Favicon -->
  <link rel="icon" type="image/jpeg" href="{{ asset('images/logo.png') }}">

  <!-- Google Fonts: Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
    rel="stylesheet">

  <!-- CSS Tablera (Bootstrap 5) & Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.6.0/dist/css/tabler.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.48.0/dist/tabler-icons.min.css" />

  <!-- Dynamic Styles & Design System Tokens -->
  <!-- Google Tag Manager -->
  <script>(function (w, d, s, l, i) {
      w[l] = w[l] || []; w[l].push({
        'gtm.start':
          new Date().getTime(), event: 'gtm.js'
      }); var f = d.getElementsByTagName(s)[0],
        j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : ''; j.async = true; j.src =
          'https://www.googletagmanager.com/gtm.js?id=' + i + dl; f.parentNode.insertBefore(j, f);
    })(window, document, 'script', 'dataLayer', 'GTM-TWG3S5S3');</script>
  <!-- End Google Tag Manager -->
  <style>
    :root {
      --primary-color: #3b82f6;
      --primary-dark: #1d4ed8;
      --primary-glow: rgba(59, 130, 246, 0.25);
      --secondary-color: #10b981;
      --dark-navy: #0f172a;
      --surface-card: #ffffff;
      --bg-gradient: linear-gradient(135deg, #f8fafc 0%, #eff6ff 50%, #f0fdf4 100%);
      --font-main: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    body {
      font-family: var(--font-main);
      background: #f8fafc;
      color: #334155;
      overflow-x: hidden;
    }

    /* Navbar Customization */
    .front-navbar {
      background: rgba(255, 255, 255, 0.94);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border-bottom: 1px solid rgba(226, 232, 240, 0.8);
      position: sticky;
      top: 0;
      z-index: 1040;
      transition: all 0.3s ease;
    }

    .front-navbar .nav-link {
      font-weight: 600;
      color: #475569;
      padding: 0.6rem 1rem;
      border-radius: 8px;
      transition: all 0.2s ease;
    }

    .front-navbar .nav-link:hover,
    .front-navbar .nav-link.active {
      color: var(--primary-color);
      background: rgba(59, 130, 246, 0.08);
    }

    .brand-logo-img {
      width: 42px;
      height: 42px;
      border-radius: 10px;
      object-fit: cover;
      box-shadow: 0 4px 12px var(--primary-glow);
    }

    .brand-title {
      font-weight: 800;
      font-size: 1.35rem;
      background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      letter-spacing: -0.5px;
    }

    /* Hero Section Gradient Cards */
    .hero-wrapper {
      position: relative;
      background: var(--bg-gradient);
      padding: 4.5rem 0 5rem;
      overflow: hidden;
    }

    .hero-badge {
      background: rgba(59, 130, 246, 0.1);
      color: #2563eb;
      border: 1px solid rgba(59, 130, 246, 0.2);
      font-weight: 700;
      font-size: 0.85rem;
      padding: 0.4rem 1rem;
      border-radius: 50px;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      margin-bottom: 1.25rem;
    }

    .hero-title {
      font-size: 3rem;
      font-weight: 800;
      line-height: 1.15;
      color: #0f172a;
      letter-spacing: -1px;
    }

    .text-gradient {
      background: linear-gradient(135deg, #2563eb 0%, #10b981 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .hero-image-box {
      position: relative;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 20px 50px rgba(15, 23, 42, 0.12);
      border: 4px solid #ffffff;
      transition: transform 0.4s ease;
    }

    .hero-image-box:hover {
      transform: translateY(-6px);
    }

    /* Feature Cards */
    .feature-card {
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 16px;
      padding: 1.75rem;
      transition: all 0.3s ease;
      height: 100%;
    }

    .feature-card:hover {
      border-color: var(--primary-color);
      box-shadow: 0 12px 30px rgba(59, 130, 246, 0.12);
      transform: translateY(-4px);
    }

    .feature-icon-wrapper {
      width: 56px;
      height: 56px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.75rem;
      margin-bottom: 1.25rem;
    }

    /* Pricing Cards */
    .pricing-card {
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 20px;
      padding: 2.25rem 1.75rem;
      transition: all 0.3s ease;
      position: relative;
      height: 100%;
      display: flex;
      flex-direction: column;
    }

    .pricing-card.featured {
      border: 2px solid var(--primary-color);
      box-shadow: 0 20px 40px var(--primary-glow);
    }

    .pricing-badge {
      position: absolute;
      top: -14px;
      right: 24px;
      background: linear-gradient(135deg, #3b82f6, #10b981);
      color: #ffffff;
      font-weight: 700;
      font-size: 0.75rem;
      padding: 0.3rem 0.9rem;
      border-radius: 50px;
      text-uppercase: uppercase;
      letter-spacing: 0.5px;
    }

    /* FAQ Accordion Styling */
    .front-faq .accordion-item {
      border: 1px solid #e2e8f0;
      border-radius: 12px !important;
      margin-bottom: 0.85rem;
      overflow: hidden;
      background: #ffffff;
    }

    .front-faq .accordion-button {
      font-weight: 700;
      font-size: 1.1rem;
      color: #1e293b;
      padding: 1.2rem 1.5rem;
      background: #ffffff;
    }

    .front-faq .accordion-button:not(.collapsed) {
      color: var(--primary-color);
      background: #f0f9ff;
      box-shadow: none;
    }

    /* CTA Banner */
    .cta-banner {
      background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #1d4ed8 100%);
      border-radius: 24px;
      padding: 4rem 2rem;
      color: #ffffff;
      box-shadow: 0 25px 60px rgba(15, 23, 42, 0.25);
    }

    /* Footer */
    .front-footer {
      background: #0f172a;
      color: #94a3b8;
      padding-top: 4.5rem;
      padding-bottom: 2rem;
      font-size: 0.95rem;
    }

    .front-footer a {
      color: #cbd5e1;
      text-decoration: none;
      transition: color 0.2s ease;
    }

    .front-footer a:hover {
      color: #ffffff;
    }

    @media (max-width: 991.98px) {
      .hero-title {
        font-size: 2.25rem;
      }

      .hero-wrapper {
        padding: 2.5rem 0 3.5rem;
      }
    }
  </style>

  @yield('styles')
</head>

<body>
  <!-- Google Tag Manager (noscript) -->
  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TWG3S5S3" height="0" width="0"
      style="display:none;visibility:hidden"></iframe></noscript>
  <!-- End Google Tag Manager (noscript) -->
  <!-- Header / Navigation -->
  @include('layouts.partials.navbar')

  <!-- Content Slot / Main -->
  <main>
    @yield('content')
  </main>

  <!-- Footer -->
  @include('layouts.partials.footer')

  <!-- Tabler & Bootstrap Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.6.0/dist/js/tabler.min.js" defer></script>
  @yield('scripts')
</body>

</html>