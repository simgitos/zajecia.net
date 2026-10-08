<x-app-layout>
    <div class="page-header d-print-none mb-4">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle text-uppercase fw-bold text-secondary">
                        <i class="ti ti-user-check me-1"></i> Panel Nauczyciela & Instruktorów
                    </div>
                    <h2 class="page-title text-success fw-extrabold fs-1">
                        Pulpit Prowadzącego Zajęcia
                    </h2>
                    <div class="text-secondary mt-1 fs-4">
                        Zarządzanie swoimi grupami, realizacja lekcji oraz rejestracja obecności dzieci.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <!-- 3 Kafelki Statystyk -->
            <div class="row row-cards mb-4">
                <!-- Kafelek 1: Moje Kursy -->
                <div class="col-sm-6 col-lg-4">
                    <div class="card shadow-sm border-0 border-start border-success border-4 h-100">
                        <div class="card-body p-3 p-sm-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="subheader fw-bold text-uppercase fs-6">Moje Kursy</div>
                                <span class="avatar bg-green-subtle text-success rounded-circle fs-2">
                                    <i class="ti ti-school"></i>
                                </span>
                            </div>
                            <div class="h1 mb-1 fw-extrabold text-dark fs-1">{{ $teacherCourses->count() }}</div>
                            <div class="text-secondary small">Grupy przypisane do mojego profilu</div>
                        </div>
                    </div>
                </div>

                <!-- Kafelek 2: Lekcje w tym miesiącu -->
                <div class="col-sm-6 col-lg-4">
                    <div class="card shadow-sm border-0 border-start border-primary border-4 h-100">
                        <div class="card-body p-3 p-sm-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="subheader fw-bold text-uppercase fs-6">Przeprowadzone Lekcje</div>
                                <span class="avatar bg-blue-subtle text-primary rounded-circle fs-2">
                                    <i class="ti ti-calendar-event"></i>
                                </span>
                            </div>
                            <div class="h1 mb-1 fw-extrabold text-dark fs-1">{{ $currentMonthLessonsCount }}</div>
                            <div class="text-secondary small">Zrealizowane w bieżącym miesiącu</div>
                        </div>
                    </div>
                </div>

                <!-- Kafelek 3: Przypisani uczniowie -->
                <div class="col-sm-6 col-lg-4">
                    <div class="card shadow-sm border-0 border-start border-warning border-4 h-100">
                        <div class="card-body p-3 p-sm-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="subheader fw-bold text-uppercase fs-6">Moi Uczniowie</div>
                                <span class="avatar bg-warning-subtle text-warning rounded-circle fs-2">
                                    <i class="ti ti-mood-kid"></i>
                                </span>
                            </div>
                            <div class="h1 mb-1 fw-extrabold text-dark fs-1">{{ $assignedChildrenCount }}</div>
                            <div class="text-secondary small">Liczba zapisanych dzieci w moich sekcjach</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sekcja Moje Kursy - Szybkie tworzenie lekcji -->
            <div class="row row-cards mb-4">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-surface border-bottom d-flex align-items-center justify-content-between py-3">
                            <h3 class="card-title fw-bold text-dark mb-0">
                                <i class="ti ti-books me-2 text-success"></i> Przydzielone Zajęcia i Obłożenie
                            </h3>
                            <a href="{{ route('teacher.courses.index') }}" class="btn btn-sm btn-outline-success fw-bold">
                                Zobacz wszystkie kursy
                            </a>
                        </div>
                        <div class="card-body p-3">
                            @if($teacherCourses->isEmpty())
                                <div class="text-center py-5 text-muted">
                                    <i class="ti ti-school-off fs-1 d-block mb-2 text-secondary"></i>
                                    Nie masz obecnie przydzielonych kursów w tej placówce.
                                </div>
                            @else
                                <div class="d-flex flex-column gap-3">
                                    @foreach($teacherCourses as $course)
                                        @php
                                            $enrolled = $course->children_count;
                                            $max = $course->max_participants;
                                        @endphp
                                        <div class="card border p-3 shadow-none bg-surface rounded">
                                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                                                <div>
                                                    <div class="fw-bold text-dark fs-3 mb-1">
                                                        {{ $course->title }}
                                                    </div>
                                                    <div class="text-secondary small">
                                                        <i class="ti ti-door me-1"></i> Sala: <strong>{{ $course->room?->name ?? 'Główna' }}</strong> • 
                                                        <i class="ti ti-users me-1"></i> Obłożenie: <span class="fw-bold text-primary">{{ $enrolled }} / {{ $max }}</span> dzieci
                                                    </div>
                                                </div>

                                                <div class="d-flex flex-wrap align-items-center gap-2 w-100 w-md-auto">
                                                    <a href="{{ route('teacher.courses.show', $course) }}" class="btn btn-outline-secondary btn-sm fw-bold">
                                                        <i class="ti ti-users me-1"></i> Lista Dzieci
                                                    </a>
                                                    <a href="{{ route('teacher.lessons.create', $course) }}" class="btn btn-success btn-sm fw-bold shadow-sm">
                                                        <i class="ti ti-checkup-list me-1"></i> Przeprowadź Lekcję
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Prawa kolumna: Ostatnie lekcje -->
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-surface border-bottom py-3">
                            <h3 class="card-title fw-bold text-dark mb-0">
                                <i class="ti ti-history me-2 text-primary"></i> Moje Ostatnie Lekcje
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            @if($recentLessons->isEmpty())
                                <div class="p-4 text-center text-muted small">
                                    Brak zrealizowanych lekcji w historii.
                                </div>
                            @else
                                <div class="list-group list-group-flush">
                                    @foreach($recentLessons as $lesson)
                                        <a href="{{ route('teacher.lessons.show', $lesson) }}" class="list-group-item list-group-item-action p-3">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <strong class="text-dark fs-4">{{ $lesson->course?->title }}</strong>
                                                <span class="badge bg-green-lt small">
                                                    {{ $lesson->attendances_count }} obecności
                                                </span>
                                            </div>
                                            <div class="text-secondary small">
                                                <i class="ti ti-calendar me-1"></i> Data: {{ $lesson->realized_at ? $lesson->realized_at->format('d.m.Y') : '-' }}
                                            </div>
                                            @if($lesson->topic)
                                                <div class="text-muted small mt-1 text-truncate">
                                                    Temat: "{{ $lesson->topic }}"
                                                </div>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
