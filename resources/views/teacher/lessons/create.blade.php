<x-app-layout>
    <div class="page-header d-print-none mb-4">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="mb-1">
                        <a href="{{ route('teacher.courses.index') }}" class="text-secondary">
                            <i class="ti ti-arrow-left me-1"></i> Powrót do zajęć
                        </a>
                    </div>
                    <h2 class="page-title text-primary fw-bold">
                        <i class="ti ti-checkup-list me-2"></i> Realizacja zajęć: {{ $course->title }}
                    </h2>
                    <div class="text-secondary mt-1">
                        @if($course->isIndividual())
                            <span class="badge bg-azure-lt me-1"><i class="ti ti-user me-1"></i>Zajęcia indywidualne</span>
                            Wybierz uczestnika i odnotuj jego obecność na pojedynczej sesji.
                        @else
                            <span class="badge bg-purple-lt me-1"><i class="ti ti-users me-1"></i>Zajęcia grupowe</span>
                            Wybierz datę przeprowadzenia zajęć i sprawdź listę obecności całej grupy.
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-3" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-alert-circle fs-2 me-2"></i>
                        <div>
                            <strong>Wystąpiły błędy w formularzu:</strong>
                            <ul class="mb-0 mt-1 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('teacher.lessons.store', $course) }}" method="POST">
                @csrf

                <!-- Szczegóły terminu -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-surface border-bottom">
                        <h3 class="card-title fw-bold"><i class="ti ti-calendar me-2 text-primary"></i> Szczegóły
                            przeprowadzonego terminu</h3>
                    </div>
                    <div class="card-body">
                                                  <div class="col-md-4">
                                <label class="form-label required">Data przeprowadzenia</label>
                                <input type="date" name="realized_at" id="realized_at" class="form-control"
                                    value="{{ old('realized_at', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Temat zajęć</label>
                                <input type="text" name="topic" class="form-control"
                                    placeholder="np. Lekcja indywidualna #4, Wprowadzenie do ćwiczeń..."
                                    value="{{ old('topic') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Uwagi / Notatka prowadzącego</label>
                                <textarea name="notes" class="form-control" rows="2"
                                    placeholder="Opcjonalne uwagi odnośnie postępów lub przebiegu zajęć">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                @if($course->isIndividual())
                    {{-- WARIANT: ZAJĘCIA INDYWIDUALNE (DOKŁADNIE 1 UCZESTNIK NA LIŚCIE OBECNOŚCI) --}}
                    @php
                        $activeChild = $course->children->firstWhere('id', $selectedChildId) ?? $course->children->first();
                        $activeEnrolledDate = $activeChild ? (($activeChild->pivot?->created_at ?? $activeChild->created_at)?->format('Y-m-d')) : null;
                        $activeEnrolledFormatted = $activeChild ? (($activeChild->pivot?->created_at ?? $activeChild->created_at)?->format('d.m.Y')) : null;
                    @endphp

                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-surface border-bottom d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="card-title fw-bold mb-0">
                                    <i class="ti ti-user-check me-2 text-primary"></i> Uczestnik zajęć indywidualnych
                                </h3>
                                <small class="text-secondary">Na liście obecności dla zajęć indywidualnych znajduje się tylko jedna osoba.</small>
                            </div>
                            @if($course->children->count() > 1)
                                <div class="col-auto">
                                    <div class="d-flex align-items-center gap-2">
                                        <label class="form-label mb-0 text-nowrap fw-bold fs-5">Zmień uczestnika:</label>
                                        <select id="individual_child_switcher" class="form-select form-select-sm"
                                            onchange="window.location.href = '{{ route('teacher.lessons.create', $course) }}?child_id=' + this.value">
                                            @foreach($course->children as $index => $child)
                                                <option value="{{ $child->id }}"
                                                    {{ $activeChild && $activeChild->id === $child->id ? 'selected' : '' }}>
                                                    {{ $index + 1 }}. {{ $child->name }} ({{ $child->age }} lat)
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if(!$activeChild)
                            <div class="card-body text-center text-muted py-5">
                                <i class="ti ti-user-off fs-1 d-block mb-2 text-secondary"></i>
                                Brak zapisanych uczestników na te zajęcia. Dodaj uczestnika przed przeprowadzeniem lekcji.
                            </div>
                        @else
                            <input type="hidden" name="child_id" value="{{ $activeChild->id }}">

                            <div class="card-body">
                                {{-- Alert informujący o dacie dołączenia ucznia, gdy data lekcji jest wcześniejsza --}}
                                <div id="individual-enrollment-warning" class="alert alert-danger py-2.5 mb-3 d-none shadow-xs" role="alert">
                                    <div class="d-flex align-items-center">
                                        <i class="ti ti-alert-triangle fs-2 me-2 flex-shrink-0"></i>
                                        <div>
                                            Uczeń <strong>{{ $activeChild->name }}</strong> nie był uczestnikiem zajęć w wybranym dniu lekcji.
                                            Data dołączenia do zajęć to <strong>{{ $activeEnrolledFormatted }}</strong>.
                                            Data lekcji musi być przynajmniej z dnia dodania ucznia.
                                        </div>
                                    </div>
                                </div>

                                <div class="p-3 bg-light rounded border mb-4">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="avatar avatar-lg bg-primary-subtle text-primary fw-bold fs-2 rounded-circle">
                                                {{ mb_substr($activeChild->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <h3 class="mb-1 text-dark fw-bold">{{ $activeChild->name }}</h3>
                                                
                                            </div>
                                            <div class="text-secondary">
                                                <span class="me-3"><i class="ti ti-cake me-1"></i>{{ $activeChild->birth_date ? $activeChild->birth_date->format('d.m.Y') : '-' }} ({{ $activeChild->age }} lat)</span>
                                                @if($activeChild->parent)
                                                    <span><i class="ti ti-user me-1"></i>Opiekun: {{ $activeChild->parent->name }} ({{ $activeChild->parent->email }})</span>
                                                @endif
                                            </div>
                                            @if($activeChild->parent_comment)
                                                <div class="mt-2">
                                                    <span class="badge bg-warning-lt text-wrap">
                                                        <i class="ti ti-alert-triangle me-1"></i> Uwagi opiekuna: {{ $activeChild->parent_comment }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3 align-items-center" id="individual-attendance-inputs">
                                    <div class="col-md-6">
                                        <label class="form-label required fw-bold fs-4 mb-2">Status obecności uczestnika</label>
                                        <div class="btn-group w-100" role="group">
                                            <input type="radio" class="btn-check"
                                                name="attendance[{{ $activeChild->id }}][status]"
                                                id="status_present_{{ $activeChild->id }}" value="present">
                                            <label class="btn btn-outline-success py-2"
                                                for="status_present_{{ $activeChild->id }}">
                                                <i class="ti ti-check me-1 fs-3"></i> Obecny
                                            </label>

                                            <input type="radio" class="btn-check"
                                                name="attendance[{{ $activeChild->id }}][status]"
                                                id="status_absent_{{ $activeChild->id }}" value="absent">
                                            <label class="btn btn-outline-danger py-2"
                                                for="status_absent_{{ $activeChild->id }}">
                                                <i class="ti ti-x me-1 fs-3"></i> Nieobecny
                                            </label>

                                            <input type="radio" class="btn-check"
                                                name="attendance[{{ $activeChild->id }}][status]"
                                                id="status_excused_{{ $activeChild->id }}" value="excused">
                                            <label class="btn btn-outline-warning py-2"
                                                for="status_excused_{{ $activeChild->id }}">
                                                <i class="ti ti-clock me-1 fs-3"></i> Usprawiedliwiony
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold fs-4 mb-2">Uwaga do obecności (opcjonalnie)</label>
                                        <input type="text" name="attendance[{{ $activeChild->id }}][notes]" class="form-control"
                                            placeholder="np. spóźnienie 15 min, zgłoszona niedyspozycja...">
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    {{-- WARIANT: ZAJĘCIA GRUPOWE (CAŁA LISTA OBECNOŚCI DLA GRUPY) --}}
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-surface border-bottom d-flex justify-content-between align-items-center">
                            <h3 class="card-title fw-bold">
                                <i class="ti ti-users me-2 text-primary"></i> Lista obecności grupy (Uczestnicy: {{ $course->children->count() }})
                            </h3>
                        </div>

                        @if($course->children->isEmpty())
                            <div class="card-body text-center text-muted py-5">
                                <i class="ti ti-user-off fs-1 d-block mb-2 text-secondary"></i>
                                Na te zajęcia nie ma jeszcze zapisanych uczestników.
                            </div>
                        @else
                            <div class="card-body p-3">
                                @foreach($course->children as $index => $child)
                                    @php
                                        $cEnrolledDate = ($child->pivot?->created_at ?? $child->created_at)?->format('Y-m-d');
                                        $cEnrolledFormatted = ($child->pivot?->created_at ?? $child->created_at)?->format('d.m.Y');
                                    @endphp
                                    <div class="child-attendance-row mb-3 card border shadow-none"
                                         data-child-id="{{ $child->id }}"
                                         data-child-name="{{ $child->name }}"
                                         data-enrolled-at="{{ $cEnrolledDate }}"
                                         data-enrolled-formatted="{{ $cEnrolledFormatted }}">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div>
                                                    <strong class="text-dark fs-3">{{ $child->name }}</strong>
                                                   
                                                    @if($child->parent_comment)
                                                        <span class="badge bg-warning-lt ms-1">
                                                            <i class="ti ti-alert-triangle me-1"></i> {{ $child->parent_comment }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Komunikat gdy uczeń nie był jeszcze uczestnikiem w dniu lekcji --}}
                                            <div class="attendance-not-enrolled-alert alert alert-warning py-2 px-3 mb-0 d-none">
                                                <i class="ti ti-info-circle me-1"></i>
                                                Uczeń <strong>{{ $child->name }}</strong> nie był uczestnikiem zajęć w dniu lekcji (dołączył: {{ $cEnrolledFormatted }}). Nie zostanie dopisany do listy obecności.
                                            </div>

                                            {{-- Pola formularza obecności --}}
                                            <div class="attendance-inputs row g-2 align-items-center">
                                                <div class="col-md-6">
                                                    <div class="btn-group w-100" role="group">
                                                        <input type="radio" class="btn-check" name="attendance[{{ $child->id }}][status]"
                                                            id="status_present_{{ $child->id }}" value="present">
                                                        <label class="btn btn-outline-success" for="status_present_{{ $child->id }}">
                                                            <i class="ti ti-check me-1"></i> Obecny
                                                        </label>

                                                        <input type="radio" class="btn-check" name="attendance[{{ $child->id }}][status]"
                                                            id="status_absent_{{ $child->id }}" value="absent">
                                                        <label class="btn btn-outline-danger" for="status_absent_{{ $child->id }}">
                                                            <i class="ti ti-x me-1"></i> Nieobecny
                                                        </label>

                                                        <input type="radio" class="btn-check" name="attendance[{{ $child->id }}][status]"
                                                            id="status_excused_{{ $child->id }}" value="excused">
                                                        <label class="btn btn-outline-warning" for="status_excused_{{ $child->id }}">
                                                            <i class="ti ti-clock me-1"></i> Usprawiedliwiony
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" name="attendance[{{ $child->id }}][notes]" class="form-control"
                                                        id="notes_{{ $child->id }}"
                                                        placeholder="np. spóźnienie 15 min, zgłoszona niedyspozycja...">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                <div class="d-flex justify-content-between align-items-center mb-5">
                    <a href="{{ route('teacher.courses.index') }}" class="btn btn-link link-secondary">Anuluj</a>
                    <button type="submit" id="submit-lesson-btn" class="btn btn-success btn-lg" @if($course->children->isEmpty()) disabled @endif>
                        <i class="ti ti-check me-1"></i> Zapisz i zrealizuj
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dateInput = document.getElementById('realized_at');
            const isIndividual = {{ $course->isIndividual() ? 'true' : 'false' }};
            const submitBtn = document.getElementById('submit-lesson-btn');

            function checkEnrollmentDates() {
                const selectedDate = dateInput ? dateInput.value : null;
                if (!selectedDate) return;

                if (isIndividual) {
                    const warningBox = document.getElementById('individual-enrollment-warning');
                    const inputsContainer = document.getElementById('individual-attendance-inputs');
                    const activeChildEnrolledAt = '{{ $activeEnrolledDate ?? '' }}';

                    if (activeChildEnrolledAt && selectedDate < activeChildEnrolledAt) {
                        if (warningBox) warningBox.classList.remove('d-none');
                        if (inputsContainer) inputsContainer.classList.add('opacity-50');
                        dateInput.classList.add('is-invalid');
                        if (submitBtn) {
                            submitBtn.disabled = true;
                            submitBtn.title = 'Data lekcji nie może być wcześniejsza niż data zapisu uczestnika.';
                        }
                    } else {
                        if (warningBox) warningBox.classList.add('d-none');
                        if (inputsContainer) inputsContainer.classList.remove('opacity-50');
                        dateInput.classList.remove('is-invalid');
                        if (submitBtn && {{ $course->children->isNotEmpty() ? 'true' : 'false' }}) {
                            submitBtn.disabled = false;
                            submitBtn.title = '';
                        }
                    }
                } else {
                    const rows = document.querySelectorAll('.child-attendance-row');
                    rows.forEach(function (row) {
                        const enrolledAt = row.getAttribute('data-enrolled-at');
                        const inputsContainer = row.querySelector('.attendance-inputs');
                        const notEnrolledAlert = row.querySelector('.attendance-not-enrolled-alert');
                        const allInputs = row.querySelectorAll('input, select, textarea');

                        if (enrolledAt && selectedDate < enrolledAt) {
                            if (inputsContainer) inputsContainer.classList.add('d-none');
                            if (notEnrolledAlert) notEnrolledAlert.classList.remove('d-none');
                            allInputs.forEach(function (i) {
                                i.disabled = true;
                            });
                        } else {
                            if (inputsContainer) inputsContainer.classList.remove('d-none');
                            if (notEnrolledAlert) notEnrolledAlert.classList.add('d-none');
                            allInputs.forEach(function (i) {
                                i.disabled = false;
                            });
                        }
                    });
                }
            }

            if (dateInput) {
                dateInput.addEventListener('change', checkEnrollmentDates);
                dateInput.addEventListener('input', checkEnrollmentDates);
                checkEnrollmentDates();
            }
        });
    </script>
</x-app-layout>