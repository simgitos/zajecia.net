<x-app-layout>
    <div class="page-header d-print-none mb-4">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title text-primary fw-bold">
                        <i class="ti ti-wallet me-2"></i> Podsumowanie Płatności
                    </h2>
                    <div class="text-secondary mt-1">
                        Zestawienie pozycji rozliczeniowych i statusu płatności za zajęcia Twoich dzieci.
                    </div>
                </div>
                <!-- Wybór miesiąca rozliczeniowego -->
                <div class="col-auto ms-auto d-print-none">
                    <form action="{{ route('user.payments.index') }}" method="GET" class="d-flex align-items-center gap-2">
                        <label for="month-select" class="form-label mb-0 me-1 fw-bold text-secondary">Okres:</label>
                        <select id="month-select" name="month" class="form-select bg-surface shadow-sm" onchange="this.form.submit()">
                            @foreach($monthOptions as $value => $label)
                                <option value="{{ $value }}" {{ $month === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <!-- Podsumowanie finansowe w kartach -->
            <div class="row row-cards mb-4">
                <div class="col-sm-6 col-lg-4">
                    <div class="card shadow-sm border-0 bg-primary-subtle text-primary">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="subheader text-primary fw-bold text-uppercase fs-5">Łączna kwota rozliczenia</div>
                                <div class="ms-auto text-primary fs-2">
                                    <i class="ti ti-cash"></i>
                                </div>
                            </div>
                            <div class="h1 mb-0 fw-extrabold text-primary">
                                {{ number_format($totalAmount, 2, ',', ' ') }} zł
                            </div>
                            <div class="text-secondary mt-1 fs-5">
                                Okres: <strong>{{ $selectedMonthName }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-4">
                    <div class="card shadow-sm border-0 bg-danger-subtle text-danger">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="subheader text-danger fw-bold text-uppercase fs-5">Do zapłaty (Nieopłacone)</div>
                                <div class="ms-auto text-danger fs-2">
                                    <i class="ti ti-clock"></i>
                                </div>
                            </div>
                            <div class="h1 mb-0 fw-bold text-danger">
                                {{ number_format($totalUnpaidAmount, 2, ',', ' ') }} zł
                            </div>
                            <div class="text-secondary mt-1 fs-5">
                                Status: <strong>W oczekiwaniu na wpłatę</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-4">
                    <div class="card shadow-sm border-0 bg-success-subtle text-success">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="subheader text-success fw-bold text-uppercase fs-5">Opłacono</div>
                                <div class="ms-auto text-success fs-2">
                                    <i class="ti ti-check"></i>
                                </div>
                            </div>
                            <div class="h1 mb-0 fw-bold text-success">
                                {{ number_format($totalPaidAmount, 2, ',', ' ') }} zł
                            </div>
                            <div class="text-secondary mt-1 fs-5">
                                Za zaksięgowane wpłaty
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(empty($childSummaries))
                <div class="card card-md shadow-sm text-center py-5 border-0">
                    <div class="card-body">
                        <div class="avatar avatar-xl bg-blue-subtle text-primary rounded-circle mb-3 fs-1">
                            <i class="ti ti-mood-kid"></i>
                        </div>
                        <h3 class="fw-bold">Brak zapisanych dzieci</h3>
                        <p class="text-secondary max-w-md mx-auto">
                            Dodaj dziecko do profilu i zapisz je na zajęcia, aby utworzyć podsumowanie płatności.
                        </p>
                        <a href="{{ route('user.children.index') }}" class="btn btn-primary mt-2">
                            <i class="ti ti-plus me-1"></i> Dodaj pierwsze dziecko
                        </a>
                    </div>
                </div>
            @else
                @foreach($childSummaries as $summary)
                    @php
                        $child = $summary['child'];
                        $items = $summary['items'];
                        $childTotal = $summary['child_total'];
                        $childUnpaid = $summary['child_unpaid'];
                    @endphp

                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-surface border-bottom d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="avatar bg-blue-lt me-3 fw-bold fs-3">
                                    {{ mb_substr($child->name, 0, 1) }}
                                </span>
                                <div>
                                    <h3 class="card-title fw-bold text-dark mb-0">{{ $child->name }}</h3>
                                    <small class="text-secondary">
                                        <i class="ti ti-calendar me-1"></i> Wiek: {{ $child->age }} lat
                                    </small>
                                </div>
                            </div>
                            <div>
                                <span class="text-secondary me-2 fs-4">Należność za dziecko:</span>
                                <span class="badge {{ $childUnpaid > 0 ? 'bg-danger-lt' : 'bg-success-lt' }} fs-2 fw-extrabold px-3 py-2">
                                    {{ number_format($childTotal, 2, ',', ' ') }} zł
                                </span>
                            </div>
                        </div>

                        @if($items->isEmpty())
                            <div class="card-body text-center text-secondary py-4">
                                Dziecko {{ $child->name }} nie posiada jeszcze pozycji rozliczeniowych w tym miesiącu.
                                <div class="mt-2">
                                    <a href="{{ route('user.courses.index') }}" class="btn btn-outline-primary btn-sm">
                                         Przeglądaj katalog zajęć
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nazwa zajęć</th>
                                            <th>Sposób rozliczenia</th>
                                            <th>Stawka jednostkowa</th>
                                            <th>Lekcje w miesiącu</th>
                                            <th>Kalkulacja kwoty</th>
                                            <th>Kwota</th>
                                            <th class="text-end">Status płatności</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $item)
                                            @php
                                                $course = $item->course;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <strong class="text-dark d-block fs-3">{{ $course?->title ?? 'Zajęcia usunięte' }}</strong>
                                                    <small class="text-secondary">
                                                        @if($course?->instructor)
                                                            Instruktor: {{ $course->instructor->name }} |
                                                        @endif
                                                        @if($course?->room)
                                                            Sala: {{ $course->room->name }}
                                                        @endif
                                                    </small>
                                                </td>
                                                <td>
                                                    @switch($item->billing_type)
                                                        @case('monthly_flat')
                                                            <span class="badge bg-purple-lt">
                                                                <i class="ti ti-calendar me-1"></i> Ryczałt miesięczny
                                                            </span>
                                                            @break
                                                        @case('per_lesson_monthly')
                                                            <span class="badge bg-azure-lt">
                                                                <i class="ti ti-history me-1"></i> Wg zrealizowanych lekcji
                                                            </span>
                                                            @break
                                                        @case('per_lesson_single')
                                                            <span class="badge bg-blue-lt">
                                                                <i class="ti ti-ticket me-1"></i> Pojedyncza lekcja
                                                            </span>
                                                            @break
                                                    @endswitch
                                                </td>
                                                <td>
                                                    <strong class="text-dark">
                                                        {{ number_format((float) $item->price_per_unit, 2, ',', ' ') }} zł
                                                    </strong>
                                                </td>
                                                <td>
                                                    @if($item->realized_lessons_count === 0)
                                                        <span class="badge bg-secondary-lt">Brak odbytych lekcji</span>
                                                    @else
                                                        <span class="fw-bold text-dark fs-4 d-block">
                                                            {{ $item->realized_lessons_count }} {{ $item->realized_lessons_count === 1 ? 'odbyta lekcja' : 'odbytych lekcji' }}
                                                        </span>
                                                        <small class="text-secondary d-block">
                                                            <span class="text-success fw-bold">{{ $item->present_count }} obecności</span>
                                                            @if($item->excused_count > 0)
                                                                , <span class="text-warning fw-bold">{{ $item->excused_count }} usprawiedliwionych (0 zł)</span>
                                                            @endif
                                                            @if($item->absent_count > 0)
                                                                , <span class="text-danger">{{ $item->absent_count }} nieobecności</span>
                                                            @endif
                                                        </small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small class="text-secondary bg-light px-2 py-1 rounded d-inline-block">
                                                        {{ $item->notes ?: '-' }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <strong class="text-dark fs-2">
                                                        {{ number_format((float) $item->amount, 2, ',', ' ') }} zł
                                                    </strong>
                                                </td>
                                                <td class="text-end">
                                                    @if($item->isPaid())
                                                        <span class="badge bg-success text-white px-3 py-2 fs-5">
                                                            <i class="ti ti-check me-1"></i> OPŁACONE
                                                        </span>
                                                        @if($item->paid_at)
                                                            <small class="text-muted d-block mt-1">
                                                                {{ $item->paid_at->format('d.m.Y H:i') }}
                                                            </small>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-danger text-white px-3 py-2 fs-5 mb-1">
                                                            <i class="ti ti-clock me-1"></i> DO ZAPŁATY
                                                        </span>
                                                        <!-- Przycisk do przyszłych płatności online -->
                                                        <div class="mt-1">
                                                            <button type="button" class="btn btn-outline-primary btn-sm" disabled title="Płatności online (PayU / Przelewy24 / Stripe) będą dostępne po podłączeniu bramki">
                                                                <i class="ti ti-credit-card me-1"></i> Zapłać online
                                                            </button>
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-surface">
                                            <td colspan="5" class="text-end fw-bold text-secondary">
                                                Podsumowanie dla {{ $child->name }}:
                                            </td>
                                            <td colspan="2" class="text-end">
                                                <strong class="text-primary fs-2 fw-extrabold">
                                                    {{ number_format($childTotal, 2, ',', ' ') }} zł
                                                </strong>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</x-app-layout>
