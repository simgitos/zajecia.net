<!-- Pasek boczny (Sidebar) -->
<aside class="navbar navbar-vertical navbar-expand-lg sticky-top" data-bs-theme="dark">
    <div class="container-fluid">

        <button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu"
            aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle sidebar navigation">
            <span class="navbar-toggler-icon"></span>
        </button>


        <div class="navbar-brand navbar-brand-autodark">
            <a href="/"><img src="/images/logo.png" alt="Logo" class="rounded-2 me-2 shadow-xs p-1 bg-light"
                    style="height: 30px;  object-fit: cover;" onerror="this.style.display='none'" ></a>

            <button type="button" class="btn btn-action btn-sm d-none d-lg-inline-flex" data-bs-toggle="sidebar-folded"
                aria-pressed="false" aria-label="Pin sidebar">
                <!-- Download SVG icon from http://tabler.io/icons/icon/pin -->
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    aria-hidden="true" focusable="false" class="icon">
                    <path d="M15 4.5l-4 4l-4 1.5l-1.5 1.5l7 7l1.5 -1.5l1.5 -4l4 -4"></path>
                    <path d="M9 15l-4.5 4.5"></path>
                    <path d="M14.5 4l5.5 5.5"></path>
                </svg>
            </button>


        </div>
        <div class="d-lg-none">
            @include('layouts.partials.topbar')
        </div>
        <nav class="collapse navbar-collapse" id="sidebar-menu" aria-label="Sidebar">
            @auth
                @php
                    $userSchools = Auth::user()->schools;
                    $activeSchool = Auth::user()->activeSchool();
                @endphp

                {{-- Przełącznik placówki / szkoły --}}
                @if($userSchools->count() > 1)
                    <div class="px-3 pt-3 pb-1">
                        <label class="form-label text-secondary small text-uppercase fw-bold mb-1">
                            <i class="ti ti-building me-1"></i> Placówka
                        </label>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary w-100 dropdown-toggle d-flex align-items-center justify-content-between text-start shadow-xs py-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="d-flex align-items-center gap-2 text-truncate">
                                    <i class="ti ti-school text-primary fs-2"></i>
                                    <span class="text-truncate fw-semibold">{{ $activeSchool?->name ?? 'Wybierz placówkę' }}</span>
                                </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end w-100 shadow-sm">
                                <li class="dropdown-header text-uppercase fs-6">Twoje placówki:</li>
                                @foreach($userSchools as $s)
                                    <li>
                                        <form action="{{ route('school.switch') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="school_id" value="{{ $s->id }}">
                                            <button type="submit" class="dropdown-item d-flex align-items-center justify-content-between py-2 {{ $activeSchool?->id === $s->id ? 'active' : '' }}">
                                                <span class="d-flex align-items-center gap-2 text-truncate">
                                                    <i class="ti ti-school text-primary"></i>
                                                    <span class="text-truncate">{{ $s->name }}</span>
                                                </span>
                                                @if($activeSchool?->id === $s->id)
                                                    <i class="ti ti-check text-success ms-2"></i>
                                                @endif
                                            </button>
                                        </form>
                                    </li>
                                @endforeach
                                <li><hr class="dropdown-divider my-1"></li>
                                <!-- <li>
                                    <a href="{{ route('schools.index') }}" class="dropdown-item py-2 text-primary">
                                        <i class="ti ti-plus me-2"></i> Przeglądaj katalog placówek
                                    </a>
                                </li> -->
                            </ul>
                        </div>
                    </div>
                 @elseif($activeSchool)
                 <!--   <div class="px-3 pt-3 pb-1">
                        <div class="card bg-surface border shadow-xs p-2 mb-1">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2 text-truncate me-2">
                                    <span class="avatar avatar-xs bg-primary-lt text-primary rounded">
                                        <i class="ti ti-school"></i>
                                    </span>
                                    <span class="text-truncate fw-medium small" title="{{ $activeSchool->name }}">{{ $activeSchool->name }}</span>
                                </div>
                                 <a href="{{ route('schools.index') }}" class="text-secondary" title="Dołącz do innej placówki">
                                    <i class="ti ti-plus fs-3"></i>
                                </a> 
                            </div>
                        </div>
                    </div>-->
                @endif

                {{-- Przełącznik roli użytkownika --}}
                @if(Auth::user()->hasMultipleRoles())
                    @php
                        $activeRole = Auth::user()->activeRole();
                    @endphp
                    <div class="px-3 pt-2 pb-2">
                        <label class="form-label text-secondary small text-uppercase fw-bold mb-1">
                            <i class="ti ti-switch-horizontal me-1"></i> Aktywna rola
                        </label>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary w-100 dropdown-toggle d-flex align-items-center justify-content-between text-start shadow-xs py-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="d-flex align-items-center gap-2 text-truncate">
                                    <i class="ti {{ $activeRole?->icon() }} text-{{ $activeRole?->color() }} fs-2"></i>
                                    <span class="text-truncate fw-semibold">{{ $activeRole?->label() }}</span>
                                </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end w-100 shadow-sm">
                                <li class="dropdown-header text-uppercase fs-6">Przełącz widok:</li>
                                @foreach(Auth::user()->rolesForActiveSchool() as $userRole)
                                    <li>
                                        <form action="{{ route('role.switch') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="role" value="{{ $userRole->value }}">
                                            <button type="submit" class="dropdown-item d-flex align-items-center justify-content-between py-2 {{ $activeRole?->value === $userRole->value ? 'active' : '' }}">
                                                <span class="d-flex align-items-center gap-2">
                                                    <i class="ti {{ $userRole->icon() }} text-{{ $userRole->color() }}"></i>
                                                    <span>{{ $userRole->label() }}</span>
                                                </span>
                                                @if($activeRole?->value === $userRole->value)
                                                    <i class="ti ti-check text-success"></i>
                                                @endif
                                            </button>
                                        </form>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            @endauth

            <ul class="navbar-nav pt-lg-3">
                <li class="nav-section-title">Przegląd</li>
                <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('dashboard') }}">
                        <span class="nav-link-icon">
                            <i class="ti ti-home"></i>
                        </span>
                        <span class="nav-link-title">Pulpit</span>
                    </a>
                </li>
                @activeRole('admin')
                @include('admin.menu')
                @endactiveRole
                @activeRole('teacher')
                @include('teacher.menu')
                @endactiveRole
                @activeRole('user')
                @include('user.menu')
                @endactiveRole

                @auth
                    @if(Auth::user()->isSuperAdmin())
                        <li class="nav-section-title text-danger fw-bold mt-2">
                            <i class="ti ti-shield-bolt me-1"></i> SuperAdmin (SU)
                        </li>
                        <li class="nav-item {{ request()->routeIs('su.schools.*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('su.schools.index') }}">
                                <span class="nav-link-icon text-danger">
                                    <i class="ti ti-building-community"></i>
                                </span>
                                <span class="nav-link-title">Szkoły</span>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('su.settings.*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('su.settings.index') }}">
                                <span class="nav-link-icon text-danger">
                                    <i class="ti ti-adjustments-alt"></i>
                                </span>
                                <span class="nav-link-title">Ustawienia</span>
                            </a>
                        </li>
                    @endif
                @endauth
            </ul>

        </nav>

    </div>
</aside>