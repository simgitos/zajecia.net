@extends('layouts.app')

@section('content')
<div class="page-header d-print-none mb-4">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="mb-1">
                    <a href="{{ route('admin.courses.index') }}" class="text-secondary">
                        <i class="ti ti-arrow-left me-1"></i> Powrót do listy zajęć
                    </a>
                </div>
                <h2 class="page-title text-primary fw-bold">
                    <i class="ti ti-books me-2"></i> {{ $course->title }}
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ route('admin.courses.edit', $course->id) }}" class="btn btn-primary">
                    <i class="ti ti-edit me-1"></i> Edytuj zajęcia
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards mb-4">
            <!-- Szczegóły zajęć -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-surface border-bottom">
                        <h3 class="card-title fw-bold"><i class="ti ti-info-circle me-2 text-primary"></i> Informacje o zajęciach</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <span class="text-secondary d-block fs-5">Obłożenie / Limit miejsc</span>
                            @php
                                $isFull = $course->children_count >= $course->max_participants;
                                $percentage = min(100, round(($course->children_count / max(1, $course->max_participants)) * 100));
                            @endphp
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold fs-2 text-dark">{{ $course->children_count }} / {{ $course->max_participants }}</span>
                                <span class="badge {{ $isFull ? 'bg-danger' : 'bg-success' }}">
                                    {{ $isFull ? 'Brak wolnych miejsc' : ($course->max_participants - $course->children_count) . ' wolnych' }}
                                </span>
                            </div>
                            <div class="progress progress-sm mb-2">
                                <div class="progress-bar {{ $isFull ? 'bg-danger' : 'bg-primary' }}" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>

                        <hr class="my-3">

                        <div class="space-y-3">
                            <div>
                                <span class="text-secondary d-block fs-5">Instruktor</span>
                                <strong><i class="ti ti-user me-1 text-primary"></i> {{ $course->instructor?->name ?? 'Brak przypisanego' }}</strong>
                            </div>

                            <div>
                                <span class="text-secondary d-block fs-5">Sala</span>
                                @if($course->room)
                                    <span class="badge bg-blue-lt fs-4"><i class="ti ti-door me-1"></i>{{ $course->room->name }}</span>
                                @else
                                    <span class="text-muted">Brak sal</span>
                                @endif
                            </div>

                            <div>
                                <span class="text-secondary d-block fs-5">Typ zajęć</span>
                                <span class="badge {{ $course->type === 'group' ? 'bg-purple-lt' : 'bg-azure-lt' }} fs-4">
                                    {{ $course->type === 'group' ? 'Grupowe' : 'Indywidualne' }}
                                </span>
                            </div>

                            <div>
                                <span class="text-secondary d-block fs-5">Cena i rozliczenie</span>
                                <strong>{{ number_format($course->price_per_unit, 2, ',', ' ') }} zł</strong>
                                <small class="text-secondary d-block">
                                    @switch($course->billing_type)
                                        @case('monthly_flat') Ryczałt miesięczny @break
                                        @case('per_lesson_monthly') Wg lekcji (miesięcznie) @break
                                        @case('per_lesson_single') Pojedyncza lekcja @break
                                    @endswitch
                                </small>
                            </div>

                            <div>
                                <span class="text-secondary d-block fs-5">Wymagania wiekowe</span>
                                <strong>
                                    @if($course->min_age || $course->max_age)
                                        {{ $course->min_age ?? 0 }} - {{ $course->max_age ?? '∞' }} lat
                                    @else
                                        Bez limitu wieku
                                    @endif
                                </strong>
                            </div>

                            <div>
                                <span class="text-secondary d-block fs-5">Status</span>
                                @if($course->is_active)
                                    <span class="badge bg-success-lt">Aktywne</span>
                                @else
                                    <span class="badge bg-secondary-lt">Nieaktywne</span>
                                @endif
                            </div>

                            @if($course->description)
                                <div>
                                    <span class="text-secondary d-block fs-5">Opis zajęć</span>
                                    <p class="text-dark mb-0 fs-4">{{ $course->description }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista zapisanych dzieci -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-surface d-flex justify-content-between align-items-center border-bottom">
                        <h3 class="card-title fw-bold">
                            <i class="ti ti-mood-kid me-2 text-primary"></i> Lista zapisanych dzieci ({{ $course->children->count() }})
                        </h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Imię i nazwisko dziecka</th>
                                    <th>Data ur. (Wiek)</th>
                                    <th>Rodzic (Kontakt)</th>
                                    <th>Uwagi rodzica</th>
                                    <th>Data zapisu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($course->children as $index => $child)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong class="text-dark">{{ $child->name }}</strong>
                                            @if($child->pesel)
                                                <small class="text-muted d-block">PESEL: {{ $child->pesel }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $child->birth_date ? $child->birth_date->format('d.m.Y') : '-' }}
                                            <small class="text-muted d-block">({{ $child->age }} lat)</small>
                                        </td>
                                        <td>
                                            @if($child->parent)
                                                <div class="fw-bold">{{ $child->parent->name }}</div>
                                                <small class="text-muted">{{ $child->parent->email }}</small>
                                            @else
                                                <span class="text-muted">Brak danych rodzica</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($child->parent_comment)
                                                <span class="badge bg-warning-lt text-wrap" style="max-width: 200px;">
                                                    {{ $child->parent_comment }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $child->pivot->created_at ? $child->pivot->created_at->format('d.m.Y H:i') : '-' }}
                                            </small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-5">
                                            <i class="ti ti-user-off fs-1 d-block mb-2 text-secondary"></i>
                                            Brak zapisanych dzieci na te zajęcia.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
