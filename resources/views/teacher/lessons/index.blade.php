<x-app-layout>
    <div class="page-header d-print-none mb-3">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="text-secondary fw-semibold" style="font-size:0.8rem; text-transform:uppercase; letter-spacing:.06em;">
                        <i class="ti ti-calendar-check me-1"></i> Instrukcja / Historia zajęć
                    </div>
                    <h2 class="page-title fw-bold mb-0">
                        Przeprowadzone Zajęcia
                    </h2>
                    <div class="text-secondary mt-1" style="font-size:0.85rem;">
                        Przegląd i historia zrealizowanych terminów zajęć.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="card shadow-sm border-0 mb-4" style="border-radius:14px; overflow:hidden;">
                <div class="card-header bg-body d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h3 class="fw-bold mb-0" style="font-size:0.95rem;">
                        <i class="ti ti-calendar-check me-2 text-primary"></i> Zrealizowane terminy
                        <span class="badge bg-blue-lt text-primary ms-1" style="font-size:0.72rem;">{{ $recentLessons->count() }}</span>
                    </h3>

                    {{-- Filtr miesiąca --}}
                    <form method="GET" action="{{ url()->current() }}" class="d-flex align-items-center gap-2">
                        <select name="month" class="form-select form-select-sm" onchange="this.form.submit()">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $m == $filterMonth ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->locale('pl')->isoFormat('MMMM') }}
                                </option>
                            @endfor
                        </select>
                        <select name="year" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                            @for($y = now()->year; $y >= now()->year - 2; $y--)
                                <option value="{{ $y }}" {{ $y == $filterYear ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </form>
                </div>

                @if($recentLessons->isEmpty())
                    <div class="p-5 text-center text-muted">
                        <i class="ti ti-calendar-off d-block mb-2" style="font-size:2.5rem; opacity:0.3;"></i>
                        Brak zrealizowanych terminów w wybranym okresie.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-vcenter table-hover mb-0" style="font-size:0.85rem;">
                            <thead style="background: rgba(var(--tblr-surface-color-rgb),1);">
                                <tr>
                                    <th>Zajęcia</th>
                                    <th>Temat</th>
                                    <th>Data zrealizowania</th>
                                    <th>Obecności</th>
                                    <th class="text-end">Akcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentLessons as $lesson)
                                    <tr>
                                        <td>
                                            <span class="fw-semibold text-dark">{{ $lesson->course?->title ?? '—' }}</span>
                                        </td>
                                        <td class="text-secondary">
                                            {{ $lesson->topic ?? '—' }}
                                        </td>
                                        <td class="text-secondary">
                                            <i class="ti ti-calendar me-1" style="font-size:0.8rem;"></i>
                                            {{ $lesson->realized_at ? $lesson->realized_at->format('d.m.Y') : '—' }}
                                        </td>
                                        <td>
                                            <span class="badge bg-green-lt text-green fw-semibold">
                                                <i class="ti ti-check me-1"></i>{{ $lesson->attendances_count }} obecnych
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('teacher.lessons.show', $lesson) }}"
                                               class="btn btn-sm btn-outline-primary fw-semibold"
                                               style="border-radius:7px; font-size:0.75rem;">
                                                <i class="ti ti-eye me-1"></i>Podgląd
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
