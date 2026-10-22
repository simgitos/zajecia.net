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
            'full_name' => $validated['full_name'] ?? null,
            'nip' => $validated['nip'] ?? null,
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'postal_code' => $validated['postal_code'] ?? null,
            'bank_account_number' => $validated['bank_account_number'] ?? null,
        ];

        $school->update($updateData);

        return redirect()->route('admin.settings.edit')
            ->with('success', 'Ustawienia placówki zostały pomyślnie zaktualizowane.');
    }
}
