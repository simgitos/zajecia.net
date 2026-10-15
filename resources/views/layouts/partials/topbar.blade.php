<header class="navbar navbar-expand-md d-print-none  bg-surface shadow-xs">
    <div class="container-xl">
        {{-- Lewa strona: Hamburger + Logo --}}
        <div class="d-flex align-items-center me-2">
            <!-- <button class="navbar-toggler me-2 p-1 border-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#sidebar-folded" aria-controls="sidebar-folded" aria-expanded="false"
                aria-label="Menu nawigacji">
                <span class="navbar-toggler-icon"></span>
            </button> -->

        </div>

        {{-- Spacer --}}
        <div class="flex-fill"></div>

        {{-- Prawa strona: Ikona Powiadomień + Theme Switcher + User Dropdown --}}
        <div class="navbar-nav flex-row order-md-last align-items-center">

            @auth
                {{-- Ikona powiadomień z powiadomieniami --}}
                <div class="nav-item dropdown me-2 me-md-3">
                    <a href="#" class="nav-link px-0 d-flex align-items-center position-relative" data-bs-toggle="dropdown" tabindex="-1" aria-label="Powiadomienia">
                        <i class="ti ti-bell fs-2 text-secondary"></i>
                        <span class="badge bg-red badge-blink position-absolute top-0 end-0 translate-middle-y rounded-circle p-1"></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end shadow-lg py-0" style="width: 300px; max-width: 90vw;">
                        <div class="card border-0">
                            <div class="card-header d-flex justify-content-between align-items-center py-2 bg-surface">
                                <h3 class="card-title fw-bold fs-4 mb-0"><i class="ti ti-bell me-1 text-primary"></i> Powiadomienia</h3>
                                <span class="badge bg-primary-lt">Nowe</span>
                            </div>
                            <div class="list-group list-group-flush list-group-hoverable" style="max-height: 240px; overflow-y: auto;">
                                <div class="list-group-item p-2.5">
                                    <div class="row align-items-center">
                                        <div class="col-auto"><span class="status-dot status-dot-animated bg-success d-block"></span></div>
                                        <div class="col text-truncate">
                                            <a href="{{ route('dashboard') }}" class="text-body d-block fw-bold fs-5">System gotowy do użycia</a>
                                            <div class="text-secondary small text-truncate">Witaj w panelu Zajęciownia.pl</div>
                                        </div>
                                    </div>
                                </div>
                                @if(Auth::user()->hasRole('user'))
                                <div class="list-group-item p-2.5">
                                    <div class="row align-items-center">
                                        <div class="col-auto"><span class="status-dot bg-warning d-block"></span></div>
                                        <div class="col text-truncate">
                                            <a href="{{ route('user.payments.index') }}" class="text-body d-block fw-bold fs-5">Stan rozliczeń</a>
                                            <div class="text-secondary small text-truncate">Sprawdź opłaty za zajęcia</div>
                                        </div>
                                    </div>
                                </div>
                                @elseif(Auth::user()->hasRole('teacher'))
                                <div class="list-group-item p-2.5">
                                    <div class="row align-items-center">
                                        <div class="col-auto"><span class="status-dot bg-azure d-block"></span></div>
                                        <div class="col text-truncate">
                                            <a href="{{ route('teacher.courses.index') }}" class="text-body d-block fw-bold fs-5">Moje zajęcia</a>
                                            <div class="text-secondary small text-truncate">Lista zajęć i ewidencja obecności</div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endauth

            {{-- Theme Switcher --}}
            <div class="nav-item me-2 me-md-3">
                <a href="#" class="nav-link px-0 d-flex align-items-center" id="theme-toggle"
                    data-bs-toggle="tooltip" data-bs-placement="bottom" title="Zmień motyw">
                    <i class="ti ti-sun fs-2 d-none" id="theme-icon-light"></i>
                    <i class="ti ti-moon fs-2 d-none" id="theme-icon-dark"></i>
                </a>
            </div>

            {{-- Separator --}}
            <div class="vr d-none d-md-block mx-2 opacity-25"></div>

            @auth
                @php
                    $topbarSchool = Auth::user()->activeSchool();
                    $topbarSchools = Auth::user()->schools;
                @endphp

                @if($topbarSchool)
                    <div class="nav-item d-none d-sm-flex align-items-center me-2">
                        <span class="badge bg-primary-lt text-primary px-2.5 py-1.5 fs-4 fw-semibold border border-primary-subtle rounded-pill">
                            <i class="ti ti-school me-1 fs-3"></i> {{ $topbarSchool->name }}
                        </span>
                    </div>
                @endif

                {{-- User dropdown --}}
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link d-flex lh-1 text-reset p-0 px-1 px-md-2" data-bs-toggle="dropdown"
                        aria-label="Menu użytkownika" aria-expanded="false">
                        <span class="avatar avatar-sm rounded-circle shadow-xs"
                            style="background-image: url('https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=206bc4&color=fff&size=64')">
                        </span>
                        <div class="d-none d-md-block ps-2">
                            <div class="fw-medium">{{ Auth::user()->name }}</div>
                            <div class="mt-1 small text-secondary">
                                {{ Auth::user()->activeRole()?->label() ?? 'Użytkownik' }}
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
                        @if(Auth::user()->hasMultipleRoles())
                            <div class="dropdown-divider"></div>
                            <span class="dropdown-header">Przełącz rolę</span>
                            @foreach(Auth::user()->rolesForActiveSchool() as $userRole)
                                <form action="{{ route('role.switch') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="role" value="{{ $userRole->value }}">
                                    <button type="submit" class="dropdown-item d-flex align-items-center justify-content-between {{ Auth::user()->activeRole()?->value === $userRole->value ? 'active' : '' }}">
                                        <span>
                                            <i class="ti {{ $userRole->icon() }} me-2 text-{{ $userRole->color() }}"></i>{{ $userRole->label() }}
                                        </span>
                                        @if(Auth::user()->activeRole()?->value === $userRole->value)
                                            <i class="ti ti-check text-success"></i>
                                        @endif
                                    </button>
                                </form>
                            @endforeach
                        @endif
                        @if($topbarSchools->count() > 1)
                            <div class="dropdown-divider"></div>
                            <span class="dropdown-header">Przełącz placówkę</span>
                            @foreach($topbarSchools as $s)
                                <form action="{{ route('school.switch') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="school_id" value="{{ $s->id }}">
                                    <button type="submit" class="dropdown-item d-flex align-items-center justify-content-between {{ $topbarSchool?->id === $s->id ? 'active' : '' }}">
                                        <span class="text-truncate me-2">
                                            <i class="ti ti-school me-2 text-primary"></i>{{ $s->name }}
                                        </span>
                                        @if($topbarSchool?->id === $s->id)
                                            <i class="ti ti-check text-success"></i>
                                        @endif
                                    </button>
                                </form>
                            @endforeach
                        @endif
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('schools.index') }}" class="dropdown-item">
                            <i class="ti ti-search me-2 text-secondary"></i>Katalog placówek
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

        updateIcons();

        const observer = new MutationObserver(function () {
            updateIcons();
        });
        observer.observe(html, { attributes: true, attributeFilter: ['data-bs-theme'] });

        if (toggle) {
            toggle.addEventListener('click', function (e) {
                e.preventDefault();
                const current = html.getAttribute('data-bs-theme') || 'light';
                const next = current === 'dark' ? 'light' : 'dark';
                html.setAttribute('data-bs-theme', next);
                localStorage.setItem('tablerTheme', next);
            });
        }

        const saved = localStorage.getItem('tablerTheme');
        if (saved) {
            html.setAttribute('data-bs-theme', saved);
        }
    });
</script>