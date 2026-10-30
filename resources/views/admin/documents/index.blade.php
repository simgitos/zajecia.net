@extends('layouts.app')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-surface d-flex justify-content-between align-items-center">
        <div>
            <h3 class="card-title fw-bold mb-0">
                <i class="ti ti-files me-2 text-primary"></i> Dokumenty placówki: {{ $school->name }}
            </h3>
            <div class="text-secondary small mt-1">
                Zarządzaj regulaminami, klauzulami RODO, standardami ochrony małoletnich oraz załącznikami placówki.
            </div>
        </div>
        <a href="{{ route('admin.documents.create') }}" class="btn btn-primary">
            <i class="ti ti-plus me-1"></i> Dodaj dokument
        </a>
    </div>

   

    <div class="p-3 bg-light-lt border-bottom">
        <div class="d-flex align-items-center">
            <span class="avatar avatar-sm bg-primary-lt text-primary rounded me-3">
                <i class="ti ti-folder fs-2"></i>
            </span>
            <div class="small text-secondary">
                Pliki są automatycznie segregowane w dedykowanym katalogu dla tej placówki. Dokumenty oznaczone jako aktywne są widoczne dla rodziców w zakładce „Dokumenty” oraz w procesie akceptacji zgód.
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-vcenter card-table table-hover">
            <thead>
                <tr>
                    <th>Typ dokumentu</th>
                    <th>Tytuł dokumentu</th>
                    <th>Plik</th>
                    <th>Wymagany</th>
                    <th>Status</th>
                    <th>Data aktualizacji</th>
                    <th class="w-1 text-end">Akcje</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($documents as $doc)
                    <tr>
                        <td>
                            <span class="badge bg-{{ $doc->type->badgeColor() }}-lt fw-semibold">
                                {{ $doc->type->label() }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $doc->title }}</div>
                            @if($doc->description)
                                <div class="text-secondary small">{{ Str::limit($doc->description, 80) }}</div>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-1">
                                <i class="ti ti-paperclip text-muted"></i>
                                <span class="small text-truncate" style="max-width: 200px;" title="{{ $doc->file_name }}">
                                    {{ $doc->file_name }}
                                </span>
                                <span class="badge bg-secondary-lt text-muted small ms-1">
                                    {{ $doc->formattedSize() }}
                                </span>
                            </div>
                        </td>
                        <td>
                            @if($doc->is_required)
                                <span class="badge bg-warning-lt text-warning fw-semibold">Wymagany</span>
                            @else
                                <span class="badge bg-secondary-lt text-secondary">Opcjonalny</span>
                            @endif
                        </td>
                        <td>
                            @if($doc->is_active)
                                <span class="badge bg-success-lt fw-semibold">Widoczny</span>
                            @else
                                <span class="badge bg-danger-lt fw-semibold">Ukryty</span>
                            @endif
                        </td>
                        <td class="text-secondary small">
                            {{ $doc->updated_at->format('d.m.Y H:i') }}
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ route('admin.documents.download', $doc) }}" class="btn btn-sm btn-outline-secondary" title="Pobierz plik">
                                    <i class="ti ti-download"></i>
                                </a>
                                <a href="{{ route('admin.documents.edit', $doc) }}" class="btn btn-sm btn-outline-primary" title="Edytuj">
                                    <i class="ti ti-edit"></i>
                                </a>
                                <form action="{{ route('admin.documents.destroy', $doc) }}" method="POST" onsubmit="return confirm('Czy na pewno chcesz usunąć ten dokument?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Usuń">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-secondary">
                            <div class="empty">
                                <div class="empty-icon text-muted">
                                    <i class="ti ti-file-off fs-1"></i>
                                </div>
                                <p class="empty-title">Brak dodanych dokumentów</p>
                                <p class="empty-subtitle text-secondary">
                                    Dodaj regulamin, politykę prywatności, RODO lub standardy ochrony małoletnich dla tej placówki.
                                </p>
                                <div class="empty-action">
                                    <a href="{{ route('admin.documents.create') }}" class="btn btn-primary">
                                        <i class="ti ti-plus me-1"></i> Dodaj pierwszy dokument
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
