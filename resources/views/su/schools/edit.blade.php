@extends('layouts.app')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle text-danger fw-bold">
                <i class="ti ti-shield-bolt me-1"></i> PANEL SUPERADMINA (SU)
            </div>
            <h2 class="page-title text-dark fw-bold fs-2">
                Edycja placówki: {{ $school->name }}
            </h2>
        </div>
        <div class="col-auto ms-auto d-flex gap-2">
            <form action="{{ route('su.schools.switch', $school) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-primary">
                    <i class="ti ti-login me-1"></i> Wejdź jako placówka
                </button>
            </form>
            <a href="{{ route('su.schools.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i> Powrót
            </a>
        </div>
    </div>
</div>



<!-- Sekcja 1: Biała Lista Tpay Collect (Weryfikacja rachunku bankowego) -->
<div class="card shadow-sm border-0 border-top border-4 border-success mb-4">
    <div class="card-header bg-surface py-3 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <span class="avatar bg-success-lt text-success rounded">
                <i class="ti ti-shield-check fs-2"></i>
            </span>
            <div>
                <h3 class="card-title fw-bold text-dark mb-0">Tpay Collect — Biała Lista Kont Bankowych</h3>
                <div class="text-secondary small">Weryfikacja rachunku placówki w Tpay na potrzeby automatycznych wypłat z transakcji online.</div>
            </div>
        </div>
        <div>
            @if($school->tpay_whitelist_status === 'verified')
                <span class="badge bg-success text-white fs-5 px-3 py-1">
                    <i class="ti ti-shield-check me-1"></i> Konto Zweryfikowane w Tpay
                </span>
            @elseif($school->tpay_whitelist_status === 'pending')
                <span class="badge bg-warning text-dark fs-5 px-3 py-1">
                    <i class="ti ti-clock me-1"></i> Oczekuje na zatwierdzenie
                </span>
            @else
                <span class="badge bg-warning-lt text-warning-emphasis fs-5 px-3 py-1">
                    <i class="ti ti-alert-circle me-1"></i> Rachunek niezweryfikowany
                </span>
            @endif
        </div>
    </div>
    <div class="card-body">
        <div class="row align-items-center g-3">
            <div class="col-md-7">
                <div class="mb-2">
                    <span class="text-muted small text-uppercase fw-bold">Numer rachunku do rozliczeń Collect (IBAN):</span>
                    <div class="fs-3 fw-bold font-monospace text-dark mt-0.5">
                        {{ $school->formattedIban() ?? 'Brak zdefiniowanego numeru konta w danych placówki' }}
                    </div>
                </div>
                <div class="text-secondary small">
                    Odbiorca: <strong>{{ $school->full_name ?: $school->name }}</strong>
                    @if($school->nip) • NIP: <strong>{{ $school->nip }}</strong> @endif
                    @if($school->tpay_whitelist_at)
                        • Data weryfikacji: <strong>{{ $school->tpay_whitelist_at->format('d.m.Y H:i') }}</strong>
                    @endif
                </div>
                @if($school->tpay_whitelist_response)
                    <div class="text-muted small mt-2 bg-light p-2 rounded font-monospace" style="font-size: 0.75rem;">
                        Odpowiedź Tpay: {{ Str::limit($school->tpay_whitelist_response, 180) }}
                    </div>
                @endif
            </div>

            <div class="col-md-5 d-flex flex-column flex-sm-row justify-content-md-end gap-2">
                @if(!empty($school->bank_account_number))
                    {{-- Zgłoszenie przez API Tpay Collect --}}
                    <form action="{{ route('su.schools.tpay-whitelist', $school) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success fw-bold shadow-sm w-100" onclick="return confirm('Czy na pewno chcesz wysłać zgłoszenie tego rachunku do API Tpay Collect (Biała lista kont)?');">
                            <i class="ti ti-cloud-upload me-1"></i> Zgłoś do białej listy Tpay (API)
                        </button>
                    </form>

                    {{-- Ręczna weryfikacja / zmiana statusu --}}
                    <form action="{{ route('su.schools.tpay-manual-verify', $school) }}" method="POST">
                        @csrf
                        @if($school->tpay_whitelist_status === 'verified')
                            <input type="hidden" name="status" value="unverified">
                            <button type="submit" class="btn btn-outline-secondary btn-sm" title="Oznacz jako niezweryfikowane">
                                <i class="ti ti-x me-1"></i> Cofnij weryfikację
                            </button>
                        @else
                            <input type="hidden" name="status" value="verified">
                            <button type="submit" class="btn btn-outline-success fw-bold" title="Ręczne potwierdzenie bez wywoływania API Tpay">
                                <i class="ti ti-check me-1"></i> Oznacz jako zweryfikowane
                            </button>
                        @endif
                    </form>
                @else
                    <div class="alert alert-warning py-2 px-3 m-0 small">
                        Wprowadź numer rachunku bankowego poniżej, aby aktywować weryfikację w Tpay.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Sekcja 2: Główne Dane Placówki -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-surface py-3">
        <h3 class="card-title fw-bold text-dark mb-0">
            <i class="ti ti-building me-2 text-primary"></i> Dane podstawowe i kontaktowe
        </h3>
    </div>
    <form action="{{ route('su.schools.update', $school) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label required fw-bold">Skrócona nazwa placówki (wyświetlana)</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $school->name) }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">NIP</label>
                    <input type="text" name="nip" class="form-control @error('nip') is-invalid @enderror" value="{{ old('nip', $school->nip) }}">
                    @error('nip') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label required fw-bold">Status placówki</label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="active" {{ old('status', $school->status) === 'active' ? 'selected' : '' }}>Aktywna</option>
                        <option value="suspended" {{ old('status', $school->status) === 'suspended' ? 'selected' : '' }}>Zawieszona</option>
                    </select>
                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Pełna oficjalna nazwa placówki (właściciel rachunku)</label>
                    <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror" value="{{ old('full_name', $school->full_name) }}" placeholder="np. Samorządowy Ośrodek Kultury i Rekreacji...">
                    <small class="form-hint">Używana w przelewach Tpay Collect jako odbiorca transakcji.</small>
                    @error('full_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Numer rachunku bankowego</label>
                    <input type="text" name="bank_account_number" class="form-control font-monospace @error('bank_account_number') is-invalid @enderror" value="{{ old('bank_account_number', $school->bank_account_number) }}" placeholder="XX XXXX XXXX XXXX XXXX XXXX XXXX">
                    <small class="form-hint">Format polski (26 cyfr) lub IBAN z przedrostkiem PL.</small>
                    @error('bank_account_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label required fw-bold">Plan taryfowy</label>
                    <select name="subscription_plan" class="form-select @error('subscription_plan') is-invalid @enderror" required>
                        <option value="solo" {{ old('subscription_plan', $school->subscription_plan) === 'solo' ? 'selected' : '' }}>Solo (do 30 zapisów)</option>
                        <option value="start" {{ old('subscription_plan', $school->subscription_plan) === 'start' ? 'selected' : '' }}>Start (do 100 zapisów)</option>
                        <option value="standard" {{ old('subscription_plan', $school->subscription_plan) === 'standard' ? 'selected' : '' }}>Standard (do 350 zapisów) - Rekomendowany</option>
                        <option value="pro" {{ old('subscription_plan', $school->subscription_plan) === 'pro' ? 'selected' : '' }}>Pro / Maxi (do 800 zapisów)</option>
                    </select>
                    @error('subscription_plan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">E-mail kontaktowy</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $school->email) }}">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Telefon</label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $school->phone) }}">
                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Adres siedziby</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address', $school->address) }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Miejscowość</label>
                    <input type="text" name="city" class="form-control" value="{{ old('city', $school->city) }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-bold">Kod pocztowy</label>
                    <input type="text" name="postal_code" class="form-control" value="{{ old('postal_code', $school->postal_code) }}">
                </div>
            </div>
        </div>
        <div class="card-footer bg-light d-flex justify-content-between align-items-center">
            <span class="text-secondary small">Utworzono: {{ $school->created_at?->format('d.m.Y H:i') }}</span>
            <div class="d-flex gap-2">
                <a href="{{ route('su.schools.index') }}" class="btn btn-secondary">Anuluj</a>
                <button type="submit" class="btn btn-primary fw-bold">
                    <i class="ti ti-device-floppy me-1"></i> Zapisz zmiany
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
