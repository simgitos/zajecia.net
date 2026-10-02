<!-- Pasek boczny (Sidebar) -->
<aside class="navbar navbar-vertical navbar-expand-lg navbar-folded-hover">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-folded"
            aria-controls="sidebar-folded" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <h1 class="navbar-brand navbar-brand-autodark">
            <a href="/">
                a
            </a>
        </h1>
        <div class="collapse navbar-collapse" id="sidebar-folded">
            <ul class="navbar-nav pt-lg-3">
                <li class="nav-item active">
                    <a class="nav-link" href="{{route('dashboard')}}" aria-current="page">
                        <span class="nav-link-icon">
                            <i class="ti ti-home"></i>
                        </span>
                        <span class="nav-link-title">Home</span>
                    </a>
                </li>
                @role('admin')
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                            role="button" aria-haspopup="true" aria-expanded="false">
                            <span class="nav-link-icon">
                                <i class="ti ti-user"></i>
                            </span>
                            <span class="nav-link-title">Reports</span>
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#">Overview</a>
                            <a class="dropdown-item" href="#">Sales</a>
                            <a class="dropdown-item" href="#">Traffic</a>
                            <a class="dropdown-item" href="?theme=dark">Tryb ciemny</a>
                            <a class="dropdown-item" href="?theme=light">Tryb jasny</a>
                        </div>
                    </li>
                @endrole

                <li class="nav-item">
                    <a class="nav-link" href="{{route('users.index')}}">
                        <span class="nav-link-icon">
                            <i class="ti ti-users"></i>
                        </span>
                        <span class="nav-link-title">Użytkownicy</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</aside>