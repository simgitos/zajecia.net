@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-surface d-flex justify-content-between align-items-center">
                <h3 class="card-title fw-bold">
                    <i class="ti ti-edit me-2 text-primary"></i> Edytuj sezon: {{ $season->name }}
                </h3>
                <a href="{{ route('admin.seasons.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ti ti-arrow-left me-1"></i> Powrót do listy
                </a>
            </div>

            <form action="{{ route('admin.seasons.update', $season->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                            <h4 class="alert-title"><i class="ti ti-alert-triangle me-1"></i> Wystąpiły błędy w formularzu:</h4>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label required">Nazwa sezonu / semestru</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', $season->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label required">Data rozpoczęcia</label>
                            <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" 
                                   value="{{ old('start_date', $season->start_date?->format('Y-m-d')) }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Data zakończenia</label>
                            <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" 
                                   value="{{ old('end_date', $season->end_date?->format('Y-m-d')) }}" required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                   {{ old('is_active', $season->is_active) ? 'checked' : '' }}>
                            <span class="form-check-label fw-semibold">Ustaw jako bieżący aktywny sezon placówki</span>
                        </label>
                        <small class="form-hint">Aktywny sezon określa aktualną ofertę zapisów dla rodziców i bieżący grafik.</small>
                    </div>
                </div>

                <div class="card-footer bg-light-lt d-flex justify-content-between align-items-center">
                    <a href="{{ route('admin.seasons.index') }}" class="btn btn-link link-secondary">Anuluj</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i> Zapisz zmiany
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
