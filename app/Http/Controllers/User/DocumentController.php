<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $school = $user->activeSchool();
        abort_unless($school, 404, 'Brak aktywnej placówki.');

        $documents = $school->documents()
            ->active()
            ->get();

        $membership = $user->activeSchoolMembership();

        return view('user.documents.index', compact('school', 'documents', 'membership'));
    }

    public function download(Request $request, Document $document): StreamedResponse
    {
        $user = $request->user();
        $school = $user->activeSchool();
        abort_unless($school && $document->school_id === $school->id && $document->is_active, 403);

        abort_unless(Storage::disk('public')->exists($document->file_path), 404, 'Plik nie został znaleziony.');

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    public function updateConsents(Request $request): RedirectResponse
    {
        $user = $request->user();
        $school = $user->activeSchool();
        abort_unless($school, 404, 'Brak aktywnej placówki.');

        $now = now();

        $user->schools()->updateExistingPivot($school->id, [
            'image_consent_accepted_at' => $request->boolean('image_consent') ? $now : null,
            'marketing_consent_accepted_at' => $request->boolean('marketing_consent') ? $now : null,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Twoje zgody opcjonalne zostały pomyślnie zaktualizowane.');
    }
}
