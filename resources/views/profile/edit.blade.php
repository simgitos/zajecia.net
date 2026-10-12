<x-app-layout>
    <div class="page-header d-print-none mb-4">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title text-primary fw-bold fs-2">
                        <i class="ti ti-user me-2"></i> Mój profil
                    </h2>
                    <div class="text-secondary mt-1 fs-4">
                        Zarządzaj swoimi danymi kontaktowymi, adresem oraz bezpieczeństwem konta.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @if (session('status') === 'profile-updated')
                <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-circle-check fs-2 me-2"></i>
                        <div class="fw-semibold">Dane profilu zostały pomyślnie zaktualizowane.</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('status') === 'password-updated')
                <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-circle-check fs-2 me-2"></i>
                        <div class="fw-semibold">Hasło zostało pomyślnie zmienione.</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row row-cards">
                {{-- Sekcja 1: Dane profilowe i adresowe --}}
                <div class="col-12 col-lg-8">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-surface border-bottom">
                            <h3 class="card-title fw-bold text-dark mb-0">
                                <i class="ti ti-id me-2 text-primary"></i> Informacje o profilu i adres
                            </h3>
                        </div>
                        <div class="card-body">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>
                </div>

                {{-- Kolumna boczna: Hasło i Usunięcie konta --}}
                <div class="col-12 col-lg-4">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-surface border-bottom">
                            <h3 class="card-title fw-bold text-dark mb-0">
                                <i class="ti ti-key me-2 text-warning"></i> Zmiana hasła
                            </h3>
                        </div>
                        <div class="card-body">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    <div class="card shadow-sm border-0 border-start border-danger border-4">
                        <div class="card-header bg-surface border-bottom">
                            <h3 class="card-title fw-bold text-danger mb-0">
                                <i class="ti ti-alert-triangle me-2"></i> Usunięcie konta
                            </h3>
                        </div>
                        <div class="card-body">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
