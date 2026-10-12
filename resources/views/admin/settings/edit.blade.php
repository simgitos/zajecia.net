@extends('layouts.app')

@section('content')
<div class="page-header d-print-none mb-4">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title text-primary fw-bold fs-2">
                    <i class="ti ti-settings me-2"></i> Ustawienia placówki
                </h2>
                <div class="text-secondary mt-1 fs-4">
                    Zarządzaj podstawowymi danymi szkoły oraz konfiguracją płatności online Tpay.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-xl">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="ti ti-circle-check fs-2 me-2"></i>
                <div class="fw-semibold">{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center mb-1">
                <i class="ti ti-alert-triangle fs-2 me-2"></i>
                <div class="fw-bold">Wystąpiły błędy podczas zapisywania formularza:</div>
            </div>
            <ul class="mb-0 ps-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row row-cards">
            <!-- Sekcja 1: Dane placówki -->
            <div class="col-12 col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-surface border-bottom">
                        <h3 class="card-title fw-bold text-dark mb-0">
                            <i class="ti ti-building me-2 text-primary"></i> Dane placówki
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label required fw-bold" for="name">Nazwa placówki</label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $school->name) }}" 
                                   placeholder="np. Miejsko-Gminny Ośrodek Kultury" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold" for="nip">NIP</label>
                            <input type="text" 
                                   name="nip" 
                                   id="nip" 
                                   class="form-control @error('nip') is-invalid @enderror" 
                                   value="{{ old('nip', $school->nip) }}" 
                                   placeholder="np. 1234567890">
                            @error('nip')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold" for="email">Adres e-mail</label>
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email', $school->email) }}" 
                                   placeholder="kontakt@szkola.pl">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold" for="phone">Telefon kontaktowy</label>
                            <input type="text" 
                                   name="phone" 
                                   id="phone" 
                                   class="form-control @error('phone') is-invalid @enderror" 
                                   value="{{ old('phone', $school->phone) }}" 
                                   placeholder="+48 123 456 789">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold" for="bank_account_number">Numer rachunku bankowego</label>
                            <input type="text" 
                                   name="bank_account_number" 
                                   id="bank_account_number" 
                                   class="form-control @error('bank_account_number') is-invalid @enderror" 
                                   value="{{ old('bank_account_number', $school->bank_account_number) }}" 
                                   placeholder="XX XXXX XXXX XXXX XXXX XXXX XXXX">
                            <small class="form-hint">Numer konta wyświetlany rodzicom przy opłacaniu tradycyjnym przelewem.</small>
                            @error('bank_account_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sekcja 2: Płatności Tpay -->
            <div class="col-12 col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-surface border-bottom d-flex justify-content-between align-items-center">
                        <h3 class="card-title fw-bold text-dark mb-0">
                            <i class="ti ti-credit-card me-2 text-danger"></i> Płatności online Tpay
                        </h3>
                        @if($school->tpay_client_id && $school->tpay_client_secret)
                            <span class="badge bg-success-lt">
                                <i class="ti ti-check me-1"></i> Skonfigurowano
                            </span>
                        @else
                            <span class="badge bg-warning-lt">
                                <i class="ti ti-alert-circle me-1"></i> Nieaktywne
                            </span>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info border-0 shadow-none mb-3 py-2 px-3">
                            <div class="d-flex align-items-start">
                                <i class="ti ti-info-circle fs-2 me-2 mt-0.5"></i>
                                <div class="fs-5">
                                    Dane dostępowe wygenerujesz w Panelu Akceptanta Tpay w zakładce <strong>Integracja &rarr; API OpenAPI</strong>.
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold" for="tpay_client_id">Tpay Client ID (Username)</label>
                            <input type="text" 
                                   name="tpay_client_id" 
                                   id="tpay_client_id" 
                                   class="form-control font-monospace @error('tpay_client_id') is-invalid @enderror" 
                                   value="{{ old('tpay_client_id', $school->tpay_client_id) }}" 
                                   placeholder="np. 01M4RD1GXZ9Z50TCJ8FYCKJKAT-...">
                            @error('tpay_client_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold" for="tpay_client_secret">
                                Tpay Client Secret
                                @if(!empty($school->tpay_client_secret))
                                    <span class="badge bg-success-lt ms-1 fs-6">Klucz zapisany w bazie</span>
                                @endif
                            </label>
                            <input type="password" 
                                   name="tpay_client_secret" 
                                   id="tpay_client_secret" 
                                   class="form-control font-monospace @error('tpay_client_secret') is-invalid @enderror" 
                                   value="" 
                                   autocomplete="new-password"
                                   placeholder="{{ !empty($school->tpay_client_secret) ? '•••••••••••••••••••••••••••••••• (Pozostaw puste, aby nie zmieniać)' : 'Wklej Client Secret z Tpay' }}">
                            <small class="form-hint">Sekret jest bezpiecznie szyfrowany algorytmem AES-256 w bazie danych.</small>
                            @error('tpay_client_secret')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold" for="tpay_merchant_secret">
                                Kod bezpieczeństwa powiadomień (Merchant Secret)
                                @if(!empty($school->tpay_merchant_secret))
                                    <span class="badge bg-success-lt ms-1 fs-6">Klucz zapisany w bazie</span>
                                @endif
                            </label>
                            <input type="password" 
                                   name="tpay_merchant_secret" 
                                   id="tpay_merchant_secret" 
                                   class="form-control font-monospace @error('tpay_merchant_secret') is-invalid @enderror" 
                                   value="" 
                                   autocomplete="new-password"
                                   placeholder="{{ !empty($school->tpay_merchant_secret) ? '•••••••••••••••••••••••••••••••• (Pozostaw puste, aby nie zmieniać)' : 'Opcjonalny kod bezpieczeństwa webhooka' }}">
                            <small class="form-hint">Używany do weryfikacji powiadomień JWS (domyślnie użyty zostanie Client Secret).</small>
                            @error('tpay_merchant_secret')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-check form-switch mt-2">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="tpay_production_mode" 
                                       value="1" 
                                       {{ old('tpay_production_mode', $school->tpay_production_mode) ? 'checked' : '' }}>
                                <span class="form-check-label fw-bold">Środowisko produkcyjne (Live)</span>
                            </label>
                            <small class="form-hint text-muted d-block ms-4">
                                Odznaczone: środowisko testowe sandbox (<code>sandbox.tpay.com</code>). Zaznaczone: prawdziwe płatności produkcyjne.
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pasek akcji zapisu -->
            <div class="col-12 mt-4 text-end">
                <button type="submit" class="btn btn-primary px-4 py-2 fw-bold shadow-sm">
                    <i class="ti ti-device-floppy me-1 fs-3"></i> Zapisz ustawienia
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
