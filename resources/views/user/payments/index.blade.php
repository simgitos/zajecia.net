<x-app-layout>
    <div class="page-header d-print-none mb-4">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title text-primary fw-bold fs-2">
                        <i class="ti ti-wallet me-2"></i> Płatności
                    </h2>
                    <div class="text-secondary mt-1 fs-4">
                        Zestawienie nieopłaconych zajęć oraz historia dokonanych wpłat.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm mb-3" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-circle-check fs-2 me-2"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('info'))
                <div class="alert alert-info alert-dismissible fade show shadow-sm mb-3" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle fs-2 me-2"></i>
                        <div>{{ session('info') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-3" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-alert-triangle fs-2 me-2"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- 2 Kafelki podsumowujące (dostosowane pod mobile) -->
            <div class="row row-cards mb-4">
                <!-- Kafelek 1: Do zapłaty -->
                <div class="col-md-5">
                    <div class="card shadow-sm border-0 border-start border-danger border-4 h-100">
                        <div class="card-body d-flex flex-column justify-content-between p-3 p-sm-4">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="subheader fw-bold text-uppercase fs-5">Bieżąca suma do zapłaty
                                        <div class="h1 mb-1 fw-extrabold text-danger fs-1">
                                            {{ number_format($totalUnpaidAmount, 2, ',', ' ') }} zł
                                        </div>
                                    </div>
                                    <span class="avatar bg-danger text-white rounded-circle fs-2">
                                        <i class="ti ti-clock"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="d-flex flex-column gap-2 mt-2">
                                @if($totalUnpaidAmount > 0)
                                    <form action="{{ route('user.payments.pay-all') }}" method="POST" onsubmit="return confirm('Czy na pewno chcesz opłacić online wszystkie nieopłacone zajęcia ({{ number_format($totalUnpaidAmount, 2, ',', ' ') }} zł)?');">
                                        @csrf
                                        <button type="submit" class="btn btn-danger w-100 py-2 fw-bold shadow-sm">
                                            <i class="ti ti-credit-card me-1 fs-3"></i> Zapłać online ({{ number_format($totalUnpaidAmount, 2, ',', ' ') }} zł)
                                        </button>
                                    </form>
                                @else
                                    <button type="button" class="btn btn-secondary w-100 py-2 fw-bold" disabled>
                                        <i class="ti ti-check me-1 fs-3"></i> Wszystkie zajęcia opłacone
                                    </button>
                                @endif
                                <button type="button" class="btn btn-outline-secondary w-100 py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#modal-payment-history">
                                    <i class="ti ti-history me-1 fs-3"></i> Historia wpłat ({{ $paidItems->count() }})
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sekcja z listą dzieci jako Accordion -->
            <div id="unpaid-accordion-section" class="mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h3 class="fw-bold text-dark mb-0 fs-2">
                        <i class="ti ti-mood-kid me-2 text-primary"></i> Szczegóły płatności
                    </h3>
                </div>

                @if(empty($childUnpaidSummaries) || $children->isEmpty())
                    <div class="card card-md shadow-sm text-center py-5 border-0">
                        <div class="card-body">
                            <div class="avatar avatar-xl bg-blue-subtle text-primary rounded-circle mb-3 fs-1">
                                <i class="ti ti-mood-kid"></i>
                            </div>
                            <h3 class="fw-bold">Brak dodanych uczestników</h3>
                            <p class="text-secondary">
                                Dodaj uczestnika do swojego profilu, aby móc zapisywać je na zajęcia.
                            </p>
                            <a href="{{ route('user.children.index') }}" class="btn btn-primary mt-2">
                                <i class="ti ti-plus me-1"></i> Dodaj uczestnika
                            </a>
                        </div>
                    </div>
                @else
                    <div class="accordion shadow-sm rounded border-0" id="accordion-children-unpaid">
                        @foreach($childUnpaidSummaries as $index => $summary)
                            @php
                                $child = $summary['child'];
                                $unpaidTotal = $summary['unpaid_total'];
                                $items = $summary['items'];
                                $hasUnpaid = $unpaidTotal > 0 && !$items->isEmpty();
                                $accordionId = "child-accordion-" . $child->id;
                            @endphp

                            <div class="accordion-item border-0 mb-2 rounded overflow-hidden shadow-sm">
                                <h2 class="accordion-header" id="heading-{{ $child->id }}">
                                    <button class="accordion-button py-3 px-3 px-sm-4 {{ $hasUnpaid ? '' : 'collapsed' }} bg-surface" 
                                            type="button" 
                                            data-bs-toggle="collapse" 
                                            data-bs-target="#{{ $accordionId }}" 
                                            aria-expanded="{{ $hasUnpaid ? 'true' : 'false' }}" 
                                            aria-controls="{{ $accordionId }}">
                                        <div class="d-flex align-items-center w-100 me-2 me-sm-3">
                                            <span class="avatar bg-blue-lt me-2 me-sm-3 fw-bold fs-3 flex-shrink-0">
                                                {{ mb_substr($child->name, 0, 1) }}
                                            </span>
                                            <!-- TYLKO imię i nazwisko dziecka w nagłówku, bez wieku -->
                                            <span class="fw-bold text-dark fs-3 text-truncate">
                                                {{ $child->name }}
                                            </span>
                                            
                                            <div class="ms-auto flex-shrink-0">
                                                @if($hasUnpaid)
                                                    <span class="badge bg-danger text-white fs-4 fw-extrabold px-2 px-sm-3 py-1">
                                                        {{ number_format($unpaidTotal, 2, ',', ' ') }} zł
                                                    </span>
                                                @else
                                                    <span class="badge bg-success-lt fs-4 px-2 px-sm-3 py-1">
                                                        <i class="ti ti-check me-1"></i> Brak zaległości
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </button>
                                </h2>

                                <div id="{{ $accordionId }}" 
                                     class="accordion-collapse collapse {{ $hasUnpaid ? 'show' : '' }}" 
                                     aria-labelledby="heading-{{ $child->id }}" 
                                     data-bs-parent="#accordion-children-unpaid">
                                    <div class="accordion-body p-2 p-sm-3 bg-light">
                                        @if(!$hasUnpaid)
                                            <div class="p-3 text-center bg-surface rounded text-success border border-success-subtle">
                                                <i class="ti ti-circle-check fs-2 d-block mb-1"></i>
                                                Wszystkie zajęcia dla {{ $child->name }} są opłacone!
                                            </div>
                                        @else
                                            <div class="d-flex flex-column gap-2">
                                                @foreach($items as $item)
                                                    @php
                                                        $course = $item->course;
                                                        // Formatowanie miesiąca YYYY-MM na czytelną nazwę
                                                        try {
                                                            $itemDate = \Carbon\Carbon::createFromFormat('Y-m', $item->year_month);
                                                            $monthLabel = $itemDate->translatedFormat('F Y');
                                                        } catch(\Exception $e) {
                                                            $monthLabel = $item->year_month;
                                                        }
                                                    @endphp
                                                    <div class="card shadow-none border p-3 bg-surface rounded">
                                                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
                                                            <div >
                                                                <div class="fw-bold text-dark fs-3">
                                                                    {{ $course?->title ?? 'Zajęcia' }}
                                                                </div>
                                                                <small class="text-secondary d-block mt-1">
                                                                    <i class="ti ti-calendar me-1"></i> Okres: <strong>{{ ucfirst($monthLabel) }}</strong>
                                                                    
                                                                </small>
                                                                @if($item->notes)
                                                                    <small class="text-muted d-block mt-1 bg-light p-1 px-2 rounded">
                                                                        {{ $item->notes }}
                                                                    </small>
                                                                @endif
                                                            </div>
                                                            <div class="d-flex align-items-center justify-content-between justify-content-sm-end w-100 w-sm-auto mt-2 mt-sm-0 pt-2 pt-sm-0 border-top border-sm-0 gap-2">
                                                                <span class="badge bg-danger-lt fs-5">Nieopłacone</span>
                                                                <span class="fw-extrabold text-danger fs-2 me-2">
                                                                    {{ number_format((float) $item->amount, 2, ',', ' ') }} zł
                                                                </span>
                                                                <form action="{{ route('user.payments.pay-item', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Czy na pewno chcesz opłacić online zajęcia &quot;{{ $course?->title }}&quot; ({{ number_format((float) $item->amount, 2, ',', ' ') }} zł)?');">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-sm btn-success fw-bold px-2 py-1 shadow-sm">
                                                                        <i class="ti ti-credit-card me-1"></i> Zapłać
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal: Historia Wpłat -->
    <div class="modal modal-blur fade" id="modal-payment-history" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-primary">
                        <i class="ti ti-history me-1"></i> Historia Zaksięgowanych Wpłat
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    @if($paidItems->isEmpty())
                        <div class="p-4 text-center text-muted">
                            <i class="ti ti-receipt-off fs-1 d-block mb-2 text-secondary"></i>
                            Brak dokonanych wpłat w historii.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table table-hover">
                                <thead>
                                    <tr>
                                        <th>Data wpłaty</th>
                                        <th>Uczestnik & Zajęcia</th>
                                        <th>Forma płatności</th>
                                        <th class="text-end">Kwota</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($paidItems as $paid)
                                        <tr>
                                            <td>
                                                <strong class="text-dark d-block">
                                                    {{ $paid->paid_at ? $paid->paid_at->format('d.m.Y H:i') : '-' }}
                                                </strong>
                                                <small class="text-muted">Okres: {{ $paid->year_month }}</small>
                                            </td>
                                            <td>
                                                <strong class="text-dark d-block">{{ $paid->child?->name }}</strong>
                                                <small class="text-secondary">{{ $paid->course?->title }}</small>
                                            </td>
                                            <td>
                                                @switch($paid->payment_method)
                                                    @case('online')
                                                        <span class="badge bg-blue-lt"><i class="ti ti-credit-card me-1"></i> Płatność online</span>
                                                        @break
                                                    @case('cash')
                                                        <span class="badge bg-green-lt"><i class="ti ti-cash me-1"></i> Gotówka</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-purple-lt"><i class="ti ti-building-bank me-1"></i> Przelew bankowy</span>
                                                @endswitch
                                                @if($paid->transaction_id)
                                                    <small class="text-muted d-block">ID: {{ $paid->transaction_id }}</small>
                                                @endif
                                            </td>
                                            <td class="text-end fw-bold text-success fs-3">
                                                + {{ number_format((float) $paid->amount, 2, ',', ' ') }} zł
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary w-100 w-sm-auto ms-auto" data-bs-dismiss="modal">Zamknij</button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
