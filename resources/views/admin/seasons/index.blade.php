@extends('layouts.app')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-surface d-flex justify-content-between align-items-center">
        <h3 class="card-title fw-bold"><i class="ti ti-calendar-event me-2 text-primary"></i> Sezony edukacyjne / Semestry</h3>
        <a href="{{ route('admin.seasons.create') }}" class="btn btn-primary">
            <i class="ti ti-plus me-1"></i> Dodaj nowy sezon
        </a>
    </div>

   

    <div class="p-3 bg-light-lt border-bottom">
        <div class="d-flex align-items-center">
            <span class="avatar avatar-sm bg-primary-lt text-primary rounded me-3">
                <i class="ti ti-info-circle fs-2"></i>
            </span>
            <div class="small text-secondary">
                Wprowadzenie nowego sezonu rozpoczyna nowy okres rozliczeniowo-zajęciowy. Konta rodziców, instruktorów, historia płatności oraz profile dzieci pozostają nienaruszone. W nowym sezonie rodzice zapisują uczestników na nowo do oferty zajęć.
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-vcenter card-table table-hover">
            <thead>
                <tr>
                    <th>Nazwa sezonu</th>
                    <th>Okres obowiązywania</th>
                    <th>Liczba zajęć</th>
                    <th>Zapisani uczestnicy</th>
                    <th>Status</th>
                    <th class="w-1 text-end">Akcje</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($seasons as $season)
                    <tr class="{{ $season->is_active ? 'table-active' : '' }}">
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="avatar avatar-xs bg-{{ $season->statusBadgeColor() }}-lt rounded">
                                    <i class="ti ti-calendar"></i>
                                </span>
                                <div>
                                    <span class="fw-bold text-reset">{{ $season->name }}</span>
                                    @if($season->is_active)
                                        <span class="badge bg-success-lt ms-2">
                                            <i class="ti ti-check me-1"></i> Bieżący aktywny
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="text-secondary">
                                <i class="ti ti-calendar-time me-1"></i>{{ $season->dateRangeFormatted() }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-blue-lt">
                                <i class="ti ti-books me-1"></i>{{ $season->courses_count }} zajęć
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-purple-lt">
                                <i class="ti ti-users me-1"></i>{{ $season->enrolled_children_count }} dzieci
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $season->statusBadgeColor() }}-lt">
                                {{ $season->statusLabel() }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="btn-list flex-nowrap justify-content-end">
                                @if(!$season->is_active)
                                    <form action="{{ route('admin.seasons.activate', $season->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success btn-sm" title="Ustaw jako bieżący aktywny sezon placówki">
                                            <i class="ti ti-check me-1"></i> Aktywuj
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('admin.seasons.edit', $season->id) }}" class="btn btn-outline-primary btn-sm" title="Edytuj sezon">
                                    <i class="ti ti-edit me-1"></i> Edytuj
                                </a>
                                @if(!$season->is_active)
                                    <form action="{{ route('admin.seasons.destroy', $season->id) }}" method="POST" onsubmit="return confirm('Czy na pewno chcesz usunąć ten sezon? Związane z nim zajęcia zostaną odpięte.');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Usuń sezon">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="ti ti-calendar-off fs-1 d-block mb-2"></i>
                            Brak zdefiniowanych sezonów. Kliknij przycisk powyżej, aby utworzyć pierwszy sezon.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($seasons->hasPages())
        <div class="card-footer d-flex justify-content-center border-0 bg-transparent py-3">
            {{ $seasons->links() }}
        </div>
    @endif
</div>
@endsection
