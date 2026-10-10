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
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required placeholder="np. Lekcje gry na pianinie (indywidualne) lub Język Angielski A1">
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
                            <small class="form-hint">Zostanie automatycznie zapisana w bazie placówki.</small>
                            @error('new_room_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Typ zajęć -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label required">Typ zajęć</label>
                            <select name="type" id="course_type_select" class="form-select @error('type') is-invalid @enderror" required onchange="handleTypeChange()">
                                <option value="group" {{ old('type', 'group') === 'group' ? 'selected' : '' }}>Grupowe</option>
                                <option value="individual" {{ old('type') === 'individual' ? 'selected' : '' }}>Indywidualne (1 os./sesja)</option>
                            </select>
                            <small class="form-hint" id="type_hint">Zajęcia grupowe z wieloma uczestnikami jednocześnie.</small>
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
                            <label class="form-label required" id="participants_label">Maksymalna liczba uczestników</label>
                            <input type="number" min="1" name="max_participants" id="max_participants_input" class="form-control @error('max_participants') is-invalid @enderror" value="{{ old('max_participants', 15) }}" required>
                            <small class="form-hint" id="participants_hint">Dla zajęć indywidualnych określa wielkość kolejki/grafiku instruktora.</small>
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

                        <!-- Sekcja wyboru uczestników (dodawanie wielu jednocześnie) -->
                        <div class="col-md-12 mb-4">
                            <div class="card border">
                                <div class="card-header bg-surface d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="card-title fw-bold mb-0">
                                            <i class="ti ti-users me-2 text-primary"></i> Przypisz uczestników do zajęć (opcjonalnie)
                                        </h4>
                                        <small class="text-secondary">Możesz zaznaczyć wielu uczestników jednocześnie, którzy zostaną od razu zapisani na te zajęcia.</small>
                                    </div>
                                    <div class="col-auto">
                                        <input type="text" id="filter_create_children" class="form-control form-control-sm" placeholder="Szukaj uczestnika...">
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    @if(isset($children) && $children->isNotEmpty())
                                        <div class="table-responsive" style="max-height: 260px; overflow-y: auto;">
                                            <table class="table table-vcenter table-hover card-table mb-0">
                                                <thead>
                                                    <tr>
                                                        <th class="w-1">
                                                            <input type="checkbox" class="form-check-input" id="check_all_create" onclick="toggleAllCreateChildren(this)">
                                                        </th>
                                                        <th>Imię i nazwisko</th>
                                                        <th>Wiek</th>
                                                        <th>Opiekun</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="create_children_list">
                                                    @foreach($children as $child)
                                                        <tr class="create-child-row">
                                                            <td>
                                                                <input type="checkbox" name="child_ids[]" value="{{ $child->id }}" class="form-check-input create-child-cb" {{ in_array($child->id, old('child_ids', [])) ? 'checked' : '' }}>
                                                            </td>
                                                            <td>
                                                                <strong class="create-child-name text-dark">{{ $child->name }}</strong>
                                                            </td>
                                                            <td>
                                                                <small class="text-muted">{{ $child->age }} lat</small>
                                                            </td>
                                                            <td>
                                                                <small class="text-muted">{{ $child->parent?->name ?? 'Brak' }}</small>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="p-3 text-center text-muted">
                                            Brak zarejestrowanych uczestników w placówce.
                                        </div>
                                    @endif
                                </div>
                                <!-- Opcja tworzenia osobnych kursów dla każdego uczestnika -->
                                <div class="card-footer bg-surface py-2" id="separate_courses_wrapper">
                                    <label class="form-check mb-0">
                                        <input type="checkbox" name="create_separate_for_each" value="1" class="form-check-input" {{ old('create_separate_for_each') ? 'checked' : '' }}>
                                        <span class="form-check-label">
                                            <strong>Utwórz osobne zajęcia indywidualne dla każdego zaznaczonego uczestnika</strong>
                                            <small class="text-secondary d-block">System utworzy dla każdego zaznaczonego uczestnika osobny rekord zajęć (np. "[Tytuł] - Jan Kowalski"). Pozostaw odznaczone, aby utworzyć jedne zajęcia z kolejką uczestników.</small>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Status aktywności -->
                        <div class="col-md-12 mb-3">
                            <label class="form-check form-switch">
                                <input type="checkbox" name="is_active" class="form-check-input" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                                <span class="form-check-label">Zajęcia aktywne (widoczne w katalogu placówki)</span>
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

function handleTypeChange() {
    const typeSelect = document.getElementById('course_type_select');
    const typeHint = document.getElementById('type_hint');
    const separateWrapper = document.getElementById('separate_courses_wrapper');
    const maxPartInput = document.getElementById('max_participants_input');

    if (typeSelect.value === 'individual') {
        typeHint.innerHTML = '<span class="text-azure fw-bold">Zajęcia indywidualne:</span> Prowadzący realizuje zajęcia z 1 osobą na termin. Kolejność uczestników można ustalać metodą drag & drop.';
        if (separateWrapper) separateWrapper.classList.remove('d-none');
    } else {
        typeHint.innerHTML = 'Zajęcia grupowe z wieloma uczestnikami jednocześnie.';
        if (separateWrapper) separateWrapper.classList.add('d-none');
    }
}

function toggleAllCreateChildren(source) {
    document.querySelectorAll('.create-child-cb').forEach(cb => {
        if (cb.closest('tr').style.display !== 'none') {
            cb.checked = source.checked;
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    handleTypeChange();

    const filterInput = document.getElementById('filter_create_children');
    if (filterInput) {
        filterInput.addEventListener('input', function() {
            const q = this.value.toLowerCase();
            document.querySelectorAll('.create-child-row').forEach(row => {
                const name = row.querySelector('.create-child-name')?.textContent.toLowerCase() || '';
                row.style.display = name.includes(q) ? '' : 'none';
            });
        });
    }
});
</script>
@endsection
