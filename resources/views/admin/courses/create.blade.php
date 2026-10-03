@extends('layouts.app')

@section('content')
<div class="row row-cards">
    <div class="col-md-10 offset-md-1">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title"><i class="ti ti-plus me-2"></i> Dodaj nowe zajęcia</h3>
                <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ti ti-arrow-left me-1"></i> Powrót
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.courses.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <!-- Tytuł zajęć -->
                        <div class="col-md-8 mb-3">
                            <label class="form-label required">Tytuł / Nazwa zajęć</label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required placeholder="np. Język Angielski dla Dzieci (Poziom A1)">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Instruktor -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label required">Instruktor / Prowadzący</label>
                            <select name="instructor_id" class="form-select @error('instructor_id') is-invalid @enderror" required>
                                <option value="">-- Wybierz prowadzącego --</option>
                                @foreach($instructors as $instructor)
                                    <option value="{{ $instructor->id }}" {{ old('instructor_id') == $instructor->id ? 'selected' : '' }}>
                                        {{ $instructor->name }} ({{ $instructor->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('instructor_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Sala (wybór z istniejących lub wpisanie nowej) -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sala zajęciowa</label>
                            <select name="room_id" id="room_select" class="form-select @error('room_id') is-invalid @enderror" onchange="toggleNewRoomInput()">
                                <option value="">-- Brak / Wybierz istniejącą salę --</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                        {{ $room->name }} @if($room->location) ({{ $room->location }}) @endif
                                    </option>
                                @endforeach
                                <option value="NEW" {{ old('new_room_name') ? 'selected' : '' }}>+ Dodaj nową salę...</option>
                            </select>
                            @error('room_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3 {{ old('new_room_name') ? '' : 'd-none' }}" id="new_room_wrapper">
                            <label class="form-label text-primary"><i class="ti ti-plus me-1"></i> Nazwa nowej sali</label>
                            <input type="text" name="new_room_name" id="new_room_name" class="form-control @error('new_room_name') is-invalid @enderror" value="{{ old('new_room_name') }}" placeholder="np. Sala 102 (Piętro 1)">
                            <small class="form-hint">Zostanie automatycznie zapisana w bazie szkół.</small>
                            @error('new_room_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Typ zajęć -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label required">Typ zajęć</label>
                            <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="group" {{ old('type', 'group') === 'group' ? 'selected' : '' }}>Grupowe</option>
                                <option value="individual" {{ old('type') === 'individual' ? 'selected' : '' }}>Indywidualne</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Typ rozliczenia -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label required">Sposób rozliczenia</label>
                            <select name="billing_type" class="form-select @error('billing_type') is-invalid @enderror" required>
                                <option value="monthly_flat" {{ old('billing_type', 'monthly_flat') === 'monthly_flat' ? 'selected' : '' }}>Ryczałt miesięczny</option>
                                <option value="per_lesson_monthly" {{ old('billing_type') === 'per_lesson_monthly' ? 'selected' : '' }}>Wg lekcji (płatne miesięcznie)</option>
                                <option value="per_lesson_single" {{ old('billing_type') === 'per_lesson_single' ? 'selected' : '' }}>Pojedyncza lekcja</option>
                            </select>
                            @error('billing_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Cena jednostkowa -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label required">Cena jednostkowa (zł)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" name="price_per_unit" class="form-control @error('price_per_unit') is-invalid @enderror" value="{{ old('price_per_unit', '0.00') }}" required>
                                <span class="input-group-text">PLN</span>
                            </div>
                            @error('price_per_unit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Limit uczestników -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label required">Maksymalna liczba uczestników</label>
                            <input type="number" min="1" name="max_participants" class="form-control @error('max_participants') is-invalid @enderror" value="{{ old('max_participants', 15) }}" required>
                            @error('max_participants')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Minimalny wiek -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Minimalny wiek (lata)</label>
                            <input type="number" min="0" name="min_age" class="form-control @error('min_age') is-invalid @enderror" value="{{ old('min_age') }}" placeholder="opcjonalnie">
                            @error('min_age')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Maksymalny wiek -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Maksymalny wiek (lata)</label>
                            <input type="number" min="0" name="max_age" class="form-control @error('max_age') is-invalid @enderror" value="{{ old('max_age') }}" placeholder="opcjonalnie">
                            @error('max_age')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Opis zajęć -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Opis zajęć</label>
                            <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror" placeholder="Wprowadź krótki opis zajęć, wymagany sprzęt lub szczegóły programu...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status aktywności -->
                        <div class="col-md-12 mb-3">
                            <label class="form-check form-switch">
                                <input type="checkbox" name="is_active" class="form-check-input" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                                <span class="form-check-label">Zajęcia aktywne (widoczne dla uczestników)</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-footer text-end">
                        <a href="{{ route('admin.courses.index') }}" class="btn btn-link link-secondary me-2">Anuluj</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-device-floppy me-1"></i> Zapisz zajęcia
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleNewRoomInput() {
    const roomSelect = document.getElementById('room_select');
    const newRoomWrapper = document.getElementById('new_room_wrapper');
    const newRoomInput = document.getElementById('new_room_name');
    
    if (roomSelect && newRoomWrapper) {
        if (roomSelect.value === 'NEW') {
            newRoomWrapper.classList.remove('d-none');
            newRoomInput.focus();
        } else {
            newRoomWrapper.classList.add('d-none');
            newRoomInput.value = '';
        }
    }
}
</script>
@endsection
