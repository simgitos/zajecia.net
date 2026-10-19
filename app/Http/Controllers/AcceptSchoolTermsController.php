<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AcceptSchoolTermsController extends Controller
{
    /**
     * Zapisuje akceptację regulaminu i zgód dla aktualnie aktywnej placówki.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (!$user) {
            abort(401);
        }

        $activeSchool = $user->activeSchool();

        if (!$activeSchool) {
            return redirect()->route('dashboard');
        }

        $request->validate([
            'terms_accepted' => ['required', 'accepted'],
            'rodo_accepted' => ['required', 'accepted'],
            'image_consent' => ['nullable'],
            'marketing_consent' => ['nullable'],
        ], [
            'terms_accepted.accepted' => 'Musisz zaakceptować Regulamin oraz Politykę Prywatności.',
            'rodo_accepted.accepted' => 'Musisz potwierdzić zapoznanie się z Klauzulą Informacyjną RODO.',
        ]);

        $now = now();
        $ip = $request->ip();

        $user->schools()->updateExistingPivot($activeSchool->id, [
            'terms_accepted_at' => $now,
            'terms_accepted_ip' => $ip,
            'rodo_accepted_at' => $now,
            'rodo_accepted_ip' => $ip,
            'image_consent_accepted_at' => $request->boolean('image_consent') ? $now : null,
            'marketing_consent_accepted_at' => $request->boolean('marketing_consent') ? $now : null,
        ]);

        return redirect()->back()->with('success', 'Wymagane zgody i regulamin placówki ' . $activeSchool->name . ' zostały pomyślnie zaakceptowane.');
    }
}
