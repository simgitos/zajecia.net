@extends('layouts.front')

@section('content')
<div class="container-xl">
    <!-- Nagłówek strony -->
    <div class="page-header d-print-none mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title text-primary fs-1">
                    <i class="ti ti-school me-2"></i> Lista Placówek i Szkół
                </h2>
                <div class="text-secondary mt-1">
                    Wybierz swoją placówkę, aby przejść do rejestracji lub zalogować się do dedykowanego panelu.
                </div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ route('register') }}" class="btn btn-success">
                    <i class="ti ti-plus me-2"></i> Załóż własną placówkę (Admin)
                </a>
            </div>
        </div>
    </div>

    <!-- Lista szkół -->
    <div class="row row-cards">
        @forelse($schools as $school)
            <div class="col-md-6 col-lg-4">
                <div class="card card-stacked">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <span class="avatar avatar-lg bg-primary-subtle text-primary me-3">
                                <i class="ti ti-school fs-2"></i>
                            </span>
                            <div>
                                <h3 class="card-title mb-1">
                                    <a href="{{ route('schools.show', $school->slug) }}" class="text-reset">
                                        {{ $school->name }}
                                    </a>
                                </h3>
                                <span class="badge bg-green-lt">Aktywna placówka</span>
                            </div>
                        </div>

                        <div class="text-secondary mb-3">
                            @if($school->email)
                                <div><i class="ti ti-mail me-1"></i> {{ $school->email }}</div>
                            @endif
                            @if($school->phone)
                                <div><i class="ti ti-phone me-1"></i> {{ $school->phone }}</div>
                            @endif
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('schools.show', $school->slug) }}" class="btn btn-outline-primary w-50">
                                <i class="ti ti-eye me-1"></i> Szczegóły
                            </a>
                            <a href="{{ route('schools.register', $school->slug) }}" class="btn btn-primary w-50">
                                <i class="ti ti-user-plus me-1"></i> Zarejestruj się
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card text-center p-5">
                    <div class="empty-icon mb-3">
                        <i class="ti ti-school-off fs-1 text-secondary"></i>
                    </div>
                    <p class="empty-title h3">Brak zarejestrowanych szkół</p>
                    <p class="empty-subtitle text-secondary">
                        Bądź pierwszy i zarejestruj swoją placówkę w serwisie!
                    </p>
                    <div class="empty-action">
                        <a href="{{ route('register') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-2"></i> Załóż pierwszą placówkę
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
