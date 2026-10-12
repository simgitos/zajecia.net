<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSchoolSettingsRequest;
use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Wyświetla formularz edycji ustawień szkoły.
     */
    public function edit(): View
    {
        $school = Auth::user()->school;

        if (!$school) {
            abort(404, 'Nie znaleziono placówki powiązanej z kontem administratora.');
        }

        return view('admin.settings.edit', compact('school'));
    }

    /**
     * Zapisuje zaktualizowane ustawienia szkoły.
     */
    public function update(UpdateSchoolSettingsRequest $request): RedirectResponse
    {
        /** @var School $school */
        $school = Auth::user()->school;

        if (!$school) {
            abort(404, 'Nie znaleziono placówki.');
        }

        $validated = $request->validated();

        $updateData = [
            'name' => $validated['name'],
            'nip' => $validated['nip'] ?? null,
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'bank_account_number' => $validated['bank_account_number'] ?? null,
            'tpay_client_id' => $validated['tpay_client_id'] ?? null,
            'tpay_production_mode' => (bool) ($request->boolean('tpay_production_mode')),
        ];

        // Aktualizujemy secret tylko jeśli podano nową wartość (aby nie nadpisać istniejącego pustym polem)
        if ($request->filled('tpay_client_secret')) {
            $updateData['tpay_client_secret'] = trim((string) $validated['tpay_client_secret']);
        }

        if ($request->filled('tpay_merchant_secret')) {
            $updateData['tpay_merchant_secret'] = trim((string) $validated['tpay_merchant_secret']);
        }

        $school->update($updateData);

        return redirect()->route('admin.settings.edit')
            ->with('success', 'Ustawienia placówki zostały pomyślnie zaktualizowane.');
    }
}
