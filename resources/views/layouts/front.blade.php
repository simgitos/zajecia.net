<!doctype html>
<html lang="pl">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Primary Meta Tags -->
  <title>@yield('title', 'Zajęciownia.pl - Miejsce dla Twoich Zajęć | Zarządzanie, Zapisy i Płatności Online')</title>
  <meta name="title" content="@yield('title', 'Zajęciownia.pl - Miejsce dla Twoich Zajęć | Zarządzanie, Zapisy i Płatności Online')">
  <meta name="description" content="@yield('meta_description', 'Kompleksowa platforma do zarządzania zajęciami: tancami, sportem, korepetytorow, sztuką i innymi. Automatyczne rozliczenia, listy obecności i błyskawiczne płatności online dla opiekunów.')">
  <meta name="keywords" content="system obsługi zajęć, domy kultury, szkoły tańca, korepetycje, zajęcia sportowe, ewidencja obecności, płatności online za zajęcia, oprogramowanie MDK, zapis na zajęcia">
  <meta name="author" content="Zajęciownia.pl">
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="{{ url()->current() }}" />

  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="{{ url()->current() }}">
  <meta property="og:title" content="@yield('title', 'Zajęciownia.pl - Miejsce dla Twoich Zajęć')">
  <meta property="og:description" content="@yield('meta_description', 'Automatyzacja zapisów, ewidencji obecności i płatności online w placówkach. Wypróbuj za darmo!')">
  <meta property="og:image" content="{{ asset('images/hero-mockup.jpg') }}">

  <!-- Twitter Card -->
  <meta property="twitter:card" content="summary_large_image">
  <meta property="twitter:url" content="{{ url()->current() }}">
  <meta property="twitter:title" content="@yield('title', 'EduZajęcia.net - System Obsługi Zajęć Pozalekcyjnych')">
  <meta property="twitter:description" content="@yield('meta_description', 'Platforma SaaS dla szkół i rodziców. Zapisy, frekwencja i płatności online w jednym miejscu.')">
  <meta property="twitter:image" content="{{ asset('images/hero-mockup.jpg') }}">

  <!-- Favicon -->
  <link rel="icon" type="image/jpeg" href="{{ asset('images/logo.jpg') }}">

  <!-- Google Fonts: Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- CSS Tablera (Bootstrap 5) & Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.6.0/dist/css/tabler.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.48.0/dist/tabler-icons.min.css" />

  <!-- Dynamic Styles & Design System Tokens -->
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
  <!-- Header / Navigation -->
  <header class="navbar navbar-expand-lg front-navbar">
    <div class="container-xl">
      <!-- Logo & Brand Name -->
      <a class="navbar-brand d-flex align-items-center me-4" href="{{ url('/') }}">
        <img src="{{ asset('images/logo.jpg') }}" alt="Zajęciownia.pl Logo" class="brand-logo-img me-2">
        <div>
          <span class="brand-title d-block">Zajęciownia.pl</span>
          <small class="text-secondary fw-semibold fs-6 d-none d-sm-block">Miejsce dla Twoich Zajęć</small>
        </div>
      </a>

      <!-- Mobile Toggle Button -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Przełącz nawigację">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Nav Links & Auth Buttons -->
      <div class="collapse navbar-collapse" id="navbar-menu">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">
              <i class="ti ti-home me-1"></i> Strona główna
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ url('/#funkcje') }}">
              <i class="ti ti-sparkles me-1"></i> Funkcjonalności
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ url('/#dla-kogo') }}">
              <i class="ti ti-users me-1"></i> Dla Kogo
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->is('schools*') ? 'active' : '' }}" href="{{ route('schools.index') }}">
              <i class="ti ti-building-community me-1"></i> Katalog Placówek
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ url('/#cennik') }}">
              <i class="ti ti-tags me-1"></i> Cennik
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ url('/#faq') }}">
              <i class="ti ti-help-circle me-1"></i> FAQ
            </a>
          </li>
        </ul>

        <!-- Prawa strona: Logowanie / Rejestracja / Panel -->
        <div class="d-flex align-items-center gap-2 mt-2 mt-lg-0">
          @auth
            <div class="nav-item dropdown">
              <a href="#" class="btn btn-outline-primary d-flex align-items-center gap-2 dropdown-toggle" data-bs-toggle="dropdown">
                <span class="avatar avatar-xs bg-primary text-white rounded-circle">
                  {{ mb_substr(Auth::user()->name, 0, 1) }}
                </span>
                <span class="fw-bold">{{ Auth::user()->name }}</span>
              </a>
              <div class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                <div class="dropdown-header">
                  <div class="fw-bold text-dark">{{ Auth::user()->name }}</div>
                  <small class="text-muted">{{ Auth::user()->email }}</small>
                </div>
                <div class="dropdown-divider"></div>
                <a href="{{ route('dashboard') }}" class="dropdown-item">
                  <i class="ti ti-layout-dashboard me-2 text-primary"></i> Mój Panel
                </a>
                @if(Auth::user()->hasRole('admin'))
                  <a href="{{ route('admin.billing.index') }}" class="dropdown-item">
                    <i class="ti ti-receipt-2 me-2 text-success"></i> Rozliczenia (Admin)
                  </a>
                @elseif(Auth::user()->hasRole('user'))
                  <a href="{{ route('user.payments.index') }}" class="dropdown-item">
                    <i class="ti ti-wallet me-2 text-warning"></i> Płatności i Zapisy
                  </a>
                @endif
                <a href="{{ route('profile.edit') }}" class="dropdown-item">
                  <i class="ti ti-settings me-2 text-secondary"></i> Ustawienia profilu
                </a>
                <div class="dropdown-divider"></div>
                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <button type="submit" class="dropdown-item text-danger">
                    <i class="ti ti-logout me-2"></i> Wyloguj się
                  </button>
                </form>
              </div>
            </div>
          @else
            <a href="{{ route('login') }}" class="btn btn-ghost-primary fw-semibold d-inline-flex align-items-center">
              <i class="ti ti-login me-1 fs-3"></i> Zaloguj się
            </a>
            <a href="{{ route('register') }}" class="btn btn-primary fw-bold shadow-sm d-inline-flex align-items-center">
              <i class="ti ti-user-plus me-1 fs-3"></i> Zarejestruj się
            </a>
          @endauth
        </div>
      </div>
    </div>
  </header>

  <!-- Content Slot / Main -->
  <main>
    @yield('content')
  </main>

  <!-- Footer -->
  <footer class="front-footer">
    <div class="container-xl">
      <div class="row g-4 mb-5">
        <!-- O Nas & Logo -->
        <div class="col-lg-4">
          <div class="d-flex align-items-center mb-3">
            <img src="{{ asset('images/logo.jpg') }}" alt="Zajęciownia.pl" class="brand-logo-img me-2">
            <span class="fs-2 fw-extrabold text-white">Zajęciownia.pl</span>
          </div>
          <p class="text-secondary leading-relaxed mb-4">
          Platforma do obsługi zajęć dowolnego rodzaju: taniec, sport, sztuka, korepetycje, MDK i inne. Zapisy, ewidencja obecności i płatności online w jednym miejscu.
          </p>
          <div class="d-flex gap-2 flex-wrap">
            <span class="badge bg-blue-subtle text-primary p-2 rounded-2"><i class="ti ti-shield-check me-1"></i> Certyfikat SSL 256-bit</span>
            <span class="badge bg-green-subtle text-success p-2 rounded-2"><i class="ti ti-currency-zloty me-1"></i> Płatności BLIK & PayU</span>
          </div>
        </div>

        <!-- Szybkie Linki -->
        <div class="col-6 col-lg-2 ms-auto">
          <h5 class="text-white fw-bold mb-3">Nawigacja</h5>
          <ul class="list-unstyled d-flex flex-column gap-2 mb-0">
            <li><a href="{{ url('/') }}"><i class="ti ti-chevron-right me-1 fs-6"></i> Strona Główna</a></li>
            <li><a href="{{ url('/#funkcje') }}"><i class="ti ti-chevron-right me-1 fs-6"></i> Funkcjonalności</a></li>
            <li><a href="{{ url('/#dla-kogo') }}"><i class="ti ti-chevron-right me-1 fs-6"></i> Dla Kogo</a></li>
            <li><a href="{{ route('schools.index') }}"><i class="ti ti-chevron-right me-1 fs-6"></i> Katalog Placówek</a></li>
            <li><a href="{{ url('/#cennik') }}"><i class="ti ti-chevron-right me-1 fs-6"></i> Cennik</a></li>
            <li><a href="{{ url('/#faq') }}"><i class="ti ti-chevron-right me-1 fs-6"></i> Pytania i Odpowiedzi</a></li>
          </ul>
        </div>

        <!-- Moduły Systemu -->
        <div class="col-6 col-lg-3">
          <h5 class="text-white fw-bold mb-3">Moduły Platformy</h5>
          <ul class="list-unstyled d-flex flex-column gap-2 mb-0">
            <li><a href="{{ route('login') }}"><i class="ti ti-device-laptop me-1"></i> Panel Administratora</a></li>
            <li><a href="{{ route('login') }}"><i class="ti ti-user-check me-1"></i> Panel Prowadzącego</a></li>
            <li><a href="{{ route('login') }}"><i class="ti ti-wallet me-1"></i> Portal Opiekuna & Płatności</a></li>
            <li><a href="{{ route('register') }}"><i class="ti ti-plus me-1"></i> Zarejestruj placówkę</a></li>
          </ul>
        </div>

        <!-- Kontakt & Pomoc -->
        <div class="col-lg-3">
          <h5 class="text-white fw-bold mb-3">Kontakt & Wsparcie</h5>
          <ul class="list-unstyled d-flex flex-column gap-2 mb-3 text-secondary">
            <li><i class="ti ti-mail me-2 text-primary"></i> pomoc@zajeciownia.pl</li>
            <li><i class="ti ti-phone me-2 text-success"></i> +48 22 123 45 67 (Pn-Pt 9:00 - 17:00)</li>
            <li><i class="ti ti-map-pin me-2 text-warning"></i> ul. Aktywności 1, Warszawa</li>
          </ul>
          <div class="d-flex gap-2">
            <a href="#" class="btn btn-icon btn-dark text-white rounded-circle"><i class="ti ti-brand-facebook fs-2"></i></a>
            <a href="#" class="btn btn-icon btn-dark text-white rounded-circle"><i class="ti ti-brand-linkedin fs-2"></i></a>
            <a href="#" class="btn btn-icon btn-dark text-white rounded-circle"><i class="ti ti-brand-youtube fs-2"></i></a>
          </div>
        </div>
      </div>

      <div class="border-top border-slate-800 pt-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
        <div class="text-secondary small mb-0">
          &copy; {{ date('Y') }} Zajęciownia.pl. Wszelkie prawa zastrzeżone. Platforma dla domów kultury, szkół tańca, sportów, korepetycji i innych zajęć.
        </div>
        <div class="d-flex gap-3 small">
          <a href="#">Polityka prywatności</a>
          <a href="#">Regulamin serwisu</a>
          <a href="#">RODO</a>
        </div>
      </div>
    </div>
  </footer>

  <!-- Tabler & Bootstrap Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.6.0/dist/js/tabler.min.js" defer></script>
  @yield('scripts')
</body>

</html>