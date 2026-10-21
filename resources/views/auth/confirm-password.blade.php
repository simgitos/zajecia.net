@extends('layouts.guest')

@section('title', 'Potwierdź hasło - ' . config('app.name', 'Zajęciownia.pl'))

@section('content')
<div class="container container-tight py-4">
    <div class="card card-md shadow-sm">
      <div class="card-body">
        <h2 class="card-title text-center mb-4 fs-2">Potwierdź swoje hasło</h2>
        <p class="text-secondary mb-4 text-center">
            Jest to bezpieczna strefa aplikacji. Wprowadź swoje hasło przed kontynuowaniem.
        </p>

        @if ($errors->any())
            <div class="alert alert-danger mb-3" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('password.confirm') }}" method="POST" autocomplete="off">
          @csrf

          <div class="mb-3">
            <label class="form-label required">Hasło</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                   required autocomplete="current-password" placeholder="Wprowadź hasło">
            @error('password') 
              <div class="invalid-feedback">{{ $message }}</div> 
            @enderror
          </div>

          <div class="form-footer">
            <button type="submit" class="btn btn-primary w-full">Potwierdź</button>
          </div>
        </form>
      </div>
    </div>
</div>
@endsection
