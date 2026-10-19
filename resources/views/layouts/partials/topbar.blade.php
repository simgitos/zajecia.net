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
                @php
                    $unreadCount = Auth::user()->unreadNotifications()->count();
                    $recentNotifications = Auth::user()->notifications()->latest()->take(5)->get();
                @endphp
                {{-- Dynamiczna ikona powiadomień --}}
                <div class="nav-item dropdown me-2 me-md-3">
                    <a href="#" class="nav-link px-0 d-flex align-items-center position-relative" data-bs-toggle="dropdown" tabindex="-1" aria-label="Powiadomienia" aria-expanded="false">
                        <i class="ti ti-bell fs-2 text-secondary"></i>
                        @if($unreadCount > 0)
                            <span class="badge bg-red text-red-fg badge-pill position-absolute top-0 end-0 translate-middle-y fs-6 px-1.5 py-0.5 shadow-sm" style="font-size: 0.7rem; min-width: 18px; line-height: 1.2;">
                                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                            </span>
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end shadow-lg py-0" style="width: 360px; max-width: 92vw;">
                        <div class="card border-0 mb-0">
                            <div class="card-header d-flex justify-content-between align-items-center py-2.5 bg-surface border-bottom">
                                <h3 class="card-title fw-bold fs-4 mb-0 d-flex align-items-center">
                                    <i class="ti ti-bell me-1.5 text-primary"></i> Powiadomienia
                                </h3>
                                @if($unreadCount > 0)
                                    <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-link btn-sm text-muted text-decoration-none p-0 fs-6">
                                            <i class="ti ti-check-double me-1"></i>Oznacz wszystkie
                                        </button>
                                    </form>
                                @endif
                            </div>
                            <div class="list-group list-group-flush list-group-hoverable" style="max-height: 320px; overflow-y: auto;">
                                @forelse($recentNotifications as $notification)
                                    @php
                                        $data = $notification->data;
                                        $type = $data['type'] ?? 'info';
                                        $isUnread = is_null($notification->read_at);
                                        $statusClass = match($type) {
                                            'warning' => 'bg-warning',
                                            'danger'  => 'bg-danger',
                                            'success' => 'bg-success',
                                            default   => 'bg-azure',
                                        };
                                    @endphp
                                    <a href="{{ route('notifications.go', $notification->id) }}" 
                                       class="list-group-item list-group-item-action p-2.5 text-reset {{ $isUnread ? 'bg-primary-lt-subtle border-start border-3 border-primary' : '' }}">
                                        <div class="row align-items-start g-2">
                                            <div class="col-auto pt-1">
                                                <span class="status-dot {{ $isUnread ? 'status-dot-animated' : '' }} {{ $statusClass }} d-block"></span>
                                            </div>
                                            <div class="col text-truncate">
                                                <div class="d-flex justify-content-between align-items-center mb-0.5">
                                                    <span class="d-block text-truncate {{ $isUnread ? 'fw-bold text-dark' : 'text-body' }} fs-5">
                                                        {{ $data['title'] ?? 'Nowe powiadomienie' }}
                                                    </span>
                                                    <span class="badge text-secondary bg-transparent p-0 fs-6 flex-shrink-0 ms-1">
                                                        {{ $notification->created_at->diffForHumans() }}
                                                    </span>
                                                </div>
                                                <div class="text-secondary small text-truncate">
                                                    {{ Str::limit($data['message'] ?? '', 75) }}
                                                </div>
                                                @if(!empty($data['sender_name']))
                                                    <div class="text-muted extra-small mt-0.5" style="font-size: 0.72rem;">
                                                        <i class="ti ti-user me-0.5"></i>{{ $data['sender_name'] }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="p-4 text-center text-muted">
                                        <i class="ti ti-bell-off fs-1 d-block mb-1 opacity-40"></i>
                                        <div class="small">Brak nowych powiadomień</div>
                                    </div>
                                @endforelse
                            </div>
                            <div class="card-footer text-center py-2 bg-surface border-top">
                                <a href="{{ route('notifications.index') }}" class="btn btn-outline-primary btn-sm w-100">
                                    <i class="ti ti-list me-1"></i> Wszystkie powiadomienia
                                </a>
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