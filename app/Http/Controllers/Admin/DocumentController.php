<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\DocumentType;
use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Enum;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function index(Request $request): View
    {
        $school = $request->user()->activeSchool();
        abort_unless($school, 404, 'Brak aktywnej placówki.');

        $documents = $school->documents()
            ->latest()
            ->get();

        return view('admin.documents.index', compact('school', 'documents'));
    }

    public function create(Request $request): View
    {
        $school = $request->user()->activeSchool();
        abort_unless($school, 404, 'Brak aktywnej placówki.');

        $types = DocumentType::cases();

        return view('admin.documents.create', compact('school', 'types'));
    }

    public function store(Request $request): RedirectResponse
    {
        $school = $request->user()->activeSchool();
        abort_unless($school, 404, 'Brak aktywnej placówki.');

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', new Enum(DocumentType::class)],
            'file' => ['required', 'file', 'mimes:pdf,doc,docx,odt,rtf,jpg,jpeg,png', 'max:10240'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
            'is_required' => ['nullable', 'boolean'],
        ]);

        $file = $request->file('file');
        $folder = "documents/school_{$school->id}";
        $path = $file->store($folder, 'public');

        $school->documents()->create([
            'title' => $validated['title'],
            'type' => $validated['type'],
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'is_required' => $request->boolean('is_required', false),
        ]);

        return redirect()
            ->route('admin.documents.index')
            ->with('success', 'Dokument został pomyślnie dodany.');
    }

    public function edit(Request $request, Document $document): View
    {
        $school = $request->user()->activeSchool();
        abort_unless($school && $document->school_id === $school->id, 403);

        $types = DocumentType::cases();

        return view('admin.documents.edit', compact('school', 'document', 'types'));
    }

    public function update(Request $request, Document $document): RedirectResponse
    {
        $school = $request->user()->activeSchool();
        abort_unless($school && $document->school_id === $school->id, 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', new Enum(DocumentType::class)],
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx,odt,rtf,jpg,jpeg,png', 'max:10240'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
            'is_required' => ['nullable', 'boolean'],
        ]);

        $data = [
            'title' => $validated['title'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'is_required' => $request->boolean('is_required', false),
        ];

        if ($request->hasFile('file')) {
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            $file = $request->file('file');
            $folder = "documents/school_{$school->id}";
            $path = $file->store($folder, 'public');

            $data['file_path'] = $path;
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_size'] = $file->getSize();
            $data['mime_type'] = $file->getMimeType();
        }

        $document->update($data);

        return redirect()
            ->route('admin.documents.index')
            ->with('success', 'Dokument został zaktualizowany.');
    }

    public function destroy(Request $request, Document $document): RedirectResponse
    {
        $school = $request->user()->activeSchool();
        abort_unless($school && $document->school_id === $school->id, 403);

        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()
            ->route('admin.documents.index')
            ->with('success', 'Dokument został usunięty.');
    }

    public function download(Request $request, Document $document): StreamedResponse
    {
        $school = $request->user()->activeSchool();
        abort_unless($school && $document->school_id === $school->id, 403);

        abort_unless(Storage::disk('public')->exists($document->file_path), 404, 'Plik nie został znaleziony.');

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }
}
