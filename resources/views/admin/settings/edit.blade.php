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
                        Zarządzaj danymi identyfikacyjnymi, teleadresowymi oraz rachunkiem bankowym placówki.
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
                <!-- Sekcja 1: Dane podstawowe -->
                <div class="col-12 col-lg-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-surface border-bottom">
                            <h3 class="card-title fw-bold text-dark mb-0">
                                <i class="ti ti-building me-2 text-primary"></i> Podstawowe informacje
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label required fw-bold" for="name">Nazwa skrócona / wyświetlana</label>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $school->name) }}"
                                    placeholder="np. Miejsko-Gminny Ośrodek Kultury" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold" for="nip">NIP</label>
                                <input type="text" name="nip" id="nip"
                                    class="form-control @error('nip') is-invalid @enderror"
                                    value="{{ old('nip', $school->nip) }}" placeholder="np. 1234567890">
                                @error('nip')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold" for="email">Adres e-mail kontaktowy</label>
                                <input type="email" name="email" id="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $school->email) }}" placeholder="kontakt@szkola.pl">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold" for="phone">Telefon kontaktowy</label>
                                <input type="text" name="phone" id="phone"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    value="{{ old('phone', $school->phone) }}" placeholder="+48 123 456 789">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sekcja 2: Dane adresowe -->
                <div class="col-12 col-lg-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-surface border-bottom">
                            <h3 class="card-title fw-bold text-dark mb-0">
                                <i class="ti ti-map-pin me-2 text-primary"></i> Adres placówki
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold" for="address">Ulica i numer</label>
                                <input type="text" name="address" id="address"
                                    class="form-control @error('address') is-invalid @enderror"
                                    value="{{ old('address', $school->address) }}" placeholder="np. ul. Główna 15">
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-12 col-md-5">
                                    <label class="form-label fw-bold" for="postal_code">Kod pocztowy</label>
                                    <input type="text" name="postal_code" id="postal_code"
                                        class="form-control @error('postal_code') is-invalid @enderror"
                                        value="{{ old('postal_code', $school->postal_code) }}" placeholder="np. 00-001">
                                    @error('postal_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-7">
                                    <label class="form-label fw-bold" for="city">Miejscowość</label>
                                    <input type="text" name="city" id="city"
                                        class="form-control @error('city') is-invalid @enderror"
                                        value="{{ old('city', $school->city) }}" placeholder="np. Warszawa">
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="alert alert-light border text-secondary mt-4 mb-0 py-2 px-3">
                                <div class="d-flex align-items-center">
                                    <i class="ti ti-info-circle me-2 fs-3 text-muted"></i>
                                    <div class="small">
                                        Dane adresowe pojawią się na potwierdzeniach wpłat oraz formularzach dla
                                        uczestników.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sekcja 3: Rachunek bankowy i rozliczenia -->
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-surface border-bottom d-flex justify-content-between align-items-center">
                            <h3 class="card-title fw-bold text-dark mb-0">
                                <i class="ti ti-building-bank me-2 text-primary"></i> Rachunek bankowy i rozliczenia wpłat
                            </h3>
                            @if($school->tpay_whitelist_status === 'VERIFIED')
                                <span class="badge bg-success-lt fs-6">
                                    <i class="ti ti-check me-1"></i> Rachunek zweryfikowany w Tpay
                                </span>
                            @elseif($school->tpay_whitelist_status === 'REJECTED')
                                <span class="badge bg-danger-lt fs-6">
                                    <i class="ti ti-x me-1"></i> Odrzucony przez Tpay
                                </span>
                            @else
                                <span class="badge bg-secondary-lt fs-6">
                                    <i class="ti ti-clock me-1"></i> Oczekuje na zgłoszenie Tpay
                                </span>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold" for="full_name">Pełna nazwa placówki /
                                        odbiorcy</label>
                                    <input type="text" name="full_name" id="full_name"
                                        class="form-control @error('full_name') is-invalid @enderror"
                                        value="{{ old('full_name', $school->full_name) }}"
                                        placeholder="np. Samorządowy Ośrodek Kultury i Sportu">
                                    <small class="form-hint">Oficjalna nazwa posiadacza rachunku bankowego (wymagana do
                                        przelewów).</small>
                                    @error('full_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold" for="bank_account_number">Numer rachunku
                                        bankowego</label>
                                    <input type="text" name="bank_account_number" id="bank_account_number"
                                        class="form-control font-monospace @error('bank_account_number') is-invalid @enderror"
                                        value="{{ old('bank_account_number', $school->bank_account_number) }}"
                                        placeholder="XXXXXXXXXXXXXXXXXXXXXXXXXX" pattern="[0-9]{26}" maxlength="26"
                                        minlength="26" inputmode="numeric">
                                    <small class="form-hint">26 cyfr polskiego rachunku IBAN, na który trafiają wpłaty
                                        rodziców.</small>
                                    @error('bank_account_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="alert alert-info border-0 shadow-none mt-3 mb-0 py-2 px-3">
                                <div class="d-flex align-items-center">
                                    <i class="ti ti-shield-check fs-2 me-2"></i>
                                    <div class="fs-5">
                                        Płatności online (BLIK, szybkie przelewy) są obsługiwane centralnie przez system
                                        transakcyjny Tpay i trafiają bezpośrednio na powyższy rachunek bankowy placówki.
                                    </div>
                                </div>
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