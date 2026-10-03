@extends('layouts.front')

@section('content')
<div class="container-xl py-4">
    <!-- Hero / Header Placówki -->
    <div class="card card-md mb-4 bg-primary-subtle border-0">
        <div class="card-body text-center py-5">
            <span class="avatar avatar-xl bg-primary text-white mb-3 rounded-circle">
                <i class="ti ti-school fs-1"></i>
            </span>
            <h1 class="display-6 fw-bold mb-2">{{ $school->name }}</h1>
            <p class="text-secondary fs-3 mb-4">Witaj na stronie rekrutacji i zapisów do placówki!</p>
            
            <a href="{{ route('schools.register', $school->slug) }}" class="btn btn-primary btn-lg px-4 shadow">
                <i class="ti ti-user-plus me-2 fs-2"></i> Zarejestruj się w tej szkole
            </a>
        </div>
    </div>

    <div class="row row-cards">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="ti ti-info-circle me-2"></i> O placówce</h3>
                </div>
                <div class="card-body">
                    <p class="fs-3">
                        Witamy w panelu informacyjnym placówki <strong>{{ $school->name }}</strong>. 
                        Dołączając do nas zyskujesz dostęp do planu zajęć, zapisów oraz informacji organizacyjnych.
                    </p>
                    <div class="mt-4 p-3 bg-body-tertiary rounded">
                        <h4>Gotowy aby dołączyć?</h4>
                        <p class="text-secondary mb-3">Załóż bezpłatne konto rodzica/użytkownika w zaledwie kilka sekund.</p>
                        <a href="{{ route('schools.register', $school->slug) }}" class="btn btn-success">
                            <i class="ti ti-arrow-right me-1"></i> Przejdź do formularza rejestracji
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="ti ti-building me-2"></i> Kontakt z placówką</h3>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled space-y-2 mb-0 fs-3">
                        @if($school->email)
                            <li class="d-flex align-items-center mb-2">
                                <i class="ti ti-mail text-primary me-2 fs-2"></i>
                                <span>{{ $school->email }}</span>
                            </li>
                        @endif
                        @if($school->phone)
                            <li class="d-flex align-items-center mb-2">
                                <i class="ti ti-phone text-primary me-2 fs-2"></i>
                                <span>{{ $school->phone }}</span>
                            </li>
                        @endif
                        <li class="d-flex align-items-center">
                            <i class="ti ti-link text-primary me-2 fs-2"></i>
                            <span class="text-muted">Slug: <code>{{ $school->slug }}</code></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
