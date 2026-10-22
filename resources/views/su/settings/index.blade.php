@extends('layouts.app')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle text-danger fw-bold">
                <i class="ti ti-shield-bolt me-1"></i> PANEL SUPERADMINA (SU)
            </div>
            <h2 class="page-title text-dark fw-bold fs-2">
                Ustawienia Systemu
            </h2>
            <div class="text-secondary small mt-1">
                Zarządzaj globalnymi danymi operatora płatności Tpay, prowizjami oraz parametrami aplikacji w czytelnych sekcjach.
            </div>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modal-add-setting">
                <i class="ti ti-plus me-1"></i> Dodaj własny parametr
            </button>
        </div>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm mb-3" role="alert">
        <i class="ti ti-circle-check me-2 fs-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-3" role="alert">
        <i class="ti ti-alert-triangle me-2 fs-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<form action="{{ route('su.settings.update-all') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row row-cards">
        <!-- Sekcja 1: Operator Płatności Tpay (Collect) -->
        <div class="col-12">
            <div class="card shadow-sm border-0 border-top border-4 border-danger">
                <div class="card-header bg-surface d-flex justify-content-between align-items-center py-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar bg-danger-lt text-danger rounded">
                            <i class="ti ti-credit-card fs-2"></i>
                        </span>
                        <div>
                            <h3 class="card-title fw-bold text-dark mb-0">Operator Płatności Online (Tpay)</h3>
                            <div class="text-secondary small">Globalna konfiguracja integratora Tpay dla wszystkich placówek w usłudze Collect.</div>
                        </div>
                    </div>
                    <div>
                        @if(!empty($tpaySettings['client_id']) && $tpaySettings['has_client_secret'])
                            <span class="badge bg-success-lt fs-5 px-3 py-1">
                                <i class="ti ti-check me-1"></i> Skonfigurowano
                            </span>
                        @else
                            <span class="badge bg-warning-lt fs-5 px-3 py-1">
                                <i class="ti ti-alert-circle me-1"></i> Wymaga konfiguracji
                            </span>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <div class="alert alert-info bg-info-subtle border-0 text-info-emphasis rounded-3 py-2 px-3 mb-4 d-flex align-items-center gap-2">
                        <i class="ti ti-info-circle fs-2 text-info flex-shrink-0"></i>
                        <div class="small">
                            <strong>Bezpieczeństwo danych:</strong> Klucze <em>Client Secret</em> oraz <em>Merchant Secret</em> są automatycznie szyfrowane w bazie danych. Transakcje dla placówek będą realizowane przez tego operatora z bezpośrednim przekazaniem środków na zweryfikowane rachunki bankowe (usługa <strong>Tpay Collect</strong>).
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required fw-bold" for="tpay_client_id">Tpay Client ID</label>
                            <input type="text" 
                                   name="tpay_client_id" 
                                   id="tpay_client_id" 
                                   class="form-control font-monospace" 
                                   value="{{ old('tpay_client_id', $tpaySettings['client_id']) }}" 
                                   placeholder="np. 12345678-abcd-1234-ef00-1234567890ab">
                            <small class="form-hint">Identyfikator API z Panelu Tpay (OpenAPI).</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label required fw-bold" for="tpay_client_secret">
                                Tpay Client Secret
                                @if($tpaySettings['has_client_secret'])
                                    <span class="badge bg-success-subtle text-success ms-1 small">Klucz zapisany w bazie</span>
                                @endif
                            </label>
                            <input type="password" 
                                   name="tpay_client_secret" 
                                   id="tpay_client_secret" 
                                   class="form-control font-monospace" 
                                   placeholder="{{ $tpaySettings['has_client_secret'] ? '••••••••••••••••••••••••••••••••' : 'Wklej Client Secret...' }}" 
                                   autocomplete="new-password">
                            <small class="form-hint">Pozostaw puste, aby nie modyfikować aktualnie zapisanego tajnego klucza.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="tpay_merchant_secret">
                                Tpay Merchant Secret / Klucz powiadomień (JWS)
                                @if($tpaySettings['has_merchant_secret'])
                                    <span class="badge bg-success-subtle text-success ms-1 small">Klucz zapisany w bazie</span>
                                @endif
                            </label>
                            <input type="password" 
                                   name="tpay_merchant_secret" 
                                   id="tpay_merchant_secret" 
                                   class="form-control font-monospace" 
                                   placeholder="{{ $tpaySettings['has_merchant_secret'] ? '••••••••••••••••••••••••••••••••' : 'Wklej Merchant Secret...' }}" 
                                   autocomplete="new-password">
                            <small class="form-hint">Klucz do weryfikacji powiadomień Webhook od Tpay. Pozostaw puste, aby nie zmieniać.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold d-block">Środowisko Tpay</label>
                            <div class="p-2 border rounded bg-light d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="fw-bold">Tryb produkcyjny Tpay</div>
                                    <small class="text-secondary">Włącz dla prawdziwych transakcji lub wyłącz dla testów (Sandbox).</small>
                                </div>
                                <label class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" name="tpay_production_mode" value="1" {{ old('tpay_production_mode', $tpaySettings['production_mode']) ? 'checked' : '' }}>
                                </label>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Adres Webhooka dla Tpay (Notification URL)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="ti ti-link"></i></span>
                                <input type="text" class="form-control bg-light font-monospace" value="{{ route('tpay.webhook') }}" readonly onclick="this.select();">
                            </div>
                            <small class="form-hint">Ten URL należy wprowadzić w Panelu Tpay w ustawieniach powiadomień Webhook.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sekcja 2: Prowizje i Rozliczenia -->
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-surface py-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar bg-primary-lt text-primary rounded">
                            <i class="ti ti-percentage fs-2"></i>
                        </span>
                        <div>
                            <h3 class="card-title fw-bold text-dark mb-0">Prowizje i Płatności</h3>
                            <div class="text-secondary small">Parametry doliczania prowizji od transakcji online.</div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold" for="online_payment_commission_percent">
                            Prowizja operatora od płatności online (%)
                        </label>
                        <div class="input-group">
                            <input type="number" 
                                   step="0.01" 
                                   min="0" 
                                   max="100" 
                                   name="online_payment_commission_percent" 
                                   id="online_payment_commission_percent" 
                                   class="form-control fw-bold" 
                                   value="{{ old('online_payment_commission_percent', $settings['online_payment_commission_percent']->value ?? '1.5') }}">
                            <span class="input-group-text fw-bold">%</span>
                        </div>
                        <small class="form-hint">Wartość procentowa doliczana do kwoty transakcji rodzica przy płatnościach online (np. 1.5%).</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold" for="free_trial_days">
                            Długość darmowego okresu próbnego (dni)
                        </label>
                        <div class="input-group">
                            <input type="number" 
                                   min="1" 
                                   max="365" 
                                   name="free_trial_days" 
                                   id="free_trial_days" 
                                   class="form-control fw-bold" 
                                   value="{{ old('free_trial_days', $settings['free_trial_days']->value ?? '14') }}">
                            <span class="input-group-text">dni</span>
                        </div>
                        <small class="form-hint">Okres testowy dla nowo zarejestrowanych placówek (domyślnie 14 dni).</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sekcja 3: Ustawienia Ogólne & Komunikaty -->
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-surface py-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar bg-blue-lt text-blue rounded">
                            <i class="ti ti-info-square-rounded fs-2"></i>
                        </span>
                        <div>
                            <h3 class="card-title fw-bold text-dark mb-0">Ustawienia Ogólne i Komunikaty</h3>
                            <div class="text-secondary small">Dane kontaktowe i ogłoszenia globalne.</div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold" for="support_contact_email">
                            Główny e-mail wsparcia i kontaktu
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ti ti-mail"></i></span>
                            <input type="email" 
                                   name="support_contact_email" 
                                   id="support_contact_email" 
                                   class="form-control" 
                                   value="{{ old('support_contact_email', $settings['support_contact_email']->value ?? 'kontakt@zajeciownia.pl') }}">
                        </div>
                        <small class="form-hint">Adres e-mail widoczny w stopce aplikacji i powiadomieniach.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold" for="system_notification_banner">
                            Globalny baner informacyjny
                        </label>
                        <textarea name="system_notification_banner" 
                                  id="system_notification_banner" 
                                  class="form-control" 
                                  rows="3" 
                                  placeholder="Wpisz opcjonalny komunikat (np. o przerwie technicznej)...">{{ old('system_notification_banner', $settings['system_notification_banner']->value ?? '') }}</textarea>
                        <small class="form-hint">Pozostaw puste, aby nie wyświetlać paska ogłoszeń użytkownikom.</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sekcja 4: Dodatkowe / Niestandardowe parametry (jeśli istnieją) -->
        @php
            $builtInKeys = ['tpay_client_id', 'tpay_client_secret', 'tpay_merchant_secret', 'tpay_production_mode', 'online_payment_commission_percent', 'free_trial_days', 'support_contact_email', 'system_notification_banner'];
            $customSettings = $settings->filter(fn($item) => !in_array($item->key, $builtInKeys, true));
        @endphp

        @if($customSettings->isNotEmpty())
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-surface py-3">
                        <h3 class="card-title fw-bold text-dark mb-0">
                            <i class="ti ti-adjustments me-2 text-secondary"></i> Dodatkowe parametry niestandardowe
                        </h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 30%;">Klucz / Etykieta</th>
                                    <th style="width: 35%;">Wartość</th>
                                    <th style="width: 25%;">Opis</th>
                                    <th style="width: 10%;" class="text-end">Usuń</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customSettings as $customItem)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $customItem->label }}</div>
                                            <code>{{ $customItem->key }}</code>
                                        </td>
                                        <td>
                                            @if($customItem->type === 'textarea')
                                                <textarea name="custom_settings[{{ $customItem->id }}]" class="form-control" rows="2">{{ old("custom_settings.{$customItem->id}", $customItem->value) }}</textarea>
                                            @else
                                                <input type="text" name="custom_settings[{{ $customItem->id }}]" class="form-control" value="{{ old("custom_settings.{$customItem->id}", $customItem->value) }}">
                                            @endif
                                        </td>
                                        <td class="text-secondary small">{{ $customItem->description ?: 'Brak' }}</td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="if(confirm('Usunąć parametr?')) document.getElementById('delete-custom-{{ $customItem->id }}').submit();">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <!-- Pasek zapisu zmian -->
        <div class="col-12">
            <div class="card bg-surface border shadow-sm p-3 d-flex flex-row justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2 text-secondary small">
                    <i class="ti ti-device-floppy fs-2 text-primary"></i>
                    <span>Zapisz wprowadzone modyfikacje w sekcjach operatora płatności i ustawień.</span>
                </div>
                <button type="submit" class="btn btn-primary btn-lg fw-bold px-4 shadow">
                    <i class="ti ti-check me-2 fs-2"></i> Zapisz wszystkie ustawienia
                </button>
            </div>
        </div>
    </div>
</form>

{{-- Formularze usuwania dodatkowych parametrów --}}
@foreach($customSettings as $customItem)
    <form id="delete-custom-{{ $customItem->id }}" action="{{ route('su.settings.destroy', $customItem) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endforeach

<!-- Modal: Dodaj nowy parametr -->
<div class="modal modal-blur fade" id="modal-add-setting" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{ route('su.settings.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="ti ti-plus me-1 text-primary"></i> Dodaj nowy parametr do bazy
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Klucz systemowy (unikalny string)</label>
                        <input type="text" name="key" class="form-control" placeholder="np. default_vat_rate" required pattern="[a-z0-9_]+">
                        <small class="text-muted">Tylko małe litery, cyfry i podkreślenia.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Czytelna nazwa parametru</label>
                        <input type="text" name="label" class="form-control" placeholder="np. Domyślna stawka VAT" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Typ wartości</label>
                        <select name="type" class="form-select" required>
                            <option value="text">Tekst (text)</option>
                            <option value="number">Liczba (number)</option>
                            <option value="boolean">Włącz/Wyłącz (boolean)</option>
                            <option value="textarea">Dłuższy tekst (textarea)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Wartość początkowa</label>
                        <input type="text" name="value" class="form-control" placeholder="Wartość...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Opis / przeznaczenie</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Do czego służy ten parametr..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                    <button type="submit" class="btn btn-primary fw-bold">
                        <i class="ti ti-plus me-1"></i> Dodaj parametr
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
