<li class="nav-item {{ request()->routeIs('teacher.courses.*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('teacher.courses.index') }}">
        <span class="nav-link-icon">
            <i class="ti ti-school"></i>
        </span>
        <span class="nav-link-title">Moje Zajęcia</span>
    </a>
</li>
<li class="nav-item {{ request()->routeIs('teacher.children.*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('teacher.children.index') }}">
        <span class="nav-link-icon">
            <i class="ti ti-users"></i>
        </span>
        <span class="nav-link-title">Uczestnicy</span>
    </a>
</li>
<li class="nav-item {{ request()->routeIs('teacher.lessons.*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('teacher.lessons.index') }}">
        <span class="nav-link-icon">
            <i class="ti ti-calendar-check"></i>
        </span>
        <span class="nav-link-title">Przeprowadzone lekcje</span>
    </a>
</li>