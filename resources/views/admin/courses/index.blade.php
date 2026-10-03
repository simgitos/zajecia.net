@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title"><i class="ti ti-books me-2"></i> Zarządzanie zajęciami (Kursy)</h3>
        <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">
            <i class="ti ti-plus me-1"></i> Dodaj nowe zajęcia
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show m-3 mb-0" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>Nazwa zajęć</th>
                    <th>Instruktor</th>
                    <th>Sala</th>
                    <th>Typ</th>
                    <th>Rozliczenie</th>
                    <th>Cena</th>
                    <th>Maks. os.</th>
                    <th>Wiek</th>
                    <th>Status</th>
                    <th class="w-1">Akcje</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($courses as $course)
                    <tr>
                        <td>
                            <strong class="d-block">{{ $course->title }}</strong>
                            @if($course->description)
                                <small class="text-muted text-truncate d-inline-block" style="max-width: 200px;">{{ $course->description }}</small>
                            @endif
                        </td>
                        <td>{{ $course->instructor?->name ?? 'Brak' }}</td>
                        <td>
                            @if($course->room)
                                <span class="badge bg-blue-lt"><i class="ti ti-door me-1"></i>{{ $course->room->name }}</span>
                            @else
                                <span class="text-muted">Brak sal</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $course->type === 'group' ? 'bg-purple-lt' : 'bg-azure-lt' }}">
                                {{ $course->type === 'group' ? 'Grupowe' : 'Indywidualne' }}
                            </span>
                        </td>
                        <td>
                            @switch($course->billing_type)
                                @case('monthly_flat')
                                    Ryczałt miesięczny
                                    @break
                                @case('per_lesson_monthly')
                                    Wg lekcji (miesięcznie)
                                    @break
                                @case('per_lesson_single')
                                    Pojedyncza lekcja
                                    @break
                            @endswitch
                        </td>
                        <td><strong>{{ number_format($course->price_per_unit, 2, ',', ' ') }} zł</strong></td>
                        <td>{{ $course->max_participants }}</td>
                        <td>
                            @if($course->min_age || $course->max_age)
                                {{ $course->min_age ?? 0 }} - {{ $course->max_age ?? '∞' }} lat
                            @else
                                <span class="text-muted">Bez limitu</span>
                            @endif
                        </td>
                        <td>
                            @if($course->is_active)
                                <span class="badge bg-success-lt">Aktywne</span>
                            @else
                                <span class="badge bg-secondary-lt">Nieaktywne</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-list flex-nowrap">
                                <a href="{{ route('admin.courses.edit', $course->id) }}" class="btn btn-primary btn-sm">
                                    <i class="ti ti-edit me-1"></i> Edytuj
                                </a>
                                <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST" onsubmit="return confirm('Czy na pewno chcesz usunąć te zajęcia?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="ti ti-trash me-1"></i> Usuń
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">Brak dodanych zajęć</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($courses->hasPages())
        <div class="card-footer d-flex align-items-center">
            {{ $courses->links() }}
        </div>
    @endif
</div>
@endsection
