@extends('layouts.guest')

@section('content')
<div class="container container-tight py-4">
    <div class="card card-md">
      <div class="card-body">
        <h2 class="card-title text-center mb-4">Zaloguj się do panelu</h2>

        @if (session('status') === 'verification-link-sent')
            <div class="alert alert-success mb-3" role="alert">
                Nowy link aktywacyjny został wysłany na Twój adres e-mail.
            </div>
        @elseif (session('status'))
            <div class="alert alert-info mb-3" role="alert">
                {{ session('status') }}
            </div>
        @endif
        
        <form action="{{ route('login') }}" method="POST" autocomplete="off">
          @csrf

          <!-- Adres E-mail -->
          <div class="mb-3">
            <label class="form-label">Adres E-mail</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus placeholder="nazwa@domena.pl">
            @error('email') 
              <div class="invalid-feedback">{{ $message }}</div> 
            @enderror
          </div>

          <!-- Hasło -->
          <div class="mb-2">
            <label class="form-label">
              Hasło
              @if (Route::has('password.request'))
                <span class="form-label-description">
                  <a href="{{ route('password.request') }}">Zapomniałeś hasła?</a>
                </span>
              @endif
            </label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required placeholder="Hasło">
            @error('password') 
              <div class="invalid-feedback">{{ $message }}</div> 
            @enderror
          </div>

          <!-- Zapamiętaj mnie -->
          <div class="mb-3">
            <label class="form-check">
              <input type="checkbox" name="remember" class="form-check-input"/>
              <span class="form-check-label">Zapamiętaj mnie na tym urządzeniu</span>
            </label>
          </div>

          <!-- Przycisk zaloguj -->
          <div class="form-footer">
            <button type="submit" class="btn btn-primary w-full">Zaloguj się</button>
          </div>
        </form>

       

      </div>
    </div>

    @if (Route::has('register'))
      <div class="text-center text-secondary mt-3">
        Nie masz jeszcze konta? <a href="{{ route('register') }}" tabindex="-1">Zarejestruj się</a>
      </div>
    @endif
</div>
@endsection
