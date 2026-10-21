<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class DocumentDownloadController extends Controller
{
    /**
     * Ogólnodostępne pobieranie lub podgląd dokumentu placówki.
     */
    public function __invoke(Request $request, Document $document): Response
    {
        // Jeśli dokument jest nieaktywny, dostęp ma wyłącznie administrator danej placówki
        if (!$document->is_active) {
            $user = $request->user();
            $isAdmin = $user && ($user->isAdmin() || $user->hasRole('admin'));
            $isSameSchool = $user && $user->activeSchool()?->id === $document->school_id;

            abort_unless($isAdmin && $isSameSchool, 404, 'Dokument nie jest aktywny.');
        }

        abort_unless(
            $document->file_path && Storage::disk('public')->exists($document->file_path),
            404,
            'Plik nie został znaleziony na serwerze.'
        );

        $fileName = $document->file_name ?: basename($document->file_path);

        // Jeśli jawnie zażądano pobrania pliku (?download=1)
        if ($request->boolean('download')) {
            return Storage::disk('public')->download($document->file_path, $fileName);
        }

        // Domyślnie podgląd w przeglądarce (inline) - idealny dla plików PDF/obrazów otwieranych w nowej karcie
        return Storage::disk('public')->response($document->file_path, $fileName);
    }
}
