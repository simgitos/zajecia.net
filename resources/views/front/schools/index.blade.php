@extends('layouts.front')

@section('title', 'Katalog Szkół i Placówek - EduZajęcia.net')
@section('meta_description', 'Przeglądaj zarejestrowane szkoły językowe, sportowe, taneczne oraz MDK w systemie EduZajęcia.net. Wybierz placówkę i zapisz dziecko online.')

@section('content')
<div class="py-5 bg-light min-vh-100">
    <div class="container-xl">
        <!-- Nagłówek strony -->
        <div class="page-header d-print-none mb-4">
            <div class="row align-items-center g-3">
                <div class="col">
                    <span class="badge bg-blue-subtle text-primary fw-bold px-3 py-2 rounded-pill mb-2">KATALOG PLACÓWEK</span>
                    <h1 class="page-title text-dark fw-extrabold display-6 mb-1">
                        <i class="ti ti-school me-2 text-primary"></i> Rejestr Szkół i Placówek Partnerskich
                    </h1>
                    <div class="text-secondary fs-3">
                        Wybierz swoją szkołę lub placówkę, aby przejść do rejestracji opiekuna lub zalogować się do dedykowanego panelu.
                    </div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ route('register') }}" class="btn btn-success btn-lg fw-bold shadow-sm">
                        <i class="ti ti-plus me-2"></i> Załóż własną placówkę (Admin)
                    </a>
                </div>
            </div>
        </div>

        <!-- Lista szkół -->
        <div class="row row-cards">
            @forelse($schools as $school)
                <div class="col-md-6 col-lg-4">
                    <div class="card card-stacked shadow-sm border-0 h-100">
                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center mb-3">
                                    <span class="avatar avatar-lg bg-primary-subtle text-primary me-3 rounded-3 fs-1">
                                        <i class="ti ti-school"></i>
                                    </span>
                                    <div>
                                        <h3 class="card-title fw-bold fs-2 mb-1">
                                            <a href="{{ route('schools.show', $school->slug) }}" class="text-reset">
                                                {{ $school->name }}
                                            </a>
                                        </h3>
                                        <span class="badge bg-green-lt"><i class="ti ti-circle-check me-1"></i> Aktywna placówka</span>
                                    </div>
                                </div>

                                <div class="text-secondary mb-4 fs-4">
                                    @if($school->email)
                                        <div class="mb-1"><i class="ti ti-mail me-2 text-primary"></i> {{ $school->email }}</div>
                                    @endif
                                    @if($school->phone)
                                        <div><i class="ti ti-phone me-2 text-success"></i> {{ $school->phone }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="d-flex gap-2 pt-2 border-top">
                                <a href="{{ route('schools.show', $school->slug) }}" class="btn btn-outline-primary w-50 fw-semibold">
                                    <i class="ti ti-eye me-1"></i> Szczegóły
                                </a>
                                <a href="{{ route('schools.register', $school->slug) }}" class="btn btn-primary w-50 fw-bold">
                                    <i class="ti ti-user-plus me-1"></i> Zarejestruj się
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card text-center p-5 shadow-sm border-0">
                        <div class="empty-icon mb-3">
                            <span class="avatar avatar-xl bg-blue-subtle text-primary rounded-circle fs-1">
                                <i class="ti ti-school-off"></i>
                            </span>
                        </div>
                        <h3 class="fw-bold fs-1 text-dark">Brak zarejestrowanych szkół</h3>
                        <p class="empty-subtitle text-secondary fs-3 max-w-md mx-auto">
                            Bądź pierwszy i zarejestruj swoją placówkę w serwisie EduZajęcia.net!
                        </p>
                        <div class="empty-action mt-3">
                            <a href="{{ route('register') }}" class="btn btn-primary btn-lg fw-bold">
                                <i class="ti ti-plus me-2"></i> Załóż pierwszą placówkę
                            </a>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

