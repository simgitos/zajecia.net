@extends('layouts.guest')

@section('content')
<div class="container container-tight py-4">
    <div class="card card-md">
      <div class="card-body">
        <h2 class="card-title text-center mb-4">
            @if(isset($selectedSchool))
                Dołącz do {{ $selectedSchool->name }}
            @else
                Załóż nową placówkę
            @endif
        </h2>

        @if(isset($selectedSchool))
            <div class="alert alert-info d-flex align-items-center mb-3">
                <i class="ti ti-school fs-2 me-2"></i>
                <div>
                    Rejestrujesz się w placówce <strong>{{ $selectedSchool->name }}</strong> jako użytkownik.
                </div>
            </div>
        @else
            <div class="alert alert-primary d-flex align-items-center mb-3">
                <i class="ti ti-building-plus fs-2 me-2"></i>
                <div>
                    Tworzysz nową jednostkę/szkołę. Zostaniesz automatycznie jej administratorem.
                </div>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST" autocomplete="off">
          @csrf

          @if(isset($selectedSchool))
              <input type="hidden" name="school_id" value="{{ $selectedSchool->id }}">
              
              <!-- Nazwa jednostki (ze sluga) -->
              <div class="mb-3">
                <label class="form-label">Nazwa jednostki / Szkoły</label>
                <input type="text" class="form-control bg-light" value="{{ $selectedSchool->name }}" readonly disabled>
              </div>
          @else
              <!-- Nazwa nowej jednostki (zwykła rejestracja /register) -->
              <div class="mb-3">
                  <label class="form-label">Nazwa jednostki / szkoły <span class="text-danger">*</span></label>
                  <input type="text" name="new_school_name" class="form-control @error('new_school_name') is-invalid @enderror" value="{{ old('new_school_name') }}" required autofocus placeholder="np. Szkoła Podstawowa nr 1">
                  @error('new_school_name')
                      <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
              </div>
          @endif

          <!-- Imię i Nazwisko -->
          <div class="mb-3">
            <label class="form-label">Imię i Nazwisko</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="np. Jan Kowalski" @if(isset($selectedSchool)) autofocus @endif>
            @error('name') 
              <div class="invalid-feedback">{{ $message }}</div> 
            @enderror
          </div>

          <!-- Adres E-mail -->
          <div class="mb-3">
            <label class="form-label">Adres E-mail</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="nazwa@domena.pl">
            @error('email') 
              <div class="invalid-feedback">{{ $message }}</div> 
            @enderror
          </div>

          <!-- Hasło -->
          <div class="mb-3">
            <label class="form-label">Hasło</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required placeholder="Minimum 8 znaków">
            @error('password') 
              <div class="invalid-feedback">{{ $message }}</div> 
            @enderror
          </div>

          <!-- Potwierdzenie hasła -->
          <div class="mb-3">
            <label class="form-label">Potwierdź hasło</label>
            <input type="password" name="password_confirmation" class="form-control" required placeholder="Wpisz hasło ponownie">
          </div>
          <div class="mb-3">
            <label class="form-check">
              <input type="checkbox" name="terms_and_conditions" class="form-check-input @error('terms_and_conditions') is-invalid @enderror" required>
              Akceptuję regulamin i politykę prywatności
            </label>
            @error('terms_and_conditions')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <!-- Przycisk Zarejestruj -->
          <div class="form-footer">
            <button type="submit" class="btn btn-primary w-full">
                @if(isset($selectedSchool))
                    Dołącz do szkoły
                @else
                    Utwórz placówkę i konto
                @endif
            </button>
          </div>
        </form>
      </div>
    </div>
    
    <!-- Link powrotny do logowania -->
    <div class="text-center text-secondary mt-3">
      Masz już konto? <a href="{{ route('login') }}" class="text-primary">Zaloguj się</a>
    </div>
</div>
@endsection
