<x-app-layout>

<x-user-header title="Pulpit Zarządzania Placówką"
        description="Przegląd kluczowych wskaźników, obłożenia sal oraz finansów szkoły." />
    
    

    <div class="page-body">
        <div class="container-xl">
            <!-- 4 Kafelki Statystyk -->
            <div class="row g-4 mb-4">
                <!-- Kafelek 1: Active Courses -->
                 <x-stats icon="ti ti-school" color="primary" title="Aktywne Zajęcia" value="{{ $totalCoursesCount }}" route="{{ route('admin.courses.index') }}" description="Zarządzaj zajęciami" ></x-stats>
                
                <!-- Kafelek 2: Total Children -->
                <x-stats icon="ti ti-mood-kid" color="success" title="Uczestnicy zajęć" value="{{ $totalChildrenCount }}" route="{{ route('admin.children.index') }}" description="Zarządzaj uczestnikami" ></x-stats>

                <!-- Kafelek 3: Kadra i Rodzice -->
                <x-stats icon="ti ti-users" color="info" title="Prowadzący / Opiekunowie" value="{{ $totalTeachersCount }} / {{ $totalParentsCount }}" route="{{ route('admin.user.index') }}" description="Kadra / Opiekunowie" ></x-stats>
                
                <!-- Kafelek 4: Finanse / Zaległości -->
                <x-stats icon="ti ti-currency-zloty" color="danger" title="Zaległe Wpłaty" value="{{ $unpaidAmount }}" route="{{ route('admin.billing.index') }}" description="Zarządzaj wpłatami" ></x-stats>
                
            
            </div>

            <div class="row row-cards">
                <!-- Lewa kolumna: Obłożenie grup i kursów -->
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 mb-4">
                        <div
                            class="card-header bg-surface border-bottom d-flex align-items-center justify-content-between py-3">
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
                                                    Typ:
                                                    {{ $course->billing_type === 'monthly_flat' ? 'Ryczałt' : ($course->billing_type === 'per_lesson_monthly' ? 'Za termin' : 'Pojedyncze') }}
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
                                                    <span
                                                        class="fw-bold fs-4 {{ $isFull ? 'text-danger' : 'text-primary' }}">
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
                                                        style="width: {{ min(100, $pct) }}%" role="progressbar"
                                                        aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100">
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('admin.courses.edit', $course) }}"
                                                    class="btn btn-sm btn-ghost-primary">
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
                            <a href="{{ route('admin.courses.index') }}"
                                class="btn btn-outline-primary justify-content-start py-2.5 fw-bold">
                                <i class="ti ti-school me-2 fs-2 text-primary"></i> Zarządzanie Zajęciami
                            </a>
                            <a href="{{ route('admin.user.index') }}"
                                class="btn btn-outline-secondary justify-content-start py-2.5 fw-bold">
                                <i class="ti ti-users me-2 fs-2 text-secondary"></i> Użytkownicy
                            </a>
                            <a href="{{ route('admin.children.index') }}"
                                class="btn btn-outline-info justify-content-start py-2.5 fw-bold">
                                <i class="ti ti-users me-2 fs-2 text-info"></i> Uczestnicy
                            </a>
                            <a href="{{ route('admin.billing.index') }}"
                                class="btn btn-outline-success justify-content-start py-2.5 fw-bold">
                                <i class="ti ti-receipt-2 me-2 fs-2 text-success"></i> Raporty i Rozliczenia
                            </a>
                        </div>
                    </div>

                    <!-- Ostatnie zrealizowane lekcje -->
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-surface border-bottom py-3">
                            <h3 class="card-title fw-bold text-dark mb-0">
                                <i class="ti ti-history me-2 text-warning"></i> Ostatnio Zrealizowane Zajęcia
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
                                                <i class="ti ti-calendar me-1"></i>
                                                {{ $lesson->realized_at ? $lesson->realized_at->format('d.m.Y') : '-' }} •
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