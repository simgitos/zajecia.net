<x-app-layout>
    
<x-user-header title="Historia zajęć" description="Przegląd i historia zrealizowanych zajęć." icon="ti ti-calendar-check"></x-user-header>



    <div class="page-body">
        <div class="container-xl">
            <div class="card shadow-sm border-0 mb-4" style="border-radius:14px; overflow:hidden;">
                <div class="card-header bg-body d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h3 class="fw-bold mb-0" style="font-size:0.95rem;">
                        <i class="ti ti-calendar-check me-2 text-primary"></i> Zrealizowane terminy
                        <span class="badge bg-blue-lt text-primary ms-1" style="font-size:0.72rem;">{{ $recentLessons->count() }}</span>
                    </h3>

                    {{-- Filtr miesiąca i przycisk drukowania --}}
                    <div class="d-flex align-items-center gap-2">
                        <form method="GET" action="{{ url()->current() }}" class="d-flex align-items-center gap-2 m-0 d-print-none">
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

                        @if(!empty($summaryByCourse) && count($summaryByCourse) > 0)
                            <button type="button" class="btn btn-sm btn-outline-secondary d-print-none shadow-xs" onclick="window.print()">
                                <i class="ti ti-printer me-1"></i> Drukuj zestawienie
                            </button>
                        @endif
                    </div>
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
                                    <th class="text-end d-print-none">Akcje</th>
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
                                            @if($lesson->course?->isIndividual())
                                                @php $firstAtt = $lesson->attendances->first(); @endphp
                                                @if($firstAtt && $firstAtt->child)
                                                    <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                                        <span class="fw-semibold text-dark">{{ $firstAtt->child->name }}:</span>
                                                        @switch($firstAtt->status)
                                                            @case('present')
                                                                <span class="badge bg-success-lt text-success fw-semibold"><i class="ti ti-check me-1"></i>Obecny</span>
                                                                @break
                                                            @case('absent')
                                                                <span class="badge bg-danger-lt text-danger fw-semibold"><i class="ti ti-x me-1"></i>Nieobecny</span>
                                                                @break
                                                            @case('excused')
                                                                <span class="badge bg-warning-lt text-warning fw-semibold"><i class="ti ti-clock me-1"></i>Usprawiedliwiony</span>
                                                                @break
                                                        @endswitch
                                                    </div>
                                                @else
                                                    <span class="text-muted">Brak danych</span>
                                                @endif
                                            @else
                                                @php
                                                    $presentCount = $lesson->attendances->where('status', 'present')->count();
                                                    $absentCount = $lesson->attendances->where('status', 'absent')->count();
                                                    $excusedCount = $lesson->attendances->where('status', 'excused')->count();
                                                @endphp
                                                <div class="d-flex align-items-center gap-1 flex-wrap">
                                                    <span class="badge bg-success-lt text-success fw-semibold" title="Obecni">
                                                        <i class="ti ti-check me-1"></i>{{ $presentCount }} obecnych
                                                    </span>
                                                    @if($absentCount > 0)
                                                        <span class="badge bg-danger-lt text-danger fw-semibold" title="Nieobecni">
                                                            <i class="ti ti-x me-1"></i>{{ $absentCount }} nieobecnych
                                                        </span>
                                                    @endif
                                                    @if($excusedCount > 0)
                                                        <span class="badge bg-warning-lt text-warning fw-semibold" title="Usprawiedliwieni">
                                                            <i class="ti ti-clock me-1"></i>{{ $excusedCount }} uspr.
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-end d-print-none">
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

            {{-- Podsumowanie do wydruku i rozliczenia miesiąca --}}
            @if(!empty($summaryByCourse) && count($summaryByCourse) > 0)
                <div class="card shadow-sm border-0 mb-4" style="border-radius:14px; overflow:hidden;">
                    <div class="card-header bg-surface border-bottom d-flex align-items-center justify-content-between">
                        <h3 class="fw-bold mb-0" style="font-size:0.95rem;">
                            <i class="ti ti-file-analytics me-2 text-primary"></i> Podsumowanie rozliczeniowe: {{ \Carbon\Carbon::create()->month($filterMonth)->locale('pl')->isoFormat('MMMM') }} {{ $filterYear }}
                        </h3>
                        <div class="text-secondary small d-none d-print-inline">
                            Prowadzący: <strong>{{ Auth::user()->name }}</strong>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter table-bordered mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 50px;">Lp.</th>
                                    <th>Nazwa zajęć</th>
                                    <th class="text-center" style="width: 140px;">Ilość lekcji</th>
                                    <th class="text-end" style="width: 160px;">Kwota (stawka)</th>
                                    <th class="text-end" style="width: 180px;">Wartość</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($summaryByCourse as $index => $item)
                                    <tr>
                                        <td class="text-secondary">{{ $index + 1 }}</td>
                                        <td>
                                            <strong class="text-dark">{{ $item['course_title'] }}</strong>
                                            @if($item['course']?->billing_type === 'monthly_flat')
                                                <span class="badge bg-secondary-lt text-secondary ms-1" style="font-size:0.7rem;">Ryczałt</span>
                                            @else
                                                <span class="badge bg-blue-lt text-primary ms-1" style="font-size:0.7rem;">Za lekcję</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark fw-bold px-2.5 py-1">
                                                {{ $item['lessons_count'] }}
                                            </span>
                                        </td>
                                        <td class="text-end text-secondary">
                                            {{ number_format((float) $item['price_per_unit'], 2, ',', ' ') }} zł
                                        </td>
                                        <td class="text-end fw-bold text-dark fs-4">
                                            {{ number_format((float) $item['total_value'], 2, ',', ' ') }} zł
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light border-top-2">
                                <tr>
                                    <th colspan="2" class="text-uppercase fw-extrabold text-dark fs-4 text-end pe-3">
                                        SUMA ŁĄCZNA:
                                    </th>
                                    <th class="text-center fw-extrabold text-primary fs-3">
                                        {{ $totalLessonsCount }}
                                    </th>
                                    <th></th>
                                    <th class="text-end fw-extrabold text-success fs-2">
                                        {{ number_format($totalAmountSum, 2, ',', ' ') }} zł
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
