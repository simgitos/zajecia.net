<x-app-layout>
    <div class="page-header d-print-none mb-4">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title text-primary fw-bold">
                        <i class="ti ti-school me-2"></i> Moje Zajęcia (Prowadzone Kursy)
                    </h2>
                    <div class="text-secondary mt-1">
                        Lista zajęć, w których jesteś przypisanym nauczycielem/instruktorem.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm mb-3" role="alert">
                    <i class="ti ti-check me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($courses->isEmpty())
                <div class="card card-md shadow-sm text-center py-5">
                    <div class="card-body">
                        <div class="avatar avatar-xl bg-blue-subtle text-primary rounded-circle mb-3 fs-1">
                            <i class="ti ti-calendar-off"></i>
                        </div>
                        <h3 class="fw-bold">Brak przypisanych zajęć</h3>
                        <p class="text-secondary">
                            Nie jesteś obecnie przypisany jako instruktor do żadnych aktywnych zajęć.
                        </p>
                    </div>
                </div>
            @else
                <div class="row row-cards">
                    @foreach($courses as $course)
                        @php
                            $registeredCount = $course->children_count;
                            $maxParticipants = $course->max_participants;
                            $percentage = min(100, round(($registeredCount / max(1, $maxParticipants)) * 100));
                        @endphp
                        <div class="col-md-6 col-lg-4">
                            <div class="card shadow-sm h-100 border-0 d-flex flex-column">
                                <div
                                    class="card-header bg-surface border-bottom d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3 class="card-title fw-bold text-dark mb-0">{{ $course->title }}</h3>
                                    </div>
                                    <span class="badge {{ $course->type === 'group' ? 'bg-purple-lt' : 'bg-azure-lt' }}">
                                        {{ $course->type === 'group' ? 'Grupowe' : 'Indywidualne' }}
                                    </span>
                                </div>


                                <div class="card-body bg-surface  d-flex gap-2">
                                    @if($course->isIndividual())
                                        @if($course->children->isEmpty())
                                            <button class="btn btn-success w-100" disabled title="Brak przypisanych uczestników">
                                                <i class="ti ti-checkup-list me-1"></i> Przeprowadź lekcję
                                            </button>
                                        @elseif($course->children->count() === 1)
                                            <a href="{{ route('teacher.lessons.create', ['course' => $course->id, 'child_id' => $course->children->first()->id]) }}"
                                                class="btn btn-success w-100">
                                                <i class="ti ti-checkup-list me-1"></i> Przeprowadź lekcję
                                            </a>
                                        @else
                                            <button type="button" class="btn btn-success w-80" data-bs-toggle="modal"
                                                data-bs-target="#selectChildModal-{{ $course->id }}">
                                                <i class="ti ti-checkup-list me-1"></i> Przeprowadź lekcję
                                            </button>

                                            <!-- Modal wyboru uczestnika dla zajęć indywidualnych -->
                                            <div class="modal modal-blur fade" id="selectChildModal-{{ $course->id }}" tabindex="-1"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title fw-bold">Wybierz uczestnika</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p class="text-secondary small mb-3">Zajęcia indywidualne:
                                                                <strong>{{ $course->title }}</strong>. Wybierz osobę, dla której
                                                                przeprowadzasz lekcję:
                                                            </p>
                                                            <div class="list-group list-group-flush">
                                                                @foreach($course->children as $child)
                                                                    <a href="{{ route('teacher.lessons.create', ['course' => $course->id, 'child_id' => $child->id]) }}"
                                                                        class="list-group-item list-group-item-action d-flex align-items-center justify-content-between p-2">
                                                                        <span class="fw-semibold text-dark">{{ $child->name }}</span>
                                                                        <i class="ti ti-chevron-right text-muted"></i>
                                                                    </a>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @else
                                        @if($course->children->isEmpty())
                                            <button class="btn btn-success w-100" disabled title="Brak przypisanych uczestników">
                                                <i class="ti ti-checkup-list me-1"></i> Przeprowadź lekcję
                                            </button>
                                        @else
                                            <a href="{{ route('teacher.lessons.create', ['course' => $course->id]) }}" class="btn btn-success w-100"  title="Przeprowadź lekcję">
                                                <i class="ti ti-checkup-list me-1"></i> Przeprowadź lekcję
                                            </a>
                                        @endif
                                        
                                    @endif

                                    <a href="{{ route('teacher.courses.show', $course->id) }}"
                                        class="btn btn-outline-primary w-10">
                                        <i class="ti ti-eye me-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>