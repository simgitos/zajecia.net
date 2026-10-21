<header class="navbar navbar-expand-lg front-navbar">
    <div class="container-xl">
        <!-- Logo & Brand Name -->
        <a class="navbar-brand d-flex align-items-center me-auto me-lg-4" href="{{ url('/') }}">
            <img src="{{ asset('images/logo.png') }}" alt="Zajęciownia.pl Logo" class="navbar-brand me-1"
                style="width: 40px">
            <div>
                <span class="brand-title d-block">Zajęciownia.pl</span>
                <small class="text-secondary fw-semibold fs-6 d-none d-sm-block">Miejsce dla Twoich Zajęć</small>
            </div>
        </a>


        <div class="d-flex align-items-center gap-2 d-lg-none">

            <!-- Mobile Toggle Button (Hamburger) -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu"
                aria-controls="navbar-menu" aria-expanded="false" aria-label="Przełącz nawigację">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- Prawa strona w widoku mobilnym: ikona profilu / logowania obok hamburgera --> @auth
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link p-0 text-reset d-flex align-items-center" data-bs-toggle="dropdown"
                        aria-label="Menu profilu" title="{{ Auth::user()->name }}">
                        <span class="avatar avatar-sm bg-primary text-white rounded-circle shadow-xs">
                            <i class="ti ti-user fs-2"></i>
                        </span>
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
                <a href="{{ route('login') }}" class="nav-link p-0 text-reset d-flex align-items-center" title="Zaloguj się"
                    aria-label="Zaloguj się">
                    <span class="avatar avatar-sm bg-primary text-white rounded-circle shadow-xs">
                            <i class="ti ti-user fs-2"></i>
                        </span>
                </a>
            @endauth


        </div>

        <!-- Nav Links & Auth Buttons (Desktop & Collapsed Mobile) -->
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
                    <a class="nav-link {{ request()->is('schools*') ? 'active' : '' }}" href="#">
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

            <!-- Prawa strona na Desktopie -->
            <div class="d-none d-lg-flex align-items-center gap-2 mt-2 mt-lg-0">
                @auth
                    <div class="nav-item dropdown">
                        <a href="#" class="btn btn-outline-primary d-flex align-items-center gap-2 dropdown-toggle"
                            data-bs-toggle="dropdown">
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
                    <a href="{{ route('login') }}"
                        class="btn btn-primary fw-semibold d-inline-flex align-items-center">
                        <i class="ti ti-login me-1 fs-3"></i> Zaloguj się
                    </a>
                @endauth
            </div>
        </div>
    </div>
</header>