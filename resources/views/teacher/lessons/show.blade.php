<x-app-layout>
    <div class="page-header d-print-none mb-4">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="mb-1">
                        <a href="{{ route('teacher.courses.show', $lesson->course_id) }}" class="text-secondary">
                            <i class="ti ti-arrow-left me-1"></i> Powrót do zajęć: {{ $lesson->course->title }}
                        </a>
                    </div>
                    <h2 class="page-title text-primary fw-bold">
                        <i class="ti ti-checkup-list me-2"></i> Zrealizowana Lekcja: {{ $lesson->realized_at->format('d.m.Y') }}
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <button type="button" onclick="window.print();" class="btn btn-outline-secondary">
                        <i class="ti ti-printer me-1"></i> Drukuj listę
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-surface border-bottom">
                    <h3 class="card-title fw-bold">Podsumowanie lekcji</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <span class="text-secondary d-block fs-5">Data przeprowadzenia</span>
                            <strong class="fs-3 text-dark"><i class="ti ti-calendar me-1 text-primary"></i> {{ $lesson->realized_at->format('d.m.Y') }}</strong>
                        </div>
                        <div class="col-md-5">
                            <span class="text-secondary d-block fs-5">Temat lekcji</span>
                            <strong class="fs-4 text-dark">{{ $lesson->topic ?: 'Brak zdefiniowanego tematu' }}</strong>
                        </div>
                        <div class="col-md-4">
                            <span class="text-secondary d-block fs-5">Statystyka obecności</span>
                            @php
                                $presentCount = $lesson->attendances->where('status', 'present')->count();
                                $totalCount = $lesson->attendances->count();
                            @endphp
                            <span class="badge bg-success-lt fs-3 fw-bold">
                                Obecnych: {{ $presentCount }} / {{ $totalCount }}
                            </span>
                        </div>
                        @if($lesson->notes)
                            <div class="col-12 mt-2">
                                <span class="text-secondary d-block fs-5">Uwagi / Notatka</span>
                                <p class="text-dark mb-0 fs-4">{{ $lesson->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-surface border-bottom">
                    <h3 class="card-title fw-bold">Sprawdzona lista obecności</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table table-hover">
                        <thead>
                            <tr>
                                <th class="w-1">#</th>
                                <th>Imię i nazwisko dziecka</th>
                                <th>Status obecności</th>
                                <th>Uwaga</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lesson->attendances as $index => $att)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong class="text-dark fs-3">{{ $att->child?->name ?? 'Brak danych' }}</strong>
                                        @if($att->child?->age)
                                            <small class="text-muted d-block">({{ $att->child->age }} lat)</small>
                                        @endif
                                    </td>
                                    <td>
                                        @switch($att->status)
                                            @case('present')
                                                <span class="badge bg-success text-white fs-4"><i class="ti ti-check me-1"></i> Obecny</span>
                                                @break
                                            @case('absent')
                                                <span class="badge bg-danger text-white fs-4"><i class="ti ti-x me-1"></i> Nieobecny</span>
                                                @break
                                            @case('excused')
                                                <span class="badge bg-warning text-dark fs-4"><i class="ti ti-clock me-1"></i> Usprawiedliwiony</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        {{ $att->notes ?: '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Brak wpisów obecności</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
