<li class="nav-item {{ request()->routeIs('user.children.*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('user.children.index') }}">
        <span class="nav-link-icon">
            <i class="ti ti-mood-kid"></i>
        </span>
        <span class="nav-link-title">Uczestnicy</span>
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

<li class="nav-item {{ request()->routeIs('user.documents.*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('user.documents.index') }}">
        <span class="nav-link-icon">
            <i class="ti ti-files"></i>
        </span>
        <span class="nav-link-title">Dokumenty</span>
    </a>
</li>

<li class="nav-item {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('notifications.index') }}">
        <span class="nav-link-icon">
            <i class="ti ti-bell"></i>
        </span>
        <span class="nav-link-title">Powiadomienia</span>
        @php
            $navUnread = Auth::user()->unreadNotifications()->count();
        @endphp
        @if($navUnread > 0)
            <span class="badge bg-red ms-auto">{{ $navUnread }}</span>
        @endif
    </a>
</li>