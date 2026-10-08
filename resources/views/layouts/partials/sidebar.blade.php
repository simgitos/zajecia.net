<!-- Pasek boczny (Sidebar) -->
<aside class="navbar navbar-vertical navbar-expand-lg navbar-folded-hover">
    <div class="container-fluid">
        <h1 class="navbar-brand navbar-brand-autodark">
            <a href="/">
                <i class="ti ti-school fs-1 me-2"></i>
                <span class="d-none d-lg-inline">Zajęciownia.pl</span>
            </a>
        </h1>
        <div class="collapse navbar-collapse" id="sidebar-folded">
            <ul class="navbar-nav pt-lg-3">
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
        </div>
    </div>
</aside>