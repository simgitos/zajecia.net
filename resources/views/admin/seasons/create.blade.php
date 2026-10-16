@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-surface d-flex justify-content-between align-items-center">
                <h3 class="card-title fw-bold">
                    <i class="ti ti-calendar-plus me-2 text-primary"></i> Dodaj nowy sezon / semestr
                </h3>
                <a href="{{ route('admin.seasons.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ti ti-arrow-left me-1"></i> Powrót do listy
                </a>
            </div>

            <form action="{{ route('admin.seasons.store') }}" method="POST">
                @csrf
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

                    <div class="alert alert-info d-flex align-items-center mb-4">
                        <i class="ti ti-info-circle fs-1 me-3"></i>
                        <div>
                            <strong>Ważna informacja:</strong> Nowy sezon rozpoczyna nowy cykl zapisów. Użytkownicy z ich rolami, historia płatności (oraz ewentualne zaległe rachunki) i profile dzieci pozostają w systemie. Uczestnicy muszą zapisać się na nowo do zajęć w tym sezonie.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Nazwa sezonu / semestru</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name') }}" 
                               placeholder="np. Rok szkolny 2026/2027 lub Semestr letni 2026" required>
                        <small class="form-hint">Czytelna nazwa wyświetlana rodzicom oraz w panelu (np. Rok szkolny 2026/2027, Semestr zimowy).</small>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label required">Data rozpoczęcia</label>
                            <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" 
                                   value="{{ old('start_date', date('Y') . '-09-01') }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Data zakończenia</label>
                            <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" 
                                   value="{{ old('end_date', (date('Y') + 1) . '-06-30') }}" required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    @if($existingSeasons->isNotEmpty())
                        <div class="mb-4 p-3 bg-surface border rounded">
                            <label class="form-label fw-bold">
                                <i class="ti ti-copy me-1 text-primary"></i> Kopiowanie oferty zajęć
                            </label>
                            <select name="copy_courses_from_season_id" class="form-select @error('copy_courses_from_season_id') is-invalid @enderror">
                                <option value="">-- Nie kopiuj (utwórz sezon z pustym katalogiem zajęć) --</option>
                                @foreach($existingSeasons as $prev)
                                    <option value="{{ $prev->id }}" {{ old('copy_courses_from_season_id') == $prev->id ? 'selected' : '' }}>
                                        Skopiuj ofertę z: {{ $prev->name }} ({{ $prev->courses()->count() }} zajęć)
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-hint text-muted mt-1">
                                Kopiowane są szablony zajęć (nazwy, ceny, sale, instruktorzy, opisy). <strong>Uczestnicy NIE są kopiowani</strong> — w nowym sezonie zajęcia będą miały 0 zapisanych dzieci, dzięki czemu rodzice mogą zapisać się na nowo.
                            </small>
                            @error('copy_courses_from_season_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <span class="form-check-label fw-semibold">Ustaw od razu jako bieżący aktywny sezon placówki</span>
                        </label>
                        <small class="form-hint">Jeśli zaznaczone, nowo tworzony sezon stanie się aktywnym punktem odniesienia dla zapisów i grafiku.</small>
                    </div>
                </div>

                <div class="card-footer bg-light-lt d-flex justify-content-between align-items-center">
                    <a href="{{ route('admin.seasons.index') }}" class="btn btn-link link-secondary">Anuluj</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i> Utwórz sezon
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
