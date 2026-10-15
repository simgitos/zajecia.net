<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AcceptSchoolTermsController extends Controller
{
    /**
     * Zapisuje akceptację regulaminu dla aktualnie aktywnej placówki.
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

        $user->schools()->updateExistingPivot($activeSchool->id, [
            'terms_accepted_at' => now(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Regulamin placówki ' . $activeSchool->name . ' został zaakceptowany.');
    }
}
