<?php

declare(strict_types=1);

namespace App\Http\Controllers\Su;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Wyświetla formularz ustawień globalnych podzielony na czytelne karty/sekcje.
     */
    public function index(): View
    {
        $settings = Setting::orderBy('id')->get()->keyBy('key');

        $tpaySettings = [
            'client_id' => Setting::get('tpay_client_id', ''),
            'has_client_secret' => !empty(Setting::getSecret('tpay_client_secret')),
            'has_merchant_secret' => !empty(Setting::getSecret('tpay_merchant_secret')),
            'production_mode' => (bool) Setting::get('tpay_production_mode', false),
        ];

        return view('su.settings.index', compact('settings', 'tpaySettings'));
    }

    /**
     * Zapis ustawień z kart panelu SU (alias updateAll dla trasy settings.update-all).
     */
    public function updateAll(Request $request): RedirectResponse
    {
        return $this->update($request);
    }

    /**
     * Zapis ustawień z kart panelu SU.
     */
    public function update(Request $request): RedirectResponse
    {
        // 1. Zapis konfiguracji Operatora płatności Tpay
        if ($request->has('tpay_client_id')) {
            Setting::set(
                'tpay_client_id',
                trim((string) $request->input('tpay_client_id')),
                'Tpay Client ID',
                'text',
                'Identyfikator klienta w Tpay OpenAPI'
            );
        }

        if ($request->filled('tpay_client_secret')) {
            Setting::set(
                'tpay_client_secret',
                trim((string) $request->input('tpay_client_secret')),
                'Tpay Client Secret',
                'password',
                'Tajny klucz API Tpay (zaszyfrowany)'
            );
        }

        if ($request->filled('tpay_merchant_secret')) {
            Setting::set(
                'tpay_merchant_secret',
                trim((string) $request->input('tpay_merchant_secret')),
                'Tpay Merchant Secret / Klucz powiadomień',
                'password',
                'Klucz do weryfikacji podpisów JWS z powiadomień Tpay (zaszyfrowany)'
            );
        }

        Setting::set(
            'tpay_production_mode',
            $request->boolean('tpay_production_mode') ? '1' : '0',
            'Tryb produkcyjny Tpay',
            'boolean',
            'Czy płatności działają na środowisku produkcyjnym Tpay (odznaczone = Sandbox)'
        );

        // 2. Zapis prowizji i rozliczeń
        if ($request->has('online_payment_commission_percent')) {
            Setting::set(
                'online_payment_commission_percent',
                $request->input('online_payment_commission_percent'),
                'Prowizja operatora płatności online (%)',
                'number',
                'Wysokość prowizji doliczanej przy płatnościach elektronicznych'
            );
        }

        // 3. Ustawienia ogólne
        if ($request->has('free_trial_days')) {
            Setting::set(
                'free_trial_days',
                $request->input('free_trial_days'),
                'Długość darmowego okresu próbnego (dni)',
                'number',
                'Liczba dni darmowego testu po rejestracji placówki'
            );
        }

        if ($request->has('support_contact_email')) {
            Setting::set(
                'support_contact_email',
                trim((string) $request->input('support_contact_email')),
                'Główny e-mail wsparcia i kontaktu',
                'text',
                'Adres e-mail widoczny dla placówek i rodziców'
            );
        }

        if ($request->has('system_notification_banner')) {
            Setting::set(
                'system_notification_banner',
                trim((string) $request->input('system_notification_banner')),
                'Komunikat globalny (pasek ogłoszeń)',
                'textarea',
                'Opcjonalny komunikat widoczny na górze systemu'
            );
        }

        // 4. Dowolne inne parametry przekazane w tablicy custom_settings
        $custom = $request->input('custom_settings', []);
        foreach ($custom as $id => $val) {
            $setting = Setting::find($id);
            if ($setting) {
                $setting->value = $val !== null ? (string) $val : null;
                $setting->save();
            }
        }

        return redirect()->route('su.settings.index')
            ->with('success', 'Ustawienia systemowe zostały pomyślnie zaktualizowane.');
    }

    /**
     * Dodanie nowego niestandardowego parametru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'key' => ['required', 'string', 'max:100', 'unique:settings,key'],
            'label' => ['required', 'string', 'max:255'],
            'value' => ['nullable', 'string'],
            'type' => ['required', 'string', 'in:text,number,boolean,textarea,password'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        Setting::set(
            $validated['key'],
            $validated['value'] ?? null,
            $validated['label'],
            $validated['type'],
            $validated['description'] ?? null
        );

        return redirect()->route('su.settings.index')
            ->with('success', "Parametr \"{$validated['label']}\" został dodany.");
    }

    /**
     * Usunięcie parametru.
     */
    public function destroy(Setting $setting): RedirectResponse
    {
        $label = $setting->label;
        $setting->delete();

        return redirect()->route('su.settings.index')
            ->with('success', "Parametr \"{$label}\" został usunięty.");
    }
}
