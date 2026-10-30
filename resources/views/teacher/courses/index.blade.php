<x-app-layout>
    <x-user-header 
        title='Moje Zajęcia (Prowadzone Kursy)'
        description="Lista zajęć, w których jesteś przypisanym nauczycielem/instruktorem." />

  
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
                <div class="accordion" id="accordion-teacher-courses">
                    @foreach($courses as $course)
                        @php
                            $registeredCount = $course->children_count;
                            $maxParticipants = $course->max_participants;
                            $percentage = min(100, round(($registeredCount / max(1, $maxParticipants)) * 100));
                        @endphp
                        <div class="accordion-item card shadow-sm mb-3 border">
                            <h2 class="accordion-header" id="heading-course-{{ $course->id }}">
                                <button class="accordion-button collapsed py-3 px-3 px-md-4" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapse-course-{{ $course->id }}"
                                    aria-expanded="false" aria-controls="collapse-course-{{ $course->id }}">
                                    <div class="d-flex align-items-center justify-content-between w-100 me-3 flex-wrap gap-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <span
                                                class="avatar avatar-sm {{ $course->type === 'group' ? 'bg-purple-lt text-purple' : 'bg-azure-lt text-azure' }} rounded-circle">
                                                <i
                                                    class="{{ $course->type === 'group' ? 'ti ti-users' : 'ti ti-user' }} fs-3"></i>
                                            </span>
                                            <span class="fw-bold text-dark fs-3 mb-0">{{ $course->title }}</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span
                                                class="badge {{ $course->type === 'group' ? 'bg-purple-lt text-purple' : 'bg-azure-lt text-azure' }} fs-5">
                                                {{ $course->type === 'group' ? 'Grupowe' : 'Indywidualne' }}
                                            </span>

                                        </div>
                                    </div>
                                </button>
                            </h2>

                            <div id="collapse-course-{{ $course->id }}" class="accordion-collapse collapse"
                                aria-labelledby="heading-course-{{ $course->id }}" data-bs-parent="#accordion-teacher-courses">
                                <div class="accordion-body border-top bg-surface p-3 p-md-4">
                                    <!-- Informacje i szczegóły zajęć -->
                                    <div class="row g-1 mb-1">

                                        <div class="col-sm-6 col-md-3">
                                            <div class="text-secondary small mb-1">
                                                <i class="ti ti-door me-1"></i>
                                                {{ $course->room?->name ?? 'Brak przypisanej sali' }}
                                            </div>
                                        </div>

                                        <div class="col-sm-6 col-md-3">
                                            <div class="text-secondary small mb-1">
                                                <i class="ti ti-clock me-1"></i>
                                                {{ $course->schedule_info ?: 'Brak zdefiniowanego harmonogramu' }}
                                            </div>
                                        </div>


                                    </div>

                                    <!-- Lista zapisanych uczestników -->
                                    @if($course->children->isNotEmpty())
                                        <div class="mb-3 pt-3 border-top">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <span class="subheader fw-bold text-uppercase text-secondary">
                                                    <i class="ti ti-list me-1"></i> Uczestnicy ( {{ $registeredCount }} /
                                                    {{ $maxParticipants }})
                                                </span>
                                                
                                            </div>


                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach($course->children as $child)
                                                    <span
                                                        class="badge bg-white text-dark border p-2 shadow-xs d-inline-flex align-items-center gap-1">
                                                        <i class="ti ti-user text-primary"></i>
                                                        <strong>{{ $child->name }}</strong>
                                                        @if($child->age)
                                                            <span class="text-muted small">({{ $child->age }} lat)</span>
                                                        @endif
                                                        @if($child->pivot?->sort_order)
                                                            <span class="badge bg-secondary-lt ms-1">#{{ $child->pivot->sort_order }}</span>
                                                        @endif
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Przyciski akcji -->
                                    <div
                                        class="d-flex flex-wrap gap-2 pt-3 border-top justify-content-between align-items-center">
                                        <div class="d-flex flex-wrap gap-2">
                                            @if($course->isIndividual())
                                                @if($course->children->isEmpty())
                                                    <button class="btn btn-success" disabled title="Brak przypisanych uczestników">
                                                        <i class="ti ti-checkup-list me-1"></i> Przeprowadź lekcję
                                                    </button>
                                                @elseif($course->children->count() === 1)
                                                    <a href="{{ route('teacher.lessons.create', ['course' => $course->id, 'child_id' => $course->children->first()->id]) }}"
                                                        class="btn btn-success">
                                                        <i class="ti ti-checkup-list me-1"></i> Przeprowadź lekcję
                                                    </a>
                                                @else
                                                    <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                                        data-bs-target="#selectChildModal-{{ $course->id }}">
                                                        <i class="ti ti-checkup-list me-1"></i> Przeprowadź lekcję
                                                    </button>
                                                @endif
                                            @else
                                                @if($course->children->isEmpty())
                                                    <button class="btn btn-success" disabled title="Brak przypisanych uczestników">
                                                        <i class="ti ti-checkup-list me-1"></i> Przeprowadź lekcję
                                                    </button>
                                                @else
                                                    <a href="{{ route('teacher.lessons.create', ['course' => $course->id]) }}"
                                                        class="btn btn-success">
                                                        <i class="ti ti-checkup-list me-1"></i> Przeprowadź lekcję
                                                    </a>
                                                @endif
                                            @endif

                                            <a href="{{ route('teacher.courses.show', $course->id) }}"
                                                class="btn btn-outline-primary">
                                                <i class="ti ti-eye me-1"></i> Szczegóły i historia zajęć
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($course->isIndividual() && $course->children->count() > 1)
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
                                                <strong>{{ $course->title }}</strong>. Wybierz osobę, dla której przeprowadzasz
                                                lekcję:
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
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>