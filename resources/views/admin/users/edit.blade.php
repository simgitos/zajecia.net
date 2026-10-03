@extends('layouts.app')

@section('content')
<div class="row row-cards">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title"><i class="ti ti-user-edit me-2"></i> Edycja użytkownika: {{ $user->name }}</h3>
                <a href="{{ route('admin.user.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ti ti-arrow-left me-1"></i> Powrót
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.user.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Imię i Nazwisko -->
                    <div class="mb-3">
                        <label class="form-label required">Imię i Nazwisko</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Adres Email -->
                    <div class="mb-3">
                        <label class="form-label required">Adres E-mail</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Rola użytkownika -->
                    <div class="mb-3">
                        <label class="form-label required">Rola w systemie</label>
                        <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->value }}" {{ old('role', $user->roles?->first()?->value ?? 'user') === $role->value ? 'selected' : '' }}>
                                    {{ $role->label() }} {{ $role->value === 'teacher' ? '(Instruktor)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-hint">Wybierz "Nauczyciel" (Instruktor), aby nadać uprawnienia instruktorskie.</small>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-footer text-end">
                        <a href="{{ route('admin.user.index') }}" class="btn btn-link link-secondary me-2">Anuluj</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-device-floppy me-1"></i> Zapisz zmiany
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
