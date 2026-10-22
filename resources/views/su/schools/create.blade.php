@extends('layouts.app')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle text-danger fw-bold">
                <i class="ti ti-shield-bolt me-1"></i> PANEL SUPERADMINA (SU)
            </div>
            <h2 class="page-title text-dark fw-bold fs-2">
                Dodaj nową placówkę
            </h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('su.schools.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i> Powrót do listy
            </a>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <form action="{{ route('su.schools.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label required">Pełna nazwa placówki</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="np. Miejski Dom Kultury w Warszawie">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">NIP</label>
                    <input type="text" name="nip" class="form-control @error('nip') is-invalid @enderror" value="{{ old('nip') }}" placeholder="np. 5252525252">
                    @error('nip') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label required">Status</label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Aktywna</option>
                        <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>Zawieszona</option>
                    </select>
                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label required">Plan taryfowy</label>
                    <select name="subscription_plan" class="form-select @error('subscription_plan') is-invalid @enderror" required>
                        <option value="solo" {{ old('subscription_plan') === 'solo' ? 'selected' : '' }}>Solo (do 30 zapisów)</option>
                        <option value="start" {{ old('subscription_plan') === 'start' ? 'selected' : '' }}>Start (do 100 zapisów)</option>
                        <option value="standard" {{ old('subscription_plan', 'standard') === 'standard' ? 'selected' : '' }}>Standard (do 350 zapisów) - Rekomendowany</option>
                        <option value="pro" {{ old('subscription_plan') === 'pro' ? 'selected' : '' }}>Pro / Maxi (do 800 zapisów)</option>
                    </select>
                    @error('subscription_plan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">E-mail kontaktowy</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="sekretariat@mdk.pl">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Telefon</label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="+48 ...">
                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Adres siedziby</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address') }}" placeholder="ul. Szkolna 1">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Miejscowość</label>
                    <input type="text" name="city" class="form-control" value="{{ old('city') }}" placeholder="Warszawa">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Kod pocztowy</label>
                    <input type="text" name="postal_code" class="form-control" value="{{ old('postal_code') }}" placeholder="00-001">
                </div>

                <div class="col-md-12">
                    <label class="form-label">Numer rachunku bankowego</label>
                    <input type="text" name="bank_account_number" class="form-control" value="{{ old('bank_account_number') }}" placeholder="PL 00 0000 0000 0000 0000 0000 0000">
                </div>
            </div>
        </div>
        <div class="card-footer bg-light d-flex justify-content-end gap-2">
            <a href="{{ route('su.schools.index') }}" class="btn btn-secondary">Anuluj</a>
            <button type="submit" class="btn btn-primary fw-bold">
                <i class="ti ti-check me-1"></i> Utwórz placówkę
            </button>
        </div>
    </form>
</div>
@endsection
