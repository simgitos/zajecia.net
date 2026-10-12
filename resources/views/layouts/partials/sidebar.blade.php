<!-- Pasek boczny (Sidebar) -->
<aside class="navbar navbar-vertical navbar-expand-lg sticky-top">
    <div class="container-fluid">

        <button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu"
            aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle sidebar navigation">
            <span class="navbar-toggler-icon"></span>
        </button>


        <div class="navbar-brand navbar-brand-autodark">
            <a href="/"><img src="/images/logo.png" alt="Logo" class="rounded-2 me-2 shadow-xs"
                    style="height: 30px;  object-fit: cover;" onerror="this.style.display='none'"></a>

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
                @if(Auth::user()->hasMultipleRoles())
                    @php
                        $activeRole = Auth::user()->activeRole();
                    @endphp
                    <div class="px-3 pt-3 pb-2">
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
                                @foreach(Auth::user()->roles as $userRole)
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
            </ul>

        </nav>

    </div>
</aside>