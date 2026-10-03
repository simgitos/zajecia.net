<li class="nav-item {{ request()->routeIs('teacher.courses.*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('teacher.courses.index') }}">
        <span class="nav-link-icon">
            <i class="ti ti-school"></i>
        </span>
        <span class="nav-link-title">Moje Zajęcia</span>
    </a>
</li>