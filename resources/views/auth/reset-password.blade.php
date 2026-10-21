@extends('layouts.guest')

@section('title', 'Ustaw nowe hasło - ' . config('app.name', 'Zajęciownia.pl'))

@section('content')
<div class="container container-tight py-4">
    <div class="card card-md shadow-sm">
      <div class="card-body">
        <h2 class="card-title text-center mb-4 fs-2">Ustaw nowe hasło</h2>
        <p class="text-secondary mb-4 text-center">
            Wprowadź swój adres e-mail oraz nowe hasło, aby uzyskać dostęp do panelu.
        </p>

        @if (session('status'))
            <div class="alert alert-info mb-3" role="alert">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger mb-3" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('password.store') }}" method="POST" autocomplete="off">
          @csrf

          <!-- Token resetowania hasła -->
          <input type="hidden" name="token" value="{{ $request->route('token') }}">

          <!-- Adres E-mail -->
          <div class="mb-3">
            <label class="form-label required">Adres E-mail</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                   value="{{ old('email', $request->email) }}" required autofocus placeholder="nazwa@domena.pl">
            @error('email') 
              <div class="invalid-feedback">{{ $message }}</div> 
            @enderror
          </div>

          <!-- Nowe Hasło -->
          <div class="mb-3">
            <label class="form-label required">Nowe Hasło</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                   required autocomplete="new-password" placeholder="Minimum 8 znaków">
            @error('password') 
              <div class="invalid-feedback">{{ $message }}</div> 
            @enderror
          </div>

          <!-- Potwierdzenie Hasła -->
          <div class="mb-4">
            <label class="form-label required">Potwierdź nowe hasło</label>
            <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" 
                   required autocomplete="new-password" placeholder="Powtórz nowe hasło">
            @error('password_confirmation') 
              <div class="invalid-feedback">{{ $message }}</div> 
            @enderror
          </div>

          <div class="form-footer">
            <button type="submit" class="btn btn-primary w-full">Zapisz nowe hasło</button>
          </div>
        </form>
      </div>
    </div>

    <div class="text-center text-secondary mt-3">
      Wróć do <a href="{{ route('login') }}" tabindex="-1">strony logowania</a>
    </div>
</div>
@endsection
