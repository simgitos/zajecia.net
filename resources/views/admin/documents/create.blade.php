@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-surface d-flex justify-content-between align-items-center">
                <h3 class="card-title fw-bold mb-0">
                    <i class="ti ti-file-plus me-2 text-primary"></i> Dodaj dokument placówki: {{ $school->name }}
                </h3>
                <a href="{{ route('admin.documents.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ti ti-arrow-left me-1"></i> Powrót do listy
                </a>
            </div>

            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger py-2 mb-3">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.documents.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label required">Tytuł dokumentu</label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="np. Regulamin uczestnictwa w zajęciach 2026/2027" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Typ dokumentu</label>
                            <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="" disabled selected>-- Wybierz typ --</option>
                                @foreach($types as $type)
                                    <option value="{{ $type->value }}" {{ old('type') === $type->value ? 'selected' : '' }}>
                                        {{ $type->label() }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-hint small">Określa przeznaczenie dokumentu (np. regulamin, klauzula RODO).</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Plik dokumentu (PDF, DOC, DOCX, obrazy max 10MB)</label>
                            <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" required accept=".pdf,.doc,.docx,.odt,.rtf,.jpg,.jpeg,.png">
                            <div class="form-hint small">Plik zostanie zapisany w dedykowanym katalogu placówki.</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Opis / Dodatkowe uwagi (opcjonalnie)</label>
                        <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror" placeholder="Krótki opis zawartości dokumentu lub data wejścia w życie...">{{ old('description') }}</textarea>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                                <span class="form-check-label fw-semibold">Dokument aktywny i widoczny</span>
                            </label>
                            <span class="form-hint small">Wyłączenie spowoduje ukrycie dokumentu przed użytkownikami.</span>
                        </div>
                        <div class="col-md-6 mt-2 mt-md-0">
                            <label class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_required" value="1" {{ old('is_required') ? 'checked' : '' }}>
                                <span class="form-check-label fw-semibold">Wymagany do zapoznania</span>
                            </label>
                            <span class="form-hint small">Oznaczenie dokumentu jako kluczowego dla placówki.</span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.documents.index') }}" class="btn btn-secondary">Anuluj</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-device-floppy me-1"></i> Zapisz dokument
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
