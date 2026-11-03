<x-app-layout>
    <x-user-header title="Witaj, {{ explode(' ', Auth::user()->name)[0] }} 👋"
        description="Dodawaj i zarządzaj Uczestnikami zajęć, sprawdzaj ich realizację  i wygodnie opłacaj rachunki " />

    <div class="page-body">
        <div class="container-xl">
            <div class="row col-md-9 row-cards g-3">
                @if($children->count() > 0)
                <!-- 1. KAFELEK PŁATNOŚCI / ROZLICZEŃ (100% szerokości na mobile) -->
                <div class="col-12">
                    @if($totalUnpaidAmount > 0)
                        <!-- Wariant A: Gdy są nieopłacone zajęcia -->
                        <div
                            class="card shadow-sm border-0 border-start border-danger border-4 bg-surface rounded-3 overflow-hidden">
                            <div class="card-body p-3 p-sm-4">
                                <div class="row align-items-center g-3">
                                    <div class="col">
                                        <div class="d-flex align-items-center mb-1">
                                            <span
                                                class="avatar avatar-sm bg-danger text-white rounded-circle me-2 shadow-xs">
                                                <i class="ti ti-wallet fs-2"></i>
                                            </span>
                                            <span
                                                class="subheader fw-bold text-uppercase text-danger tracking-wider">Płatności
                                                do uregulowania</span>
                                        </div>
                                        <div class="h1 mb-0 fw-extrabold text-danger fs-1 tracking-tight">
                                            {{ number_format($totalUnpaidAmount, 2, ',', ' ') }} <span
                                                class="fs-3">zł</span>
                                        </div>
                                        <div class="text-secondary small mt-1">
                                            <i class="ti ti-info-circle me-1"></i> Posiadasz nieopłacone pozycje za zajęcia.
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-auto mt-2 mt-sm-0">
                                        <a href="{{ route('user.payments.index') }}"
                                            class="btn btn-danger btn-lg w-100 fw-bold shadow-sm py-2 px-3">
                                            <i class="ti ti-credit-card me-2 fs-2"></i> Szczegóły płatności
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Wariant B: Wszystko opłacone - Podziękowanie i zielony status -->
                        <div
                            class="card shadow-sm border-0 border-start border-success border-4 bg-surface rounded-3 overflow-hidden">
                            <div class="card-body p-3 p-sm-4">
                                <div class="row align-items-center g-3">
                                    <div class="col">
                                        <div class="d-flex align-items-center mb-1">
                                            <span
                                                class="avatar avatar-sm bg-green-subtle text-success rounded-circle me-2 shadow-xs">
                                                <i class="ti ti-circle-check-filled fs-2"></i>
                                            </span>
                                            <span class="subheader fw-bold text-uppercase text-success tracking-wider">Stan
                                                rozliczeń</span>
                                        </div>
                                        <div class="h2 mb-0 fw-bold text-dark fs-2">
                                            Wszystko opłacone! Dziękujemy ❤️
                                        </div>
                                        <div class="text-secondary small mt-1">
                                            Brak jakichkolwiek zaległości. Dziękujemy za terminowe wpłaty.
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-auto mt-2 mt-sm-0">
                                        <a href="{{ route('user.payments.index') }}"
                                            class="btn btn-outline-success w-100 fw-bold py-2 px-3">
                                            <i class="ti ti-receipt me-1.5 fs-3"></i> Szczegóły płatności
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <!-- 2. KAFELEK UCZESTNICY (50% szerokości na mobile) -->
                <x-stats icon="ti ti-mood-kid" color="primary" title="Uczestnicy zajęć" value="{{ $children->count()>0 ? $children->count() : 'Dodaj' }}"
                    route="{{ route('user.children.index') }}" description="Dodaj/Edytuj uczestników"></x-stats>


                <!-- 3. KAFELEK OFERTA ZAJĘĆ (50% szerokości na mobile) -->
                <x-stats icon="ti ti-school" color="secondary" title="Oferta Zajęć" value="Katalog"
                    route="{{ route('user.courses.index') }}" description="Zapisz się na zajęcia"></x-stats>
                @else
                    <!-- Samouczek: Pierwsze kroki (wyświetlany przed dodaniem pierwszego uczestnika) -->
                    <div class="col-12">
                        <div class="card shadow-sm border-0 rounded-3 bg-surface overflow-hidden">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <span class="avatar avatar-sm bg-primary-lt text-primary rounded-circle me-2">
                                        <i class="ti ti-compass fs-2"></i>
                                    </span>
                                    <h3 class="card-title fw-bold text-dark mb-0">Pierwsze kroki – jak zacząć?</h3>
                                </div>
                                <p class="text-secondary mb-4">
                                    Witaj w Zajęciowni! Aby w pełni korzystać z konta, wykonaj poniższe proste kroki:
                                </p>

                                <div class="row g-3">
                                    <div class="col-12 col-md-4">
                                        <div class="p-3 bg-body rounded-3 border h-100">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="badge bg-primary text-white rounded-pill px-2 py-1 me-2 fw-bold">1</span>
                                                <strong class="text-dark">Dodaj uczestnika</strong>
                                            </div>
                                            <p class="text-secondary small mb-0">
                                                Wprowadź dane dziecka lub swoje, aby móc przypisywać uczestnika do zajęć.
                                            </p>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <div class="p-3 bg-body rounded-3 border h-100">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="badge bg-primary-lt text-primary rounded-pill px-2 py-1 me-2 fw-bold">2</span>
                                                <strong class="text-dark">Zapisz na zajęcia</strong>
                                            </div>
                                            <p class="text-secondary small mb-0">
                                                Przejrzyj ofertę w katalogu i zapisz uczestnika na wybrane kursy i grupy.
                                            </p>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <div class="p-3 bg-body rounded-3 border h-100">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="badge bg-primary-lt text-primary rounded-pill px-2 py-1 me-2 fw-bold">3</span>
                                                <strong class="text-dark">Kontroluj i opłacaj</strong>
                                            </div>
                                            <p class="text-secondary small mb-0">
                                                Śledź obecności na zajęciach oraz wygodnie opłacaj składki przez internet.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 text-start">
                                    <a href="{{ route('user.children.index') }}" class="btn btn-primary fw-bold px-3 py-2 shadow-sm">
                                        <i class="ti ti-user-plus me-1.5 fs-2"></i> Dodaj pierwszego uczestnika
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                

            </div>
        </div>
    </div>
</x-app-layout>