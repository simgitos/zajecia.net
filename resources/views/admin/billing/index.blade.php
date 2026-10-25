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
                    Raportowanie płatności w szkole, księgowanie wpłat oraz obsługa zaległości.
                </div>
            </div>
            <!-- Wybór miesiąca / okresu rozliczeniowego i akcja powiadomień -->
            <div class="col-auto ms-auto d-print-none d-flex flex-wrap align-items-center gap-2">
                <form action="{{ route('admin.billing.index') }}" method="GET" class="d-flex align-items-center gap-2 m-0">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <label for="month-select" class="form-label mb-0 me-1 fw-bold text-secondary">Okres:</label>
                    <select id="month-select" name="month" class="form-select bg-surface shadow-sm" onchange="this.form.submit()">
                        @foreach($monthOptions as $value => $label)
                            <option value="{{ $value }}" {{ $month === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </form>

                <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modal-send-billing-summary">
                    <i class="ti ti-bell-ringing me-1"></i> Powiadom rodziców o opłatach
                </button>
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

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-3" role="alert">
                <i class="ti ti-alert-circle me-1"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- 3 Kafelki Raportowe z aktywnym linkowaniem do filtrowania -->
        <div class="row g-4 mb-4">
            <!-- Kafelek 1: Suma rozliczeń (Wszystkie) -->
            <x-stats icon="ti ti-cash" color="primary" title="Suma rozliczeń" value="{{ $totalAmount }}" route="{{ route('admin.billing.index') }}" description="Zarządzaj wpłatami" ></x-stats>
            

            <!-- Kafelek 2: Lista wpłat (Opłacone) -->
             <x-stats icon="ti ti-check" color="success" title="Zaksięgowane wpłaty" value="{{ $totalPaid }}" route="{{ route('admin.billing.index', ['month' => $month, 'status' => 'paid']) }}" description="Lista wpłat" ></x-stats>   
            

            <!-- Kafelek 3: Zaległości / Po terminie (Nieopłacone) -->
            <x-stats icon="ti ti-clock" color="danger" title="Zaległości / Po terminie" value="{{ $totalUnpaid }}" route="{{ route('admin.billing.index', ['month' => $month, 'status' => 'unpaid']) }}" description="Zaległości" ></x-stats>   
            
        </div>

        <!-- Tabela pozycji rozliczeniowych z filtrami zakładek -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-surface d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex align-items-center gap-2">
                    <h3 class="card-title fw-bold mb-0">
                        Pozycje rozliczeniowe ({{ $billingItems->total() }})
                    </h3>
                    <div class="btn-group ms-3" role="group">
                        <a href="{{ route('admin.billing.index', ['month' => $month, 'status' => '']) }}" class="btn btn-sm {{ empty($statusFilter) ? 'btn-primary' : 'btn-outline-secondary' }}">
                            Wszystkie
                        </a>
                        <a href="{{ route('admin.billing.index', ['month' => $month, 'status' => 'unpaid']) }}" class="btn btn-sm {{ $statusFilter === 'unpaid' ? 'btn-danger' : 'btn-outline-secondary' }}">
                            Nieopłacone (Po terminie)
                        </a>
                        <a href="{{ route('admin.billing.index', ['month' => $month, 'status' => 'paid']) }}" class="btn btn-sm {{ $statusFilter === 'paid' ? 'btn-success' : 'btn-outline-secondary' }}">
                            Opłacone (Lista wpłat)
                        </a>
                    </div>
                </div>

                <form action="{{ route('admin.billing.index') }}" method="GET" class="d-flex flex-wrap gap-2">
                    <input type="hidden" name="month" value="{{ $month }}">
                    @if($statusFilter)
                        <input type="hidden" name="status" value="{{ $statusFilter }}">
                    @endif

                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="ti ti-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control" placeholder="Szukaj uczestnika, rodzica lub zajęć..." value="{{ request('search') }}">
                    </div>
                    <button type="submit" class="btn btn-secondary">Szukaj</button>
                    @if(request('search') || request('status') || ($month && $month !== now()->format('Y-m')))
                        <a href="{{ route('admin.billing.index') }}" class="btn btn-outline-secondary">Resetuj filtry</a>
                    @endif
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-vcenter card-table table-hover">
                    <thead>
                        <tr>
                            <th class="w-1">#</th>
                            <th>Okres</th>
                            <th>Uczestnik</th>
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
                                    <span class="badge bg-light text-dark fw-bold">{{ $item->year_month }}</span>
                                </td>
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
                                        <span class="text-muted">Zajęcia usunięte</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1 flex-wrap mb-1">
                                        <span class="badge bg-success-lt text-success" title="Obecności (płatne)">
                                            <i class="ti ti-check me-0.5"></i>{{ $item->present_count }} obecn.
                                        </span>
                                        <span class="badge {{ $item->absent_count > 0 ? 'bg-danger-lt text-danger' : 'bg-secondary-lt text-secondary opacity-50' }}" title="Nieobecności (płatne)">
                                            <i class="ti ti-x me-0.5"></i>{{ $item->absent_count }} nieobecn.
                                        </span>
                                        <span class="badge {{ $item->excused_count > 0 ? 'bg-warning-lt text-warning' : 'bg-secondary-lt text-secondary opacity-50' }}" title="Usprawiedliwione (bezpłatne)">
                                            <i class="ti ti-clock me-0.5"></i>{{ $item->excused_count }} uspr.
                                        </span>
                                    </div>
                                    <small class="text-secondary d-block">
                                        Odbytych lekcji: <strong>{{ $item->realized_lessons_count }}</strong>
                                        @if(in_array($item->billing_type, ['per_lesson_monthly', 'per_lesson_single']))
                                            &bull; Płatnych: <strong class="text-primary">{{ $item->present_count + $item->absent_count }}</strong>
                                        @endif
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
                                            <small class="text-muted d-block">{{ $item->paid_at->format('d.m.Y H:i') }}</small>
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
                                                                Uczestnik: <strong>{{ $item->child?->name }}</strong><br>
                                                                Zajęcia: <strong>{{ $item->course?->title }}</strong> ({{ $item->year_month }})<br>
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
                                <td colspan="10" class="text-center text-muted py-5">
                                    <i class="ti ti-receipt-off fs-1 d-block mb-2 text-secondary"></i>
                                    Brak pozycji rozliczeniowych w wybranym filtrze.
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

{{-- Modal wysyłki powiadomień o rozliczeniach miesiąca --}}
<div class="modal modal-blur fade" id="modal-send-billing-summary" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0">
            <form action="{{ route('admin.notifications.billing-summary') }}" method="POST">
                @csrf
                <div class="modal-header bg-surface">
                    <h5 class="modal-title fw-bold">
                        <i class="ti ti-bell-ringing me-1.5 text-primary"></i> Powiadom rodziców o rozliczeniach
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-secondary small mb-3">
                        System automatycznie podsumuje kwoty i roześle spersonalizowane powiadomienia do rodziców z linkiem bezpośrednim do płatności online.
                    </p>

                    <div class="mb-3">
                        <label class="form-label required">Miesiąc / Okres rozliczeniowy</label>
                        <select name="year_month" class="form-select" required>
                            @foreach($monthOptions as $value => $label)
                                @if($value !== 'all')
                                    <option value="{{ $value }}" {{ ($month === $value || ($month === 'all' && $value === now()->format('Y-m'))) ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Grupa docelowa rodziców</label>
                        <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column gap-2">
                            <label class="form-selectgroup-item">
                                <input type="radio" name="target_group" value="unpaid" class="form-selectgroup-input" checked>
                                <div class="form-selectgroup-label d-flex align-items-center p-2.5">
                                    <span class="badge bg-danger-lt me-2 p-1"><i class="ti ti-alert-triangle fs-3"></i></span>
                                    <div class="text-start">
                                        <div class="font-weight-medium">Tylko rodzice z nieopłaconymi rachunkami</div>
                                        <div class="text-secondary small">Przypomnienie o zaległej kwocie do uregulowania</div>
                                    </div>
                                </div>
                            </label>
                            <label class="form-selectgroup-item">
                                <input type="radio" name="target_group" value="all" class="form-selectgroup-input">
                                <div class="form-selectgroup-label d-flex align-items-center p-2.5">
                                    <span class="badge bg-primary-lt me-2 p-1"><i class="ti ti-users fs-3"></i></span>
                                    <div class="text-start">
                                        <div class="font-weight-medium">Wszyscy rodzice objęci rozliczeniem w tym miesiącu</div>
                                        <div class="text-secondary small">Zarówno z kwotą do zapłaty, jak i podziękowaniem za opłacenie</div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dodatkowa notatka / komunikat od placówki (opcjonalnie)</label>
                        <textarea name="custom_note" class="form-control" rows="3" placeholder="np. Przypominamy o terminie płatności do 10. dnia miesiąca."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-surface">
                    <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Anuluj</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-send me-1"></i> Wyślij powiadomienia
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
