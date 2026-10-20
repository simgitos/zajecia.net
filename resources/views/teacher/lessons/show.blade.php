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
                <!-- <div class="col-auto ms-auto d-print-none">
                    <button type="button" onclick="window.print();" class="btn btn-outline-secondary">
                        <i class="ti ti-printer me-1"></i> Drukuj listę
                    </button>
                </div> -->
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
                            <span class="text-secondary d-block fs-5">Frekwencja</span>
                            @if($lesson->course->isIndividual())
                                @php $firstAtt = $lesson->attendances->first(); @endphp
                                @if($firstAtt && $firstAtt->child)
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="fw-bold fs-4">{{ $firstAtt->child->name }}:</span>
                                        @switch($firstAtt->status)
                                            @case('present')
                                                <span class="badge bg-success text-white fs-4"><i class="ti ti-check me-1"></i>Obecny</span>
                                                @break
                                            @case('absent')
                                                <span class="badge bg-danger text-white fs-4"><i class="ti ti-x me-1"></i>Nieobecny</span>
                                                @break
                                            @case('excused')
                                                <span class="badge bg-warning text-white fs-4"><i class="ti ti-clock me-1"></i>Usprawiedliwiony</span>
                                                @break
                                        @endswitch
                                    </div>
                                @else
                                    <span class="badge bg-azure-lt fs-4">Brak danych uczestnika</span>
                                @endif
                            @else
                                @php
                                    $presentCount = $lesson->attendances->where('status', 'present')->count();
                                    $absentCount = $lesson->attendances->where('status', 'absent')->count();
                                    $excusedCount = $lesson->attendances->where('status', 'excused')->count();
                                    $totalCount = $lesson->attendances->count();
                                @endphp
                                <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                    <span class="badge bg-success-lt text-success fw-bold fs-4" title="Obecni">
                                        <i class="ti ti-check me-1"></i> {{ $presentCount }} obecnych
                                    </span>
                                    @if($absentCount > 0)
                                        <span class="badge bg-danger-lt text-danger fw-bold fs-4" title="Nieobecni">
                                            <i class="ti ti-x me-1"></i> {{ $absentCount }} nieobecnych
                                        </span>
                                    @endif
                                    @if($excusedCount > 0)
                                        <span class="badge bg-warning-lt text-warning fw-bold fs-4" title="Usprawiedliwieni">
                                            <i class="ti ti-clock me-1"></i> {{ $excusedCount }} uspr.
                                        </span>
                                    @endif
                                    <span class="text-secondary small ms-1">(z {{ $totalCount }})</span>
                                </div>
                            @endif
                        </div>
                        @if($lesson->notes)
                            <div class="col-12 mt-2">
                                <span class="text-secondary d-block fs-5">Uwagi / Notatka prowadzącego</span>
                                <p class="text-dark mb-0 fs-4">{{ $lesson->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-surface border-bottom">
                    <h3 class="card-title fw-bold">
                        @if($lesson->course->isIndividual())
                            <i class="ti ti-user me-2 text-primary"></i> Uczestnik zajęć indywidualnych
                        @else
                            <i class="ti ti-users me-2 text-primary"></i> Sprawdzona lista obecności
                        @endif
                    </h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table table-hover">
                        <thead>
                            <tr>
                                <th class="w-1">#</th>
                                <th>Imię i nazwisko uczestnika</th>
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
