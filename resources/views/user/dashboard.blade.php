<x-app-layout>
    <div class="page-header d-print-none mb-4">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle text-uppercase fw-bold text-secondary">
                        <i class="ti ti-home me-1"></i> Strefa Rodzica / Opiekuna
                    </div>
                    <h2 class="page-title text-primary fw-extrabold fs-1">
                        Pulpit Użytkownika
                    </h2>
                    <div class="text-secondary mt-1 fs-4">
                        Przegląd zapisanych dzieci, nadchodzących zajęć oraz stanu rozliczeń finansowych.
                    </div>
                </div>
                <!-- Szybkie akcje rodzica -->
                <div class="col-auto ms-auto d-print-none">
                    <div class="d-flex gap-2">
                        <a href="{{ route('user.courses.index') }}" class="btn btn-primary fw-bold shadow-sm">
                            <i class="ti ti-school me-1 fs-3"></i> Katalog Zajęć (Zapisy)
                        </a>
                        <a href="{{ route('user.children.index') }}" class="btn btn-outline-secondary fw-bold">
                            <i class="ti ti-plus me-1 fs-3"></i> Moi Uczestnicy
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <!-- 3 Kafelki Podsumowujące -->
            <div class="row row-cards mb-4">
                <!-- Kafelek 1: Moje Dzieci -->
                <div class="col-sm-6 col-lg-4">
                    <div class="card shadow-sm border-0 border-start border-primary border-4 h-100">
                        <div class="card-body p-3 p-sm-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="subheader fw-bold text-uppercase fs-6">Zgłoszeni Uczestnicy</div>
                                <span class="avatar bg-blue-subtle text-primary rounded-circle fs-2">
                                    <i class="ti ti-mood-kid"></i>
                                </span>
                            </div>
                            <div class="h1 mb-1 fw-extrabold text-dark fs-1">{{ $children->count() }}</div>
                            <div class="text-secondary small">Dzieci przypisane do Twojego konta</div>
                        </div>
                    </div>
                </div>

                <!-- Kafelek 2: Do Zapłaty (z przyciskiem Zapłać Online) -->
                <div class="col-sm-6 col-lg-4">
                    <div class="card shadow-sm border-0 border-start border-danger border-4 h-100">
                        <div class="card-body p-3 p-sm-4 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="subheader fw-bold text-uppercase fs-6">Do Zapłaty</div>
                                    <span class="avatar bg-danger text-white rounded-circle fs-2">
                                        <i class="ti ti-clock"></i>
                                    </span>
                                </div>
                                <div class="h1 mb-1 fw-extrabold text-danger fs-1">
                                    {{ number_format($totalUnpaidAmount, 2, ',', ' ') }} zł
                                </div>
                            </div>
                            <div class="mt-2">
                                @if($totalUnpaidAmount > 0)
                                    <form action="{{ route('user.payments.pay-all') }}" method="POST" onsubmit="return confirm('Czy na pewno chcesz opłacić online wszystkie nieopłacone zajęcia ({{ number_format($totalUnpaidAmount, 2, ',', ' ') }} zł)?');">
                                        @csrf
                                        <button type="submit" class="btn btn-danger w-100 btn-sm fw-bold shadow-sm">
                                            <i class="ti ti-credit-card me-1"></i> Zapłać online
                                        </button>
                                    </form>
                                @else
                                    <span class="badge bg-success-lt w-100 py-1.5 fs-5">
                                        <i class="ti ti-check me-1"></i> Brak zaległości
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kafelek 3: Historia i Szczegóły Płatności -->
                <div class="col-sm-6 col-lg-4">
                    <div class="card shadow-sm border-0 border-start border-success border-4 h-100">
                        <div class="card-body p-3 p-sm-4 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="subheader fw-bold text-uppercase fs-6">Opłacone Zaksięgowane</div>
                                    <span class="avatar bg-green-subtle text-success rounded-circle fs-2">
                                        <i class="ti ti-receipt-check"></i>
                                    </span>
                                </div>
                                <div class="h1 mb-1 fw-extrabold text-success fs-1">
                                    {{ number_format($totalPaidAmount, 2, ',', ' ') }} zł
                                </div>
                            </div>
                            <div class="mt-2">
                                <a href="{{ route('user.payments.index') }}" class="btn btn-outline-secondary w-100 btn-sm fw-bold">
                                    <i class="ti ti-wallet me-1"></i> Pełne rozliczenia &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista Dzieci i ich Aktywne Zajęcia -->
            <div class="row row-cards mb-4">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-surface border-bottom d-flex align-items-center justify-content-between py-3">
                            <h3 class="card-title fw-bold text-dark mb-0">
                                <i class="ti ti-mood-kid me-2 text-primary"></i> Moje Dzieci i Zapisy na Zajęcia
                            </h3>
                            <a href="{{ route('user.courses.index') }}" class="btn btn-sm btn-primary fw-bold">
                                <i class="ti ti-plus me-1"></i> Zapisz na nowe zajęcia
                            </a>
                        </div>
                        <div class="card-body p-3">
                            @if($children->isEmpty())
                                <div class="text-center py-5">
                                    <span class="avatar avatar-xl bg-blue-subtle text-primary rounded-circle mb-3 fs-1">
                                        <i class="ti ti-mood-kid"></i>
                                    </span>
                                    <h3 class="fw-bold">Nie dodano jeszcze uczestników</h3>
                                    <p class="text-secondary">
                                        Dodaj swoje dzieci do profilu, aby zapisać je na zajęcia pozalekcyjne.
                                    </p>
                                    <a href="{{ route('user.children.index') }}" class="btn btn-primary mt-2">
                                        <i class="ti ti-plus me-1"></i> Dodaj Uczestnika
                                    </a>
                                </div>
                            @else
                                <div class="d-flex flex-column gap-3">
                                    @foreach($children as $child)
                                        <div class="card border shadow-none bg-surface rounded p-3">
                                            <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                                                <div class="d-flex align-items-center">
                                                    <span class="avatar bg-blue-lt fw-bold fs-2 me-3 rounded-circle">
                                                        {{ mb_substr($child->name, 0, 1) }}
                                                    </span>
                                                    <div>
                                                        <h4 class="fw-bold text-dark mb-0 fs-3">{{ $child->name }}</h4>
                                                        <small class="text-secondary">
                                                            @if($child->birth_date)
                                                                Wiek: <strong>{{ \Carbon\Carbon::parse($child->birth_date)->age }} lat</strong>
                                                            @endif
                                                        </small>
                                                    </div>
                                                </div>
                                                <span class="badge bg-blue-subtle text-primary">
                                                    {{ $child->courses->count() }} kursów
                                                </span>
                                            </div>

                                            <!-- Lista kursów danego dziecka -->
                                            @if($child->courses->isEmpty())
                                                <div class="p-3 text-center bg-light rounded text-muted small">
                                                    Brak aktywnych zapisów na zajęcia dla tego dziecka.
                                                    <a href="{{ route('user.courses.index') }}" class="d-block mt-1 fw-bold text-primary">Zapisz na zajęcia &rarr;</a>
                                                </div>
                                            @else
                                                <div class="d-flex flex-column gap-2">
                                                    @foreach($child->courses as $course)
                                                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center p-2.5 bg-light rounded gap-2">
                                                            <div>
                                                                <strong class="text-dark fs-4">{{ $course->title }}</strong>
                                                                <div class="text-secondary small mt-0.5">
                                                                    <i class="ti ti-user me-1"></i> Prowadzący: <strong>{{ $course->instructor?->name ?? 'Instruktor' }}</strong> • 
                                                                    <i class="ti ti-door me-1"></i> Sala: {{ $course->room?->name ?? 'Główna' }}
                                                                </div>
                                                            </div>
                                                            <span class="badge bg-green-lt fs-6">
                                                                <i class="ti ti-check me-1"></i> Zapisany
                                                            </span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Prawa kolumna: Płatności i Skróty -->
                <div class="col-lg-4">
                    <!-- Moduł Płatności Online -->
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-surface border-bottom py-3">
                            <h3 class="card-title fw-bold text-dark mb-0">
                                <i class="ti ti-wallet me-2 text-danger"></i> Podsumowanie Płatności
                            </h3>
                        </div>
                        <div class="card-body p-3">
                            @if($unpaidItems->isEmpty())
                                <div class="p-3 text-center bg-success-subtle text-success rounded mb-3 border border-success-subtle">
                                    <i class="ti ti-circle-check fs-2 d-block mb-1"></i>
                                    Wszystkie zajęcia są opłacone! Brak zaległości.
                                </div>
                            @else
                                <div class="p-3 bg-danger-subtle text-danger rounded mb-3 border border-danger-subtle text-center">
                                    <div class="small fw-bold text-uppercase">Bieżąca kwota zaległości</div>
                                    <div class="display-6 fw-extrabold my-1">{{ number_format($totalUnpaidAmount, 2, ',', ' ') }} zł</div>
                                    <div class="small">Liczba pozycji: {{ $unpaidItems->count() }}</div>
                                </div>
                                <form action="{{ route('user.payments.pay-all') }}" method="POST" onsubmit="return confirm('Czy na pewno chcesz opłacić online wszystkie nieopłacone zajęcia ({{ number_format($totalUnpaidAmount, 2, ',', ' ') }} zł)?');">
                                    @csrf
                                    <button type="submit" class="btn btn-danger w-100 py-2.5 fw-bold shadow-sm mb-2">
                                        <i class="ti ti-credit-card me-1 fs-3"></i> Zapłać online ({{ number_format($totalUnpaidAmount, 2, ',', ' ') }} zł)
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('user.payments.index') }}" class="btn btn-outline-secondary w-100 py-2 fw-bold">
                                Szczegóły rozliczeń i historia wpłat
                            </a>
                        </div>
                    </div>

                    <!-- Nawigacja Rodzica -->
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-surface border-bottom py-3">
                            <h3 class="card-title fw-bold text-dark mb-0">
                                <i class="ti ti-link me-2 text-primary"></i> Nawigacja Rodzica
                            </h3>
                        </div>
                        <div class="card-body p-3 d-flex flex-column gap-2">
                            <a href="{{ route('user.courses.index') }}" class="btn btn-outline-primary justify-content-start py-2.5 fw-bold">
                                <i class="ti ti-school me-2 fs-2 text-primary"></i> Katalog i Zapisy na Zajęcia
                            </a>
                            <a href="{{ route('user.children.index') }}" class="btn btn-outline-secondary justify-content-start py-2.5 fw-bold">
                                <i class="ti ti-mood-kid me-2 fs-2 text-secondary"></i> Profil Dzieci
                            </a>
                            <a href="{{ route('user.payments.index') }}" class="btn btn-outline-warning justify-content-start py-2.5 fw-bold">
                                <i class="ti ti-wallet me-2 fs-2 text-warning"></i> Zestawienie Opłat
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>