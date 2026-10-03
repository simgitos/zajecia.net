<li class="nav-item {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.courses.index') }}">
        <span class="nav-link-icon">
            <i class="ti ti-books"></i>
        </span>
        <span class="nav-link-title">Zajęcia (Kursy)</span>
    </a>
</li>
<li class="nav-item {{ request()->routeIs('admin.children.*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.children.index') }}">
        <span class="nav-link-icon">
            <i class="ti ti-mood-kid"></i>
        </span>
        <span class="nav-link-title">Dzieci</span>
    </a>
</li>
<li class="nav-item {{ request()->routeIs('admin.user.*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.user.index') }}">
        <span class="nav-link-icon">
            <i class="ti ti-users"></i>
        </span>
        <span class="nav-link-title">Użytkownicy</span>
    </a>
</li>