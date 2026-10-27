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
                <form action="{{ route('admin.courses.store') }}" method="POST" id="courseForm">
                    @csrf

                    <div class="row">
                        <!-- Tytuł zajęć -->
                        <div class="col-md-5 mb-3">
                            <label class="form-label required">Tytuł / Nazwa zajęć</label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required placeholder="np. Lekcje gry na pianinie (indywidualne) lub Język Angielski A1">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Sezon / Semestr -->
                        <div class="col-md-3 mb-3">
                            <label class="form-label required">Sezon / Semestr</label>
                            <select name="season_id" class="form-select @error('season_id') is-invalid @enderror" required>
                                @foreach($seasons as $season)
                                    <option value="{{ $season->id }}" {{ old('season_id', $activeSeason?->id) == $season->id ? 'selected' : '' }}>
                                        {{ $season->name }} {{ $season->is_active ? '(Bieżący)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('season_id')
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

                        <!-- Cena jednostkowa -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label required">Cena</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" name="price_per_unit" class="form-control @error('price_per_unit') is-invalid @enderror" value="{{ old('price_per_unit', '0.00') }}" required>
                                <span class="input-group-text">PLN</span>
                            </div>
                            @error('price_per_unit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Typ rozliczenia -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label required">Sposób rozliczenia</label>
                            <select name="billing_type" class="form-select @error('billing_type') is-invalid @enderror" required>
                                <option value="monthly_flat" {{ old('billing_type', 'monthly_flat') === 'monthly_flat' ? 'selected' : '' }}>Ryczałt miesięczny</option>
                                {{-- <option value="per_lesson_monthly" {{ old('billing_type') === 'per_lesson_monthly' ? 'selected' : '' }}>Wg lekcji (płatne miesięcznie)</option> --}}
                                <option value="per_lesson_single" {{ old('billing_type') === 'per_lesson_single' ? 'selected' : '' }}>Za lekcję</option>
                            </select>
                            @error('billing_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Limit uczestników -->
                        <div class="col-md-4 mb-3" id="max_participants_wrapper">
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
                    </div>

                    {{-- ====================================================================
                         SEKCJA HARMONOGRAMU — dynamicznie przełączana wg typu zajęć
                    ==================================================================== --}}
                    <div class="col-md-12 mb-4" id="schedule_section">
                        <div class="card border border-primary">
                            <div class="card-header bg-primary-lt">
                                <h4 class="card-title fw-bold mb-0">
                                    <i class="ti ti-calendar-time me-2 text-primary"></i> Konfiguracja harmonogramu
                                </h4>
                            </div>
                            <div class="card-body">

                                {{-- ============ WARIANT A: ZAJĘCIA GRUPOWE ============ --}}
                                <div id="schedule_group">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-label required">Dzień tygodnia</label>
                                            <select name="schedule_day_of_week" id="schedule_day_of_week" class="form-select @error('schedule_day_of_week') is-invalid @enderror">
                                                <option value="">-- Wybierz --</option>
                                                <option value="1" {{ old('schedule_day_of_week') == '1' ? 'selected' : '' }}>Poniedziałek</option>
                                                <option value="2" {{ old('schedule_day_of_week') == '2' ? 'selected' : '' }}>Wtorek</option>
                                                <option value="3" {{ old('schedule_day_of_week') == '3' ? 'selected' : '' }}>Środa</option>
                                                <option value="4" {{ old('schedule_day_of_week') == '4' ? 'selected' : '' }}>Czwartek</option>
                                                <option value="5" {{ old('schedule_day_of_week') == '5' ? 'selected' : '' }}>Piątek</option>
                                                <option value="6" {{ old('schedule_day_of_week') == '6' ? 'selected' : '' }}>Sobota</option>
                                                <option value="7" {{ old('schedule_day_of_week') == '7' ? 'selected' : '' }}>Niedziela</option>
                                            </select>
                                            @error('schedule_day_of_week')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label required">Godzina od</label>
                                            <input type="time" name="schedule_start_time" id="schedule_start_time" class="form-control @error('schedule_start_time') is-invalid @enderror" value="{{ old('schedule_start_time') }}">
                                            @error('schedule_start_time')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label required">Godzina do</label>
                                            <input type="time" name="schedule_end_time" id="schedule_end_time" class="form-control @error('schedule_end_time') is-invalid @enderror" value="{{ old('schedule_end_time') }}">
                                            @error('schedule_end_time')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3" id="frequency_wrapper">
                                            <label class="form-label required">Częstotliwość</label>
                                            <select name="schedule_frequency" id="schedule_frequency" class="form-select @error('schedule_frequency') is-invalid @enderror">
                                                <option value="weekly" {{ old('schedule_frequency', 'weekly') === 'weekly' ? 'selected' : '' }}>Co tydzień</option>
                                                <option value="biweekly" {{ old('schedule_frequency') === 'biweekly' ? 'selected' : '' }}>Co 2 tygodnie</option>
                                                <option value="once" {{ old('schedule_frequency') === 'once' ? 'selected' : '' }}>Jednorazowo</option>
                                            </select>
                                            @error('schedule_frequency')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Podgląd harmonogramu grupowego --}}
                                    <div class="mt-3 p-3 bg-azure-lt rounded-3" id="group_schedule_preview" style="display:none;">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="ti ti-calendar-event text-azure fs-2"></i>
                                            <div>
                                                <div class="fw-bold text-dark" id="group_preview_text"></div>
                                                <small class="text-muted">Harmonogram zostanie automatycznie wygenerowany jako opis</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ============ WARIANT B: ZAJĘCIA INDYWIDUALNE — GENERATOR SLOTÓW ============ --}}
                                <div id="schedule_individual" style="display:none;">
                                    <div class="alert alert-info mb-3">
                                        <div class="d-flex align-items-start">
                                            <i class="ti ti-info-circle me-2 mt-1"></i>
                                            <div>
                                                <strong>Generator slotów indywidualnych</strong><br>
                                                Określ ramy czasowe pracy instruktora, czas trwania lekcji i przerwę techniczną. System automatycznie wygeneruje listę slotów, które możesz edytować.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-label required">Dzień tygodnia</label>
                                            <select id="ind_day_of_week" class="form-select">
                                                <option value="">-- Wybierz --</option>
                                                <option value="1">Poniedziałek</option>
                                                <option value="2">Wtorek</option>
                                                <option value="3">Środa</option>
                                                <option value="4">Czwartek</option>
                                                <option value="5">Piątek</option>
                                                <option value="6">Sobota</option>
                                                <option value="7">Niedziela</option>
                                            </select>
                                        </div>

                                        <div class="col-md-2">
                                            <label class="form-label required">Blok od</label>
                                            <input type="time" id="ind_block_start" class="form-control" value="{{ old('schedule_start_time', '14:00') }}">
                                        </div>

                                        <div class="col-md-2">
                                            <label class="form-label required">Blok do</label>
                                            <input type="time" id="ind_block_end" class="form-control" value="{{ old('schedule_end_time', '18:30') }}">
                                        </div>

                                        <div class="col-md-2">
                                            <label class="form-label required">Czas lekcji</label>
                                            <select name="slot_duration_minutes" id="ind_slot_duration" class="form-select @error('slot_duration_minutes') is-invalid @enderror">
                                                <option value="30" {{ old('slot_duration_minutes') == '30' ? 'selected' : '' }}>30 min</option>
                                                <option value="45" {{ old('slot_duration_minutes', '45') == '45' ? 'selected' : '' }}>45 min</option>
                                                <option value="60" {{ old('slot_duration_minutes') == '60' ? 'selected' : '' }}>60 min</option>
                                            </select>
                                            @error('slot_duration_minutes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-2">
                                            <label class="form-label">Przerwa</label>
                                            <select name="slot_break_minutes" id="ind_slot_break" class="form-select @error('slot_break_minutes') is-invalid @enderror">
                                                <option value="0" {{ old('slot_break_minutes', '0') == '0' ? 'selected' : '' }}>0 min</option>
                                                <option value="5" {{ old('slot_break_minutes') == '5' ? 'selected' : '' }}>5 min</option>
                                                <option value="10" {{ old('slot_break_minutes') == '10' ? 'selected' : '' }}>10 min</option>
                                            </select>
                                            @error('slot_break_minutes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-1 d-flex align-items-end">
                                            <button type="button" class="btn btn-primary w-100" id="btn_generate_slots" onclick="generateSlots()">
                                                <i class="ti ti-bolt"></i>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Lista wygenerowanych slotów --}}
                                    <div id="slots_container" class="mt-3" style="display:none;">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h5 class="mb-0">
                                                <i class="ti ti-list-check me-1 text-green"></i>
                                                Wygenerowane sloty
                                                <span class="badge bg-green ms-1" id="slots_count_badge">0</span>
                                            </h5>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addSlotManually()">
                                                    <i class="ti ti-plus me-1"></i> Dodaj slot
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearAllSlots()">
                                                    <i class="ti ti-trash me-1"></i> Wyczyść
                                                </button>
                                            </div>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-vcenter table-hover card-table mb-0" id="slots_table">
                                                <thead>
                                                    <tr>
                                                        <th class="w-1">#</th>
                                                        <th>Godzina od</th>
                                                        <th>Godzina do</th>
                                                        <th class="text-center">Czas trwania</th>
                                                        <th class="text-center">Aktywny</th>
                                                        <th class="text-end">Akcje</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="slots_body">
                                                </tbody>
                                            </table>
                                        </div>

                                        {{-- Podsumowanie slotów --}}
                                        <div class="mt-2 p-3 bg-green-lt rounded-3" id="slots_summary">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="ti ti-clock-check text-green fs-2"></i>
                                                <div>
                                                    <div class="fw-bold text-dark" id="slots_summary_text"></div>
                                                    <small class="text-muted">Sloty zostaną zapisane z kursem i będą widoczne w harmonogramie</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Hidden inputs do zapisu --}}
                                    <input type="hidden" name="schedule_slots" id="schedule_slots_json">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Stare pole schedule_info jako fallback / dodatkowy opis --}}
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">
                                <i class="ti ti-notes me-1 text-muted"></i> Dodatkowe informacje o harmonogramie
                            </label>
                            <input type="text" name="schedule_info" class="form-control @error('schedule_info') is-invalid @enderror"
                                   value="{{ old('schedule_info') }}"
                                   placeholder="np. Wtorek 15:00, Czwartek 16:30 — widoczne jako tekst w katalogu">
                            <small class="form-hint">Pole tekstowe — opcjonalnie uzupełnij dowolne informacje o terminie (wyświetlane rodzicom).</small>
                            @error('schedule_info')
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
const DAY_NAMES = ['', 'Poniedziałek', 'Wtorek', 'Środa', 'Czwartek', 'Piątek', 'Sobota', 'Niedziela'];
const FREQ_NAMES = { weekly: 'Co tydzień', biweekly: 'Co 2 tygodnie', once: 'Jednorazowo' };

let slotsData = [];

// ========== PRZEŁĄCZANIE TRYBU ==========
function handleTypeChange() {
    const typeSelect = document.getElementById('course_type_select');
    const typeHint = document.getElementById('type_hint');
    const separateWrapper = document.getElementById('separate_courses_wrapper');
    const scheduleGroup = document.getElementById('schedule_group');
    const scheduleIndividual = document.getElementById('schedule_individual');
    const frequencyWrapper = document.getElementById('frequency_wrapper');
    const maxPartWrapper = document.getElementById('max_participants_wrapper');

    if (typeSelect.value === 'individual') {
        typeHint.innerHTML = '<span class="text-azure fw-bold">Zajęcia indywidualne:</span> Prowadzący realizuje zajęcia z 1 osobą na termin. Kolejność uczestników można ustalać metodą drag & drop.';
        if (separateWrapper) separateWrapper.classList.remove('d-none');
        scheduleGroup.style.display = 'none';
        scheduleIndividual.style.display = 'block';
        if (frequencyWrapper) frequencyWrapper.style.display = 'none';
        if (maxPartWrapper) maxPartWrapper.style.display = 'none';
    } else {
        typeHint.innerHTML = 'Zajęcia grupowe z wieloma uczestnikami jednocześnie.';
        if (separateWrapper) separateWrapper.classList.add('d-none');
        scheduleGroup.style.display = 'block';
        scheduleIndividual.style.display = 'none';
        if (frequencyWrapper) frequencyWrapper.style.display = 'block';
        if (maxPartWrapper) maxPartWrapper.style.display = 'block';
    }

    updateGroupPreview();
}

// ========== PODGLĄD HARMONOGRAMU GRUPOWEGO ==========
function updateGroupPreview() {
    const day = document.getElementById('schedule_day_of_week').value;
    const startTime = document.getElementById('schedule_start_time').value;
    const endTime = document.getElementById('schedule_end_time').value;
    const freq = document.getElementById('schedule_frequency').value;
    const previewEl = document.getElementById('group_schedule_preview');
    const previewText = document.getElementById('group_preview_text');

    if (day && startTime && endTime) {
        const dayName = DAY_NAMES[parseInt(day)];
        const freqName = FREQ_NAMES[freq] || '';
        previewText.textContent = `${dayName}, ${startTime} – ${endTime} · ${freqName}`;
        previewEl.style.display = 'block';
    } else {
        previewEl.style.display = 'none';
    }
}

// ========== GENERATOR SLOTÓW INDYWIDUALNYCH ==========
function generateSlots() {
    const dayVal = document.getElementById('ind_day_of_week').value;
    const blockStart = document.getElementById('ind_block_start').value;
    const blockEnd = document.getElementById('ind_block_end').value;
    const duration = parseInt(document.getElementById('ind_slot_duration').value);
    const breakTime = parseInt(document.getElementById('ind_slot_break').value);

    if (!blockStart || !blockEnd) {
        alert('Uzupełnij godzinę rozpoczęcia i zakończenia bloku.');
        return;
    }

    const startMinutes = timeToMinutes(blockStart);
    const endMinutes = timeToMinutes(blockEnd);

    if (startMinutes >= endMinutes) {
        alert('Godzina zakończenia musi być późniejsza niż godzina rozpoczęcia.');
        return;
    }

    slotsData = [];
    let currentStart = startMinutes;

    while (currentStart + duration <= endMinutes) {
        const slotEnd = currentStart + duration;
        slotsData.push({
            start_time: minutesToTime(currentStart),
            end_time: minutesToTime(slotEnd),
            is_active: true
        });
        currentStart = slotEnd + breakTime;
    }

    if (slotsData.length === 0) {
        alert('Nie można wygenerować slotów w podanych ramach czasowych. Sprawdź ustawienia.');
        return;
    }

    // Ustaw hidden fields: dzień tygodnia i ramy bloku
    document.getElementById('schedule_day_of_week').value = dayVal;
    document.getElementById('schedule_start_time').value = blockStart;
    document.getElementById('schedule_end_time').value = blockEnd;

    renderSlots();
    updateSlotsJson();
}

function renderSlots() {
    const tbody = document.getElementById('slots_body');
    tbody.innerHTML = '';
    const container = document.getElementById('slots_container');

    if (slotsData.length === 0) {
        container.style.display = 'none';
        return;
    }

    container.style.display = 'block';
    let activeCount = 0;

    slotsData.forEach((slot, index) => {
        if (slot.is_active) activeCount++;

        const durationMin = timeToMinutes(slot.end_time) - timeToMinutes(slot.start_time);
        const rowClass = slot.is_active ? '' : 'bg-muted-lt text-muted';

        const tr = document.createElement('tr');
        tr.className = rowClass;
        tr.innerHTML = `
            <td class="text-muted">${index + 1}</td>
            <td>
                <input type="time" class="form-control form-control-sm slot-start" value="${slot.start_time}"
                       onchange="updateSlot(${index}, 'start_time', this.value)" style="max-width:130px">
            </td>
            <td>
                <input type="time" class="form-control form-control-sm slot-end" value="${slot.end_time}"
                       onchange="updateSlot(${index}, 'end_time', this.value)" style="max-width:130px">
            </td>
            <td class="text-center">
                <span class="badge ${slot.is_active ? 'bg-azure-lt text-azure' : 'bg-muted-lt text-muted'}">${durationMin} min</span>
            </td>
            <td class="text-center">
                <label class="form-check form-switch mb-0 d-inline-flex">
                    <input type="checkbox" class="form-check-input" ${slot.is_active ? 'checked' : ''}
                           onchange="toggleSlotActive(${index}, this.checked)">
                </label>
            </td>
            <td class="text-end">
                <button type="button" class="btn btn-sm btn-ghost-danger" onclick="removeSlot(${index})" title="Usuń slot">
                    <i class="ti ti-x"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });

    document.getElementById('slots_count_badge').textContent = activeCount;

    const dayVal = document.getElementById('ind_day_of_week').value;
    const dayName = dayVal ? DAY_NAMES[parseInt(dayVal)] : '';
    const summaryParts = [];
    if (dayName) summaryParts.push(dayName);
    summaryParts.push(`${activeCount} aktywnych slotów`);
    if (slotsData.length > 0) {
        const first = slotsData.find(s => s.is_active) || slotsData[0];
        const last = [...slotsData].reverse().find(s => s.is_active) || slotsData[slotsData.length - 1];
        summaryParts.push(`${first.start_time} – ${last.end_time}`);
    }
    document.getElementById('slots_summary_text').textContent = summaryParts.join(' · ');
}

function updateSlot(index, field, value) {
    slotsData[index][field] = value;
    renderSlots();
    updateSlotsJson();
}

function toggleSlotActive(index, active) {
    slotsData[index].is_active = active;
    renderSlots();
    updateSlotsJson();
}

function removeSlot(index) {
    slotsData.splice(index, 1);
    renderSlots();
    updateSlotsJson();
}

function addSlotManually() {
    let lastEnd = '09:00';
    const duration = parseInt(document.getElementById('ind_slot_duration').value) || 45;

    if (slotsData.length > 0) {
        const lastSlot = slotsData[slotsData.length - 1];
        const breakTime = parseInt(document.getElementById('ind_slot_break').value) || 0;
        lastEnd = minutesToTime(timeToMinutes(lastSlot.end_time) + breakTime);
    }

    slotsData.push({
        start_time: lastEnd,
        end_time: minutesToTime(timeToMinutes(lastEnd) + duration),
        is_active: true
    });

    renderSlots();
    updateSlotsJson();
}

function clearAllSlots() {
    if (slotsData.length > 0 && !confirm('Czy na pewno chcesz usunąć wszystkie sloty?')) return;
    slotsData = [];
    renderSlots();
    updateSlotsJson();
}

function updateSlotsJson() {
    document.getElementById('schedule_slots_json').value = JSON.stringify(slotsData);

    // Ustaw max_participants na liczbę aktywnych slotów (dla zajęć indywidualnych)
    const activeSlots = slotsData.filter(s => s.is_active).length;
    if (activeSlots > 0) {
        document.getElementById('max_participants_input').value = activeSlots;
    }
}

// ========== HELPERS ==========
function timeToMinutes(time) {
    const [h, m] = time.split(':').map(Number);
    return h * 60 + m;
}

function minutesToTime(mins) {
    const h = Math.floor(mins / 60).toString().padStart(2, '0');
    const m = (mins % 60).toString().padStart(2, '0');
    return `${h}:${m}`;
}

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

function toggleAllCreateChildren(source) {
    document.querySelectorAll('.create-child-cb').forEach(cb => {
        if (cb.closest('tr').style.display !== 'none') {
            cb.checked = source.checked;
        }
    });
}

// ========== INIT ==========
document.addEventListener('DOMContentLoaded', function() {
    handleTypeChange();

    // Podgląd grupy — aktualizacja przy zmianie
    ['schedule_day_of_week', 'schedule_start_time', 'schedule_end_time', 'schedule_frequency'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('change', updateGroupPreview);
    });

    // Filtr uczestników
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

    // Auto-generuj schedule_info przed submitem
    document.getElementById('courseForm').addEventListener('submit', function() {
        const type = document.getElementById('course_type_select').value;
        const schedInfoField = document.querySelector('input[name="schedule_info"]');

        if (!schedInfoField.value && type === 'group') {
            const day = document.getElementById('schedule_day_of_week').value;
            const start = document.getElementById('schedule_start_time').value;
            const end = document.getElementById('schedule_end_time').value;
            if (day && start && end) {
                const dayName = DAY_NAMES[parseInt(day)];
                schedInfoField.value = `${dayName} ${start} – ${end}`;
            }
        }

        if (!schedInfoField.value && type === 'individual' && slotsData.length > 0) {
            const dayVal = document.getElementById('ind_day_of_week').value;
            const dayName = dayVal ? DAY_NAMES[parseInt(dayVal)] : '';
            const active = slotsData.filter(s => s.is_active);
            if (active.length > 0) {
                schedInfoField.value = `${dayName} ${active[0].start_time} – ${active[active.length-1].end_time} (${active.length} slotów)`;
            }
        }
    });
});
</script>
@endsection
