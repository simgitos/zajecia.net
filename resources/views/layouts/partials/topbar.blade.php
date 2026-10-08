<header class="navbar navbar-expand-md d-print-none">
    <div class="container-xl">
        {{-- Mobile: hamburger do sidebara --}}
        <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse"
            data-bs-target="#sidebar-folded" aria-controls="sidebar-folded" aria-expanded="false"
            aria-label="Menu nawigacji">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- Spacer --}}
        <div class="flex-fill"></div>

        {{-- Prawa strona: theme switcher + user dropdown --}}
        <div class="navbar-nav flex-row order-md-last align-items-center">

            {{-- Theme Switcher --}}
            <div class="nav-item me-2">
                <a href="#" class="nav-link px-0 d-flex align-items-center" id="theme-toggle"
                    data-bs-toggle="tooltip" data-bs-placement="bottom" title="Zmień motyw">
                    {{-- Ikona słońca (light mode) --}}
                    <i class="ti ti-sun fs-2 d-none" id="theme-icon-light"></i>
                    {{-- Ikona księżyca (dark mode) --}}
                    <i class="ti ti-moon fs-2 d-none" id="theme-icon-dark"></i>
                </a>
            </div>

            {{-- Separator --}}
            <div class="vr d-none d-md-block mx-2 opacity-25"></div>

            @auth
                {{-- User dropdown --}}
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link d-flex lh-1 text-reset p-0 px-2" data-bs-toggle="dropdown"
                        aria-label="Menu użytkownika" aria-expanded="false">
                        <span class="avatar avatar-sm rounded-circle"
                            style="background-image: url('https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=206bc4&color=fff&size=64')">
                        </span>
                        <div class="d-none d-md-block ps-2">
                            <div class="fw-medium">{{ Auth::user()->name }}</div>
                            <div class="mt-1 small text-secondary">
                                @if(Auth::user()->hasRole('admin'))
                                    Administrator
                                @elseif(Auth::user()->hasRole('teacher'))
                                    Instruktor
                                @else
                                    Użytkownik
                                @endif
                            </div>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                        <a href="{{ route('dashboard') }}" class="dropdown-item">
                            <i class="ti ti-layout-dashboard me-2"></i>Pulpit
                        </a>
                        <a href="{{ route('profile.edit') }}" class="dropdown-item">
                            <i class="ti ti-user me-2"></i>Mój profil
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="ti ti-logout me-2"></i>Wyloguj się
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="nav-item d-flex align-items-center gap-2">
                    <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm">
                        <i class="ti ti-login me-1"></i>Zaloguj się
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">
                        Zarejestruj się
                    </a>
                </div>
            @endauth
        </div>
    </div>
</header>

{{-- Theme Switcher Script --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggle = document.getElementById('theme-toggle');
        const iconLight = document.getElementById('theme-icon-light');
        const iconDark = document.getElementById('theme-icon-dark');
        const html = document.documentElement;

        function updateIcons() {
            const theme = html.getAttribute('data-bs-theme') || 'light';
            if (theme === 'dark') {
                iconLight.classList.remove('d-none');
                iconDark.classList.add('d-none');
            } else {
                iconLight.classList.add('d-none');
                iconDark.classList.remove('d-none');
            }
        }

        // Inicjalizacja
        updateIcons();

        // Obserwowanie zmian atrybutu (kompatybilność z tabler-theme.min.js)
        const observer = new MutationObserver(function () {
            updateIcons();
        });
        observer.observe(html, { attributes: true, attributeFilter: ['data-bs-theme'] });

        // Kliknięcie: przełącz motyw
        toggle.addEventListener('click', function (e) {
            e.preventDefault();
            const current = html.getAttribute('data-bs-theme') || 'light';
            const next = current === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-bs-theme', next);
            localStorage.setItem('tablerTheme', next);
        });

        // Odczytaj zapisany motyw
        const saved = localStorage.getItem('tablerTheme');
        if (saved) {
            html.setAttribute('data-bs-theme', saved);
        }
    });
</script>