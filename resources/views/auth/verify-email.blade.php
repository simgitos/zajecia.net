@extends('layouts.guest')

@section('content')
<div class="container container-tight py-4">
    <div class="card card-md">
      <div class="card-body text-center">
        <h2 class="card-title text-center mb-4">Weryfikacja adresu e-mail</h2>

        <p class="text-secondary mb-4">
            Dziękujemy za rejestrację! Zanim rozpoczniesz, zweryfikuj swój adres e-mail, klikając w link, który właśnie do Ciebie wysłaliśmy. Jeśli nie otrzymałeś wiadomości e-mail, z chęcią wyślemy kolejną.
        </p>

        @if (session('status') == 'verification-link-sent')
            <div class="alert alert-success mb-4" role="alert">
                Nowy link weryfikacyjny został wysłany na adres e-mail podany podczas rejestracji.
            </div>
        @endif

        <div class="d-flex flex-column gap-2">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn btn-primary w-full">
                    Wyślij ponownie e-mail aktywacyjny
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-secondary w-full">
                    Wyloguj się
                </button>
            </form>
        </div>
      </div>
    </div>
</div>
@endsection
