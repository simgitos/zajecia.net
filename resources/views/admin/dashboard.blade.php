<x-app-layout>
    <div class="page-header d-print-none mb-4">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle text-uppercase fw-bold text-secondary">
                        <i class="ti ti-shield-check me-1"></i> Panel Dyrektora / Administratora
                    </div>
                    <h2 class="page-title text-primary fw-extrabold fs-1">
                        Pulpit Zarządzania Placówką
                    </h2>
                    <div class="text-secondary mt-1 fs-4">
                        Przegląd kluczowych wskaźników, obłożenia sal oraz finansów szkoły.
                    </div>
                </div>
                <!-- Szybkie akcje dyrektora -->
                <div class="col-auto ms-auto d-print-none">
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.courses.create') }}" class="btn btn-primary fw-bold shadow-sm">
                            <i class="ti ti-plus me-1 fs-3"></i> Dodaj Zajęcia
                        </a>
                        <a href="{{ route('admin.billing.index') }}" class="btn btn-outline-primary fw-bold">
                            <i class="ti ti-receipt-2 me-1 fs-3"></i> Rozliczenia
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <!-- 4 Kafelki Statystyk -->
            <div class="row row-cards mb-4">
                <!-- Kafelek 1: Active Courses -->
                <div class="col-sm-6 col-lg-3">
                    <div class="card shadow-sm border-0 border-start border-primary border-4 h-100">
                        <div class="card-body p-3 p-sm-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="subheader fw-bold text-uppercase fs-6">Aktywne Zajęcia</div>
                                <span class="avatar bg-blue-subtle text-primary rounded-circle fs-2">
                                    <i class="ti ti-school"></i>
                                </span>
                            </div>
                            <div class="h1 mb-1 fw-extrabold text-dark fs-1">{{ $totalCoursesCount }}</div>
                            <div class="text-secondary small">Wszystkie sekcje i kółka</div>
                        </div>
                    </div>
                </div>

                <!-- Kafelek 2: Total Children -->
                <div class="col-sm-6 col-lg-3">
                    <div class="card shadow-sm border-0 border-start border-success border-4 h-100">
                        <div class="card-body p-3 p-sm-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="subheader fw-bold text-uppercase fs-6">Zapisani Uczestnicy</div>
                                <span class="avatar bg-green-subtle text-success rounded-circle fs-2">
                                    <i class="ti ti-mood-kid"></i>
                                </span>
                            </div>
                            <div class="h1 mb-1 fw-extrabold text-dark fs-1">{{ $totalChildrenCount }}</div>
                            <div class="text-secondary small">Aktywni uczestnicy w placówce</div>
                        </div>
                    </div>
                </div>

                <!-- Kafelek 3: Kadra i Rodzice -->
                <div class="col-sm-6 col-lg-3">
                    <div class="card shadow-sm border-0 border-start border-info border-4 h-100">
                        <div class="card-body p-3 p-sm-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="subheader fw-bold text-uppercase fs-6">Prowadzący / Opiekunowie</div>
                                <span class="avatar bg-purple-subtle text-purple rounded-circle fs-2">
                                    <i class="ti ti-users"></i>
                                </span>
                            </div>
                            <div class="h1 mb-1 fw-extrabold text-dark fs-1">{{ $totalTeachersCount }} / {{ $totalParentsCount }}</div>
                            <div class="text-secondary small">Prowadzący i konta opiekunów</div>
                        </div>
                    </div>
                </div>

                <!-- Kafelek 4: Finanse / Zaległości -->
                <div class="col-sm-6 col-lg-3">
                    <div class="card shadow-sm border-0 border-start border-danger border-4 h-100">
                        <div class="card-body p-3 p-sm-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="subheader fw-bold text-uppercase fs-6">Zaległe Wpłaty</div>
                                <span class="avatar bg-danger text-white rounded-circle fs-2">
                                    <i class="ti ti-currency-zloty"></i>
                                </span>
                            </div>
                            <div class="h1 mb-1 fw-extrabold text-danger fs-1">{{ number_format($unpaidAmount, 2, ',', ' ') }} zł</div>
                            <div class="text-secondary small">Opłacono: {{ number_format($paidAmount, 2, ',', ' ') }} zł</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row row-cards">
                <!-- Lewa kolumna: Obłożenie grup i kursów -->
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-surface border-bottom d-flex align-items-center justify-content-between py-3">
                            <h3 class="card-title fw-bold text-dark mb-0">
                                <i class="ti ti-chart-bar me-2 text-primary"></i> Obłożenie Grup Zajęciowych i Sal
                            </h3>
                            <span class="badge bg-blue-lt fs-5 px-3 py-1">
                                Średnie obłożenie: {{ $occupancyPercentage }}%
                            </span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nazwa zajęć</th>
                                        <th>Instruktor</th>
                                        <th>Sala</th>
                                        <th class="text-center">Obłożenie</th>
                                        <th class="text-end">Akcja</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($courses as $course)
                                        @php
                                            $enrolled = $course->children_count;
                                            $max = $course->max_participants;
                                            $pct = $max > 0 ? round(($enrolled / $max) * 100) : 0;
                                            $isFull = $enrolled >= $max;
                                        @endphp
                                        <tr>
                                            <td>
                                                <strong class="text-dark fs-3 d-block">{{ $course->title }}</strong>
                                                <small class="text-muted">
                                                    Typ: {{ $course->billing_type === 'monthly_flat' ? 'Ryczałt' : ($course->billing_type === 'per_lesson_monthly' ? 'Za termin' : 'Pojedyncze') }}
                                                </small>
                                            </td>
                                            <td>
                                                <span class="fw-semibold text-secondary">
                                                    {{ $course->instructor?->name ?? 'Brak przypisania' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    <i class="ti ti-door me-1"></i> {{ $course->room?->name ?? 'Baza' }}
                                                </span>
                                            </td>
                                            <td class="text-center" style="min-width: 140px;">
                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                    <span class="fw-bold fs-4 {{ $isFull ? 'text-danger' : 'text-primary' }}">
                                                        {{ $enrolled }} / {{ $max }}
                                                    </span>
                                                    @if($isFull)
                                                        <span class="badge bg-danger text-white fs-6">PEŁNA GRUPA</span>
                                                    @else
                                                        <small class="text-muted">{{ $pct }}%</small>
                                                    @endif
                                                </div>
                                                <div class="progress progress-sm">
                                                    <div class="progress-bar {{ $isFull ? 'bg-danger' : ($pct > 75 ? 'bg-warning' : 'bg-primary') }}" 
                                                         style="width: {{ min(100, $pct) }}%" 
                                                         role="progressbar" 
                                                         aria-valuenow="{{ $pct }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100"></div>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-sm btn-ghost-primary">
                                                    <i class="ti ti-edit me-1"></i> Edytuj
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">
                                                Brak utworzonych zajęć w placówce.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Prawa kolumna: Szybkie skróty & Ostatnie Lekcje -->
                <div class="col-lg-4">
                    <!-- Szybkie nawigacje dyrektora -->
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-surface border-bottom py-3">
                            <h3 class="card-title fw-bold text-dark mb-0">
                                <i class="ti ti-pointer me-2 text-success"></i> Szybkie Skróty
                            </h3>
                        </div>
                        <div class="card-body p-3 d-flex flex-column gap-2">
                            <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-primary justify-content-start py-2.5 fw-bold">
                                <i class="ti ti-school me-2 fs-2 text-primary"></i> Zarządzanie Zajęciami
                            </a>
                            <a href="{{ route('admin.user.index') }}" class="btn btn-outline-secondary justify-content-start py-2.5 fw-bold">
                                <i class="ti ti-users me-2 fs-2 text-secondary"></i> Baza Opiekunów
                            </a>
                            <a href="{{ route('admin.children.index') }}" class="btn btn-outline-info justify-content-start py-2.5 fw-bold">
                                <i class="ti ti-users me-2 fs-2 text-info"></i> Lista Uczestników
                            </a>
                            <a href="{{ route('admin.billing.index') }}" class="btn btn-outline-success justify-content-start py-2.5 fw-bold">
                                <i class="ti ti-receipt-2 me-2 fs-2 text-success"></i> Raporty i Rozliczenia
                            </a>
                        </div>
                    </div>

                    <!-- Ostatnie zrealizowane lekcje -->
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-surface border-bottom py-3">
                            <h3 class="card-title fw-bold text-dark mb-0">
                                <i class="ti ti-history me-2 text-warning"></i> Ostatnio Zrealizowane Terminy
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            @if($recentLessons->isEmpty())
                                <div class="p-4 text-center text-muted small">
                                    Brak zrealizowanych lekcji w tym miesiącu.
                                </div>
                            @else
                                <div class="list-group list-group-flush">
                                    @foreach($recentLessons as $lesson)
                                        <div class="list-group-item p-3">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <strong class="text-dark fs-4">{{ $lesson->course?->title }}</strong>
                                                <span class="badge bg-green-subtle text-success small">
                                                    {{ $lesson->attendances_count }} obecnych
                                                </span>
                                            </div>
                                            <div class="text-secondary small">
                                                <i class="ti ti-calendar me-1"></i> {{ $lesson->realized_at ? $lesson->realized_at->format('d.m.Y') : '-' }} • 
                                                <i class="ti ti-user me-1"></i> {{ $lesson->teacher?->name }}
                                            </div>
                                            @if($lesson->topic)
                                                <div class="text-muted small mt-1 italic">
                                                    "{{ $lesson->topic }}"
                                                </div>
                                            @endif
                                        </div>
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
