<x-app-layout>
    <div class="page-header d-print-none mb-4">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title text-primary fw-bold">
                        <i class="ti ti-wallet me-2"></i> Podsumowanie Płatności
                    </h2>
                    <div class="text-secondary mt-1">
                        Zestawienie opłat za zajęcia Twoich dzieci w wybranym miesiącu rozliczeniowym.
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
                                <div class="subheader text-primary fw-bold text-uppercase fs-5">Suma do zapłaty</div>
                                <div class="ms-auto text-primary fs-2">
                                    <i class="ti ti-cash"></i>
                                </div>
                            </div>
                            <div class="h1 mb-0 fw-extrabold text-primary">
                                {{ number_format($totalAmount, 2, ',', ' ') }} zł
                            </div>
                            <div class="text-secondary mt-1 fs-5">
                                Razem za okres: <strong>{{ $selectedMonthName }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="subheader text-secondary fw-bold text-uppercase fs-5">Miesiąc rozliczeniowy</div>
                                <div class="ms-auto text-secondary fs-2">
                                    <i class="ti ti-calendar"></i>
                                </div>
                            </div>
                            <div class="h2 mb-0 fw-bold text-dark">
                                {{ $selectedMonthName }}
                            </div>
                            <div class="text-secondary mt-1 fs-5">
                                Liczba aktywnych kursów: <strong>{{ $totalCoursesCount }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="subheader text-secondary fw-bold text-uppercase fs-5">Dzieci w systemie</div>
                                <div class="ms-auto text-secondary fs-2">
                                    <i class="ti ti-mood-kid"></i>
                                </div>
                            </div>
                            <div class="h2 mb-0 fw-bold text-dark">
                                {{ count($childSummaries) }} {{ count($childSummaries) === 1 ? 'dziecko' : 'dzieci' }}
                            </div>
                            <div class="text-secondary mt-1 fs-5">
                                Przypisane do Twojego konta
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
                        $courses = $summary['courses'];
                        $childTotal = $summary['child_total'];
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
                                <span class="badge bg-success-lt fs-2 fw-extrabold px-3 py-2">
                                    {{ number_format($childTotal, 2, ',', ' ') }} zł
                                </span>
                            </div>
                        </div>

                        @if(empty($courses))
                            <div class="card-body text-center text-secondary py-4">
                                Dziecko {{ $child->name }} nie jest obecnie zapisane na żadne zajęcia.
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
                                            <th>Sposób wyliczenia kwoty</th>
                                            <th class="text-end">Należność</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($courses as $detail)
                                            @php
                                                $course = $detail['course'];
                                            @endphp
                                            <tr>
                                                <td>
                                                    <strong class="text-dark d-block fs-3">{{ $course->title }}</strong>
                                                    <small class="text-secondary">
                                                        @if($course->instructor)
                                                            Instruktor: {{ $course->instructor->name }} |
                                                        @endif
                                                        @if($course->room)
                                                            Sala: {{ $course->room->name }}
                                                        @endif
                                                    </small>
                                                </td>
                                                <td>
                                                    @switch($detail['billing_type'])
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
                                                        {{ number_format($detail['price_per_unit'], 2, ',', ' ') }} zł
                                                    </strong>
                                                    <small class="text-secondary d-block">
                                                        {{ $detail['billing_type'] === 'monthly_flat' ? 'za miesiąc' : 'za lekcję' }}
                                                    </small>
                                                </td>
                                                <td>
                                                    @if($detail['realized_lessons_count'] === 0)
                                                        <span class="badge bg-secondary-lt">Brak odbytych lekcji</span>
                                                    @else
                                                        <span class="fw-bold text-dark fs-4 d-block">
                                                            {{ $detail['realized_lessons_count'] }} {{ $detail['realized_lessons_count'] === 1 ? 'odbyta lekcja' : 'odbytych lekcji' }}
                                                        </span>
                                                        <small class="text-secondary d-block">
                                                            <span class="text-success fw-bold">{{ $detail['present_count'] }} obecności</span>
                                                            @if($detail['excused_count'] > 0)
                                                                , <span class="text-warning fw-bold">{{ $detail['excused_count'] }} usprawiedliwionych (0 zł)</span>
                                                            @endif
                                                            @if($detail['absent_count'] > 0)
                                                                , <span class="text-danger">{{ $detail['absent_count'] }} nieobecności</span>
                                                            @endif
                                                        </small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small class="text-secondary bg-light px-2 py-1 rounded d-inline-block">
                                                        {{ $detail['calculation_note'] }}
                                                    </small>
                                                </td>
                                                <td class="text-end">
                                                    <strong class="text-dark fs-2">
                                                        {{ number_format($detail['due_amount'], 2, ',', ' ') }} zł
                                                    </strong>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-surface">
                                            <td colspan="5" class="text-end fw-bold text-secondary">
                                                Podsumowanie dla {{ $child->name }}:
                                            </td>
                                            <td class="text-end">
                                                <strong class="text-success fs-2 fw-extrabold">
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
