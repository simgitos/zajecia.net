<footer class="front-footer">
    <div class="container-xl">
        <div class="row g-4 mb-5">
            <!-- O Nas & Logo -->
            <div class="col-lg-4">
                <div class="d-flex align-items-center mb-3">
                    <img src="{{ asset('images/logo.png') }}" alt="Zajęciownia.pl" class="brand-logo-img me-2">
                    <span class="fs-2 fw-extrabold text-white">Zajęciownia.pl</span>
                </div>
                <p class="text-secondary leading-relaxed mb-4">
                    Platforma do obsługi zajęć dowolnego rodzaju: taniec, sport, sztuka, korepetycje, MDK i inne.
                    Zapisy,
                    ewidencja obecności i płatności online w jednym miejscu.
                </p>
                <div class="d-flex gap-2 flex-wrap">
                    <span class="badge bg-blue-subtle text-primary p-2 rounded-2"><i
                            class="ti ti-shield-check me-1"></i>
                        Certyfikat SSL 256-bit</span>
                    <span class="badge bg-green-subtle text-success p-2 rounded-2"><i
                            class="ti ti-currency-zloty me-1"></i>
                        Płatności BLIK & PayU</span>
                </div>
            </div>

            <!-- Szybkie Linki -->
            <div class="col-6 col-lg-2 ms-auto">
                <h5 class="text-white fw-bold mb-3">Nawigacja</h5>
                <ul class="list-unstyled d-flex flex-column gap-2 mb-0">
                    <li><a href="{{ url('/') }}"><i class="ti ti-chevron-right me-1 fs-6"></i> Strona Główna</a></li>
                    <li><a href="{{ url('/#funkcje') }}"><i class="ti ti-chevron-right me-1 fs-6"></i>
                            Funkcjonalności</a></li>
                    <li><a href="{{ url('/#dla-kogo') }}"><i class="ti ti-chevron-right me-1 fs-6"></i> Dla Kogo</a>
                    </li>
                    <li><a href="{{ route('schools.index') }}"><i class="ti ti-chevron-right me-1 fs-6"></i> Katalog
                            Placówek</a></li>
                    <li><a href="{{ url('/#cennik') }}"><i class="ti ti-chevron-right me-1 fs-6"></i> Cennik</a></li>
                    <li><a href="{{ url('/#faq') }}"><i class="ti ti-chevron-right me-1 fs-6"></i> Pytania i
                            Odpowiedzi</a></li>
                </ul>
            </div>

            <!-- Moduły Systemu -->
            <div class="col-6 col-lg-3">
                <h5 class="text-white fw-bold mb-3">Moduły Platformy</h5>
                <ul class="list-unstyled d-flex flex-column gap-2 mb-0">
                    <li><a href="{{ route('login') }}"><i class="ti ti-device-laptop me-1"></i> Panel Administratora</a>
                    </li>
                    <li><a href="{{ route('login') }}"><i class="ti ti-user-check me-1"></i> Panel Prowadzącego</a></li>
                    <li><a href="{{ route('login') }}"><i class="ti ti-wallet me-1"></i> Portal Opiekuna & Płatności</a>
                    </li>
                    <li><a href="{{ route('register') }}"><i class="ti ti-plus me-1"></i> Zarejestruj placówkę</a></li>
                </ul>
            </div>

            <!-- Kontakt & Pomoc -->
            <div class="col-lg-3">
                <h5 class="text-white fw-bold mb-3">Kontakt & Wsparcie</h5>
                <ul class="list-unstyled d-flex flex-column gap-2 mb-3 text-secondary">
                    <li><i class="ti ti-mail me-2 text-primary"></i> info@zajeciownia.pl</li>
                    <li><i class="ti ti-phone me-2 text-success"></i> +48 692 343 999 (Pn-Pt 9:00 - 17:00)</li>
                    <li><i class="ti ti-map-pin me-2 text-warning"></i> ul. Rynek 18, 27-640 Klimontów</li>
                </ul>
                <div class="d-flex gap-2">
                    <a href="#" class="btn btn-icon btn-dark text-white rounded-circle"><i
                            class="ti ti-brand-facebook fs-2"></i></a>
                    <a href="#" class="btn btn-icon btn-dark text-white rounded-circle"><i
                            class="ti ti-brand-linkedin fs-2"></i></a>
                    <a href="#" class="btn btn-icon btn-dark text-white rounded-circle"><i
                            class="ti ti-brand-youtube fs-2"></i></a>
                </div>
            </div>
        </div>

        @include('layouts.partials.copy')
    </div>
</footer>