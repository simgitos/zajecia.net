@extends('layouts.guest')

@section('content')
<div class="container container-tight py-4">
    <div class="card card-md">
      <div class="card-body">
        <h2 class="card-title text-center mb-4">Utwórz nowe konto</h2>
        
        <form action="{{ route('register') }}" method="POST" autocomplete="off">
          @csrf

          <!-- Imię / Nazwa użytkownika -->
          <div class="mb-3">
            <label class="form-label">Imię i Nazwisko / Nick</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required autofocus placeholder="np. Jan Kowalski">
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

          <!-- Przycisk Zarejestruj -->
          <div class="form-footer">
            <button type="submit" class="btn btn-primary w-full">Zarejestruj się</button>
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
