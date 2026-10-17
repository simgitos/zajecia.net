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

                    <!-- Role użytkownika -->
                    <div class="mb-3">
                        <label class="form-label required">Role w systemie</label>
                        <div class="row g-2">
                            @foreach($roles as $role)
                                @php
                                    $currentSchoolId = $adminSchoolId ?? Auth::user()->activeSchool()?->id;
                                    $currentRoles = $userSchoolRoles ?? $user->rolesForSchool($currentSchoolId);
                                    $userRoleValues = $currentRoles->map(fn($r) => $r->value)->toArray();
                                    $isChecked = in_array($role->value, old('roles', $userRoleValues), true);
                                @endphp
                                <div class="col-md-4">
                                    <label class="form-selectgroup-item flex-fill">
                                        <input type="checkbox" name="roles[]" value="{{ $role->value }}" class="form-selectgroup-input" {{ $isChecked ? 'checked' : '' }}>
                                        <span class="form-selectgroup-label d-flex align-items-center p-3 text-start">
                                            <span class="me-3">
                                                <i class="ti {{ $role->icon() }} fs-1 text-{{ $role->color() }}"></i>
                                            </span>
                                            <span class="form-selectgroup-title strong d-block">
                                                {{ $role->label() }}
                                                <span class="d-block text-secondary small fw-normal mt-1">
                                                    @if($role->value === 'admin')
                                                        Dostęp do całego panelu szkoły
                                                    @elseif($role->value === 'teacher')
                                                        Prowadzenie kursów i frekwencja
                                                    @else
                                                        Strefa rodzica i zgłoszenia dzieci
                                                    @endif
                                                </span>
                                            </span>
                                        </span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <small class="form-hint mt-2">Możesz zaznaczyć jedną lub więcej ról. Użytkownik posiadający kilka ról będzie mógł przełączać swój widok z poziomu menu.</small>
                        @error('roles')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        @error('roles.*')
                            <div class="text-danger small mt-1">{{ $message }}</div>
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
