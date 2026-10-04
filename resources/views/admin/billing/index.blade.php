@extends('layouts.app')

@section('content')
<div class="page-header d-print-none mb-4">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title text-primary fw-bold">
                    <i class="ti ti-receipt-2 me-2"></i> Zarządzanie Rozliczeniami i Wpłatami
                </h2>
                <div class="text-secondary mt-1">
                    Przeglądaj pozycje rozliczeniowe w szkole i rejestruj wpłaty od rodziców.
                </div>
            </div>
            <!-- Wybór miesiąca -->
            <div class="col-auto ms-auto d-print-none">
                <form action="{{ route('admin.billing.index') }}" method="GET" class="d-flex align-items-center gap-2">
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

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm mb-3" role="alert">
                <i class="ti ti-check me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Karty statystyk finansowych -->
        <div class="row row-cards mb-4">
            <div class="col-sm-6 col-lg-4">
                <div class="card shadow-sm border-0 bg-primary-subtle text-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="subheader text-primary fw-bold text-uppercase fs-5">Suma rozliczeń</div>
                            <div class="ms-auto text-primary fs-2">
                                <i class="ti ti-cash"></i>
                            </div>
                        </div>
                        <div class="h1 mb-0 fw-extrabold text-primary">
                            {{ number_format($totalAmount, 2, ',', ' ') }} zł
                        </div>
                        <div class="text-secondary mt-1 fs-5">
                            Za okres: <strong>{{ $selectedMonthName }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-4">
                <div class="card shadow-sm border-0 bg-success-subtle text-success">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="subheader text-success fw-bold text-uppercase fs-5">Zaksięgowane (Opłacone)</div>
                            <div class="ms-auto text-success fs-2">
                                <i class="ti ti-check"></i>
                            </div>
                        </div>
                        <div class="h1 mb-0 fw-bold text-success">
                            {{ number_format($totalPaid, 2, ',', ' ') }} zł
                        </div>
                        <div class="text-secondary mt-1 fs-5">
                            Wpłaty opłacone
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-4">
                <div class="card shadow-sm border-0 bg-danger-subtle text-danger">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="subheader text-danger fw-bold text-uppercase fs-5">Nieopłacone (Do zapłaty)</div>
                            <div class="ms-auto text-danger fs-2">
                                <i class="ti ti-clock"></i>
                            </div>
                        </div>
                        <div class="h1 mb-0 fw-bold text-danger">
                            {{ number_format($totalUnpaid, 2, ',', ' ') }} zł
                        </div>
                        <div class="text-secondary mt-1 fs-5">
                            Zdolność do ściągnięcia wpłat
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabela pozycji rozliczeniowych -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-surface d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h3 class="card-title fw-bold">Pozycje rozliczeniowe ({{ $billingItems->total() }})</h3>
                <form action="{{ route('admin.billing.index') }}" method="GET" class="d-flex flex-wrap gap-2">
                    <input type="hidden" name="month" value="{{ $month }}">
                    <select name="status" class="form-select w-auto" onchange="this.form.submit()">
                        <option value="">Wszystkie statusy</option>
                        <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Nieopłacone</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Opłacone</option>
                    </select>

                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="ti ti-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control" placeholder="Szukaj dziecka, rodzica lub kursu..." value="{{ request('search') }}">
                    </div>
                    <button type="submit" class="btn btn-secondary">Filtruj</button>
                    @if(request('search') || request('status'))
                        <a href="{{ route('admin.billing.index', ['month' => $month]) }}" class="btn btn-outline-secondary">Wyczyść</a>
                    @endif
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-vcenter card-table table-hover">
                    <thead>
                        <tr>
                            <th class="w-1">#</th>
                            <th>Dziecko</th>
                            <th>Rodzic (Kontakt)</th>
                            <th>Zajęcia</th>
                            <th>Lekcje / Obecności</th>
                            <th>Kalkulacja</th>
                            <th>Kwota</th>
                            <th>Status</th>
                            <th class="text-end">Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($billingItems as $index => $item)
                            <tr>
                                <td>{{ $billingItems->firstItem() + $index }}</td>
                                <td>
                                    <strong class="text-dark fs-3">{{ $item->child?->name ?? 'Brak' }}</strong>
                                </td>
                                <td>
                                    @if($item->parent)
                                        <div class="fw-bold">{{ $item->parent->name }}</div>
                                        <small class="text-muted">{{ $item->parent->email }}</small>
                                    @else
                                        <span class="text-muted">Brak danych</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->course)
                                        <a href="{{ route('admin.courses.show', $item->course->id) }}" class="fw-bold text-primary text-decoration-none">
                                            {{ $item->course->title }}
                                        </a>
                                    @else
                                        <span class="text-muted">Kurs usunięty</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="d-block">
                                        Odbytych lekcji: <strong>{{ $item->realized_lessons_count }}</strong>
                                    </small>
                                    <small class="text-muted">
                                        <span class="text-success">{{ $item->present_count }} obecności</span>,
                                        <span class="text-warning">{{ $item->excused_count }} uspr.</span>
                                    </small>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $item->notes ?: '-' }}</small>
                                </td>
                                <td>
                                    <strong class="text-dark fs-3">{{ number_format((float) $item->amount, 2, ',', ' ') }} zł</strong>
                                </td>
                                <td>
                                    @if($item->isPaid())
                                        <span class="badge bg-success text-white">
                                            <i class="ti ti-check me-1"></i> Opłacone
                                        </span>
                                        @if($item->paid_at)
                                            <small class="text-muted d-block">{{ $item->paid_at->format('d.m.Y') }}</small>
                                        @endif
                                    @else
                                        <span class="badge bg-danger text-white">
                                            <i class="ti ti-clock me-1"></i> Nieopłacone
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($item->isPaid())
                                        <form action="{{ route('admin.billing.mark-unpaid', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Cofnąć status opłacenia dla tej pozycji?')">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                <i class="ti ti-rotate-2 me-1"></i> Cofnij wpłatę
                                            </button>
                                        </form>
                                    @else
                                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modal-mark-paid-{{ $item->id }}">
                                            <i class="ti ti-check me-1"></i> Oznacz jako opłacone
                                        </button>

                                        <!-- Modal oznaczania wpłaty -->
                                        <div class="modal modal-blur fade" id="modal-mark-paid-{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content text-start">
                                                    <form action="{{ route('admin.billing.mark-paid', $item) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title fw-bold">Rejestracja wpłaty</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p class="text-secondary mb-3">
                                                                Dziecko: <strong>{{ $item->child?->name }}</strong><br>
                                                                Kwota rozliczenia: <strong class="text-success fs-3">{{ number_format((float) $item->amount, 2, ',', ' ') }} zł</strong>
                                                            </p>

                                                            <div class="mb-3">
                                                                <label class="form-label required">Forma płatności</label>
                                                                <select name="payment_method" class="form-select" required>
                                                                    <option value="transfer" selected>Przelew bankowy</option>
                                                                    <option value="cash">Gotówka</option>
                                                                    <option value="online">Płatność online</option>
                                                                </select>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label">Identyfikator / Nr przelewu <span class="text-secondary">(Opcjonalnie)</span></label>
                                                                <input type="text" name="transaction_id" class="form-control" placeholder="np. PRZ/2026/10/01">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Anuluj</button>
                                                            <button type="submit" class="btn btn-success">
                                                                <i class="ti ti-check me-1"></i> Potwierdź wpłatę
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">
                                    <i class="ti ti-receipt-off fs-1 d-block mb-2 text-secondary"></i>
                                    Brak pozycji rozliczeniowych w wybranym okresie.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($billingItems->hasPages())
                <div class="card-footer d-flex align-items-center">
                    {{ $billingItems->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
