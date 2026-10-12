<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
</form>

<form method="post" action="{{ route('profile.update') }}">
    @csrf
    @method('patch')

    <div class="row g-3">
        {{-- Imię i nazwisko --}}
        <div class="col-12 col-md-6">
            <label class="form-label required fw-bold" for="name">Imię i nazwisko</label>
            <input type="text" 
                   id="name" 
                   name="name" 
                   class="form-control @error('name') is-invalid @enderror" 
                   value="{{ old('name', $user->name) }}" 
                   required 
                   autofocus 
                   autocomplete="name">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Email --}}
        <div class="col-12 col-md-6">
            <label class="form-label required fw-bold" for="email">Adres e-mail</label>
            <input type="email" 
                   id="email" 
                   name="email" 
                   class="form-control @error('email') is-invalid @enderror" 
                   value="{{ old('email', $user->email) }}" 
                   required 
                   autocomplete="username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="text-warning small mb-1">
                        <i class="ti ti-alert-triangle me-1"></i> Twój adres e-mail nie został zweryfikowany.
                    </p>
                    <button form="send-verification" class="btn btn-sm btn-link p-0 text-decoration-underline">
                        Kliknij tutaj, aby wysłać ponownie link weryfikacyjny.
                    </button>

                    @if (session('status') === 'verification-link-sent')
                        <p class="text-success small mt-1">
                            Nowy link weryfikacyjny został wysłany na Twój adres e-mail.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- Telefon --}}
        <div class="col-12 col-md-6">
            <label class="form-label fw-bold" for="phone">Numer telefonu</label>
            <div class="input-icon">
                <span class="input-icon-addon">
                    <i class="ti ti-phone"></i>
                </span>
                <input type="text" 
                       id="phone" 
                       name="phone" 
                       class="form-control @error('phone') is-invalid @enderror" 
                       value="{{ old('phone', $user->phone) }}" 
                       placeholder="+48 123 456 789"
                       autocomplete="tel">
            </div>
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Ulica i numer --}}
        <div class="col-12 col-md-6">
            <label class="form-label fw-bold" for="address">Ulica i numer domu / lokalu</label>
            <div class="input-icon">
                <span class="input-icon-addon">
                    <i class="ti ti-map-pin"></i>
                </span>
                <input type="text" 
                       id="address" 
                       name="address" 
                       class="form-control @error('address') is-invalid @enderror" 
                       value="{{ old('address', $user->address) }}" 
                       placeholder="np. ul. Polna 12/4"
                       autocomplete="street-address">
            </div>
            @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Kod pocztowy --}}
        <div class="col-12 col-md-4">
            <label class="form-label fw-bold" for="postal_code">Kod pocztowy</label>
            <div class="input-icon">
                <span class="input-icon-addon">
                    <i class="ti ti-mailbox"></i>
                </span>
                <input type="text" 
                       id="postal_code" 
                       name="postal_code" 
                       class="form-control @error('postal_code') is-invalid @enderror" 
                       value="{{ old('postal_code', $user->postal_code) }}" 
                       placeholder="np. 00-001"
                       autocomplete="postal-code">
            </div>
            @error('postal_code')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Miejscowość --}}
        <div class="col-12 col-md-8">
            <label class="form-label fw-bold" for="city">Miejscowość</label>
            <div class="input-icon">
                <span class="input-icon-addon">
                    <i class="ti ti-building"></i>
                </span>
                <input type="text" 
                       id="city" 
                       name="city" 
                       class="form-control @error('city') is-invalid @enderror" 
                       value="{{ old('city', $user->city) }}" 
                       placeholder="np. Warszawa"
                       autocomplete="address-level2">
            </div>
            @error('city')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="mt-4 text-end">
        <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
            <i class="ti ti-device-floppy me-1 fs-3"></i> Zapisz dane profilu
        </button>
    </div>
</form>
