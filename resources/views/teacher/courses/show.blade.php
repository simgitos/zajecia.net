<x-app-layout>
    <div class="page-header d-print-none mb-4">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="mb-1">
                        <a href="{{ route('teacher.courses.index') }}" class="text-secondary">
                            <i class="ti ti-arrow-left me-1"></i> Powrót do moich zajęć
                        </a>
                    </div>
                    <h2 class="page-title text-primary fw-bold">
                        <i class="ti ti-school me-2"></i> Zajęcia: {{ $course->title }}
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <button type="button" onclick="window.print();" class="btn btn-outline-secondary">
                        <i class="ti ti-printer me-1"></i> Drukuj listę obecności
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards mb-4">
                <!-- Informacje o zajęciach dla nauczyciela -->
                <div class="col-md-4 d-print-none">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-surface border-bottom">
                            <h3 class="card-title fw-bold"><i class="ti ti-info-circle me-2 text-primary"></i> Informacje</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <span class="text-secondary d-block fs-5">Liczba zapisanych dzieci</span>
                                <strong class="fs-2 text-dark">{{ $course->children_count }} / {{ $course->max_participants }}</strong>
                            </div>

                            <div class="mb-3">
                                <span class="text-secondary d-block fs-5">Sala</span>
                                @if($course->room)
                                    <span class="badge bg-blue-lt fs-4"><i class="ti ti-door me-1"></i>{{ $course->room->name }}</span>
                                @else
                                    <span class="text-muted">Nie określono</span>
                                @endif
                            </div>

                            <div class="mb-3">
                                <span class="text-secondary d-block fs-5">Typ zajęć</span>
                                <span class="badge {{ $course->type === 'group' ? 'bg-purple-lt' : 'bg-azure-lt' }} fs-4">
                                    {{ $course->type === 'group' ? 'Grupowe' : 'Indywidualne' }}
                                </span>
                            </div>

                            @if($course->description)
                                <div>
                                    <span class="text-secondary d-block fs-5">Opis / Uwagi do zajęć</span>
                                    <p class="text-dark mb-0 fs-4">{{ $course->description }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Tabela zapisanych dzieci -->
                <div class="col-md-8 col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-surface border-bottom d-flex justify-content-between align-items-center">
                            <h3 class="card-title fw-bold">
                                <i class="ti ti-users me-2 text-primary"></i> Lista zapisanych dzieci ({{ $course->children->count() }})
                            </h3>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table table-hover">
                                <thead>
                                    <tr>
                                        <th>Lp.</th>
                                        <th>Imię i nazwisko dziecka</th>
                                        <th>Wiek / Data ur.</th>
                                        <th>Kontakt z rodzicem</th>
                                        <th>Uwagi / Alergie</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($course->children as $index => $child)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <strong class="text-dark fs-3">{{ $child->name }}</strong>
                                            </td>
                                            <td>
                                                {{ $child->birth_date ? $child->birth_date->format('d.m.Y') : '-' }}
                                                <small class="text-muted d-block">({{ $child->age }} lat)</small>
                                            </td>
                                            <td>
                                                @if($child->parent)
                                                    <div class="fw-bold">{{ $child->parent->name }}</div>
                                                    <small class="text-muted"><i class="ti ti-mail me-1"></i>{{ $child->parent->email }}</small>
                                                @else
                                                    <span class="text-muted">Brak</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($child->parent_comment)
                                                    <span class="badge bg-warning-lt text-wrap" style="max-width: 220px;">
                                                        <i class="ti ti-alert-triangle me-1"></i> {{ $child->parent_comment }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-5">
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
</x-app-layout>
