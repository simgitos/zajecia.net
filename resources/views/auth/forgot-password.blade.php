@extends('layouts.guest')

@section('content')
<div class="container container-tight py-4">
    <div class="card card-md">
      <div class="card-body">
        <h2 class="card-title text-center mb-4">Resetowanie hasła</h2>
        <p class="text-secondary mb-4">
            Zapomniałeś hasła? Podaj swój adres e-mail, a prześlemy Ci link do ustawienia nowego hasła.
        </p>

        @if (session('status'))
            <div class="alert alert-success mb-3" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" autocomplete="off">
          @csrf

          <div class="mb-3">
            <label class="form-label">Adres E-mail</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus placeholder="nazwa@domena.pl">
            @error('email') 
              <div class="invalid-feedback">{{ $message }}</div> 
            @enderror
          </div>

          <div class="form-footer">
            <button type="submit" class="btn btn-primary w-full">Wyślij link do resetu hasła</button>
          </div>
        </form>
      </div>
    </div>

    <div class="text-center text-secondary mt-3">
      Wróć do <a href="{{ route('login') }}" tabindex="-1">strony logowania</a>
    </div>
</div>
@endsection
