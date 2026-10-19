<li class="nav-item {{ request()->routeIs('admin.seasons.*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.seasons.index') }}">
        <span class="nav-link-icon">
            <i class="ti ti-calendar-event"></i>
        </span>
        <span class="nav-link-title">Sezony / Semestry</span>
    </a>
</li>
<li class="nav-item {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.courses.index') }}">
        <span class="nav-link-icon">
            <i class="ti ti-books"></i>
        </span>
        <span class="nav-link-title">Zajęcia</span>
    </a>
</li>
<li class="nav-item {{ request()->routeIs('admin.children.*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.children.index') }}">
        <span class="nav-link-icon">
            <i class="ti ti-users"></i>
        </span>
        <span class="nav-link-title">Uczestnicy</span>
    </a>
</li>
<li class="nav-item {{ request()->routeIs('admin.billing.*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.billing.index') }}">
        <span class="nav-link-icon">
            <i class="ti ti-receipt-2"></i>
        </span>
        <span class="nav-link-title">Płatności & Rozliczenia</span>
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
<li class="nav-item {{ request()->routeIs('admin.documents.*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.documents.index') }}">
        <span class="nav-link-icon">
            <i class="ti ti-files"></i>
        </span>
        <span class="nav-link-title">Dokumenty placówki</span>
    </a>
</li>
<li class="nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.settings.edit') }}">
        <span class="nav-link-icon">
            <i class="ti ti-settings"></i>
        </span>
        <span class="nav-link-title">Ustawienia</span>
    </a>
</li>