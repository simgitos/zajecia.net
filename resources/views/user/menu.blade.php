<li class="nav-item {{ request()->routeIs('user.children.*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('user.children.index') }}">
        <span class="nav-link-icon">
            <i class="ti ti-mood-kid"></i>
        </span>
        <span class="nav-link-title">Moje Dzieci</span>
    </a>
</li>

<li class="nav-item {{ request()->routeIs('user.courses.*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('user.courses.index') }}">
        <span class="nav-link-icon">
            <i class="ti ti-school"></i>
        </span>
        <span class="nav-link-title">Katalog Zajęć</span>
    </a>
</li>

<li class="nav-item {{ request()->routeIs('user.payments.*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('user.payments.index') }}">
        <span class="nav-link-icon">
            <i class="ti ti-wallet"></i>
        </span>
        <span class="nav-link-title">Płatności</span>
    </a>
</li>