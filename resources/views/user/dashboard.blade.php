<x-app-layout>
    <!-- Nagłówek Pulpitu Opiekuna -->
    <div class="page-header d-print-none mb-3 mb-md-4">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    
                    <h2 class="page-title text-primary fw-extrabold fs-1">
                      <i class="ti ti-home me-1 text-primary"></i> Witaj {{ Auth::user()->name }}!
                      @if(isset($activeSeason) && $activeSeason)
                          <span class="badge bg-primary-lt fs-6 ms-2 align-middle">
                              <i class="ti ti-calendar me-1"></i> {{ $activeSeason->name }}
                          </span>
                      @endif
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row col-md-9 row-cards g-3">
                <!-- 1. KAFELEK PŁATNOŚCI / ROZLICZEŃ (100% szerokości na mobile) -->
                <div class="col-12">
                    @if($totalUnpaidAmount > 0)
                        <!-- Wariant A: Gdy są nieopłacone zajęcia -->
                        <div class="card shadow-sm border-0 border-start border-danger border-4 bg-surface rounded-3 overflow-hidden">
                            <div class="card-body p-3 p-sm-4">
                                <div class="row align-items-center g-3">
                                    <div class="col">
                                        <div class="d-flex align-items-center mb-1">
                                            <span class="avatar avatar-sm bg-danger text-white rounded-circle me-2 shadow-xs">
                                                <i class="ti ti-wallet fs-2"></i>
                                            </span>
                                            <span class="subheader fw-bold text-uppercase text-danger tracking-wider">Płatności do uregulowania</span>
                                        </div>
                                        <div class="h1 mb-0 fw-extrabold text-danger fs-1 tracking-tight">
                                            {{ number_format($totalUnpaidAmount, 2, ',', ' ') }} <span class="fs-3">zł</span>
                                        </div>
                                        <div class="text-secondary small mt-1">
                                            <i class="ti ti-info-circle me-1"></i> Posiadasz nieopłacone pozycje za zajęcia.
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-auto mt-2 mt-sm-0">
                                        <a href="{{ route('user.payments.index') }}" class="btn btn-danger btn-lg w-100 fw-bold shadow-sm py-2 px-3">
                                            <i class="ti ti-credit-card me-2 fs-2"></i> Szczegóły płatności
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Wariant B: Wszystko opłacone - Podziękowanie i zielony status -->
                        <div class="card shadow-sm border-0 border-start border-success border-4 bg-surface rounded-3 overflow-hidden">
                            <div class="card-body p-3 p-sm-4">
                                <div class="row align-items-center g-3">
                                    <div class="col">
                                        <div class="d-flex align-items-center mb-1">
                                            <span class="avatar avatar-sm bg-green-subtle text-success rounded-circle me-2 shadow-xs">
                                                <i class="ti ti-circle-check-filled fs-2"></i>
                                            </span>
                                            <span class="subheader fw-bold text-uppercase text-success tracking-wider">Stan rozliczeń</span>
                                        </div>
                                        <div class="h2 mb-0 fw-bold text-dark fs-2">
                                            Wszystko opłacone! Dziękujemy ❤️
                                        </div>
                                        <div class="text-secondary small mt-1">
                                            Brak jakichkolwiek zaległości. Dziękujemy za terminowe wpłaty.
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-auto mt-2 mt-sm-0">
                                        <a href="{{ route('user.payments.index') }}" class="btn btn-outline-success w-100 fw-bold py-2 px-3">
                                            <i class="ti ti-receipt me-1.5 fs-3"></i> Szczegóły płatności
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- 2. KAFELEK UCZESTNICY (50% szerokości na mobile) -->
               <x-stats icon="ti ti-mood-kid" color="primary" title="Uczestnicy zajęć" value="{{ $children->count() }}" route="{{ route('user.children.index') }}" description="Zarządzaj uczestnikami" ></x-stats> 
                

                <!-- 3. KAFELEK OFERTA ZAJĘĆ (50% szerokości na mobile) -->
               <x-stats icon="ti ti-school" color="secondary" title="Oferta Zajęć" value="Katalog" route="{{ route('user.courses.index') }}" description="Zapisz się na zajęcia" ></x-stats> 
                
            </div>
        </div>
    </div>
</x-app-layout>