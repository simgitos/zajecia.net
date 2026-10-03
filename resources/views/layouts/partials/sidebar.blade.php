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