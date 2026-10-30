@extends('layouts.app')

@section('title', 'Centrum powiadomień')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Konto użytkownika</div>
                <h2 class="page-title d-flex align-items-center">
                    <i class="ti ti-bell-ringing me-2 text-primary"></i> Centrum powiadomień
                    @if($unreadCount > 0)
                        <span class="badge bg-red text-red-fg ms-2">{{ $unreadCount }} nieprzeczytane</span>
                    @endif
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    @if($unreadCount > 0)
                        <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary">
                                <i class="ti ti-check-double me-1"></i> Oznacz wszystkie jako przeczytane
                            </button>
                        </form>
                    @endif

                    @if(Auth::user()->hasRole('admin'))
                        <a href="{{ route('admin.notifications.create') }}" class="btn btn-primary">
                            <i class="ti ti-send me-1"></i> Wyślij powiadomienie
                        </a>
                    @elseif(Auth::user()->hasRole('teacher'))
                        <a href="{{ route('teacher.notifications.create') }}" class="btn btn-primary">
                            <i class="ti ti-send me-1"></i> Wyślij powiadomienie do grupy
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
       
        <div class="card shadow-xs border-0">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 py-3 bg-surface border-bottom">
                <ul class="nav nav-pills card-header-pills m-0">
                    <li class="nav-item">
                        <a href="{{ route('notifications.index', ['filter' => 'all']) }}" 
                           class="nav-link {{ $filter !== 'unread' ? 'active' : '' }}">
                            <i class="ti ti-list me-1"></i> Wszystkie
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" 
                           class="nav-link {{ $filter === 'unread' ? 'active' : '' }}">
                            <i class="ti ti-mail-unread me-1"></i> Nieprzeczytane
                            @if($unreadCount > 0)
                                <span class="badge bg-red ms-1.5">{{ $unreadCount }}</span>
                            @endif
                        </a>
                    </li>
                </ul>
            </div>

            <div class="list-group list-group-flush">
                @forelse($notifications as $notification)
                    @php
                        $data = $notification->data;
                        $type = $data['type'] ?? 'info';
                        $isUnread = is_null($notification->read_at);
                        $iconClass = match($type) {
                            'warning' => 'ti-alert-triangle text-warning',
                            'danger'  => 'ti-alert-circle text-danger',
                            'success' => 'ti-circle-check text-success',
                            default   => 'ti-info-circle text-primary',
                        };
                        $bgBadge = match($type) {
                            'warning' => 'bg-warning-lt',
                            'danger'  => 'bg-danger-lt',
                            'success' => 'bg-success-lt',
                            default   => 'bg-primary-lt',
                        };
                    @endphp
                    <div class="list-group-item p-3 {{ $isUnread ? 'bg-primary-lt-subtle border-start border-3 border-primary' : '' }}">
                        <div class="row align-items-start g-3">
                            <div class="col-auto">
                                <span class="avatar avatar-md rounded-circle {{ $bgBadge }}">
                                    <i class="ti {{ $iconClass }} fs-2"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-1 mb-1">
                                    <h4 class="mb-0 {{ $isUnread ? 'fw-bold text-dark' : 'text-body' }} fs-3">
                                        {{ $data['title'] ?? 'Powiadomienie systemowe' }}
                                        @if($isUnread)
                                            <span class="badge bg-red text-red-fg ms-1.5 fs-6 py-0.5">Nowe</span>
                                        @endif
                                    </h4>
                                    <div class="text-secondary small">
                                        <i class="ti ti-calendar-time me-1"></i>
                                        {{ $notification->created_at->translatedFormat('d F Y, H:i') }}
                                        <span class="opacity-75">({{ $notification->created_at->diffForHumans() }})</span>
                                    </div>
                                </div>

                                @if(!empty($data['sender_name']))
                                    <div class="text-muted small mb-2">
                                        <i class="ti ti-user-circle me-1"></i> Nadawca: <strong>{{ $data['sender_name'] }}</strong>
                                    </div>
                                @endif

                                <div class="text-secondary mb-3 lh-base" style="white-space: pre-line;">
                                    {{ $data['message'] ?? '' }}
                                </div>

                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    @if(!empty($data['url']))
                                        <a href="{{ route('notifications.go', $notification->id) }}" class="btn btn-sm btn-primary">
                                            <i class="ti ti-arrow-right me-1"></i> {{ $data['action_text'] ?? 'Przejdź do szczegółów' }}
                                        </a>
                                    @endif

                                    @if($isUnread)
                                        <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                <i class="ti ti-check me-1"></i> Oznacz jako przeczytane
                                            </button>
                                        </form>
                                    @else
                                        <span class="badge bg-secondary-lt text-secondary py-1.5 px-2">
                                            <i class="ti ti-check me-1"></i> Przeczytano {{ $notification->read_at->diffForHumans() }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-5 text-center text-muted">
                        <i class="ti ti-bell-off fs-1 d-block mb-2 opacity-50"></i>
                        <h4 class="fw-bold">Brak powiadomień</h4>
                        <p class="text-secondary mb-0">
                            {{ $filter === 'unread' ? 'Nie masz żadnych nieprzeczytanych wiadomości.' : 'Nie otrzymano jeszcze żadnych powiadomień.' }}
                        </p>
                    </div>
                @endforelse
            </div>

            @if($notifications->hasPages())
                <div class="card-footer d-flex justify-content-center py-2 bg-surface">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
