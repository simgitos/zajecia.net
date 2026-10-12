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
                @role('admin')
                @include('admin.menu')
                @endrole
                @role('teacher')
                @include('teacher.menu')
                @endrole
                @role('user')
                @include('user.menu')
                @endrole
            </ul>

        </nav>

    </div>
</aside>