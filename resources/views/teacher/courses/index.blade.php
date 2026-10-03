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
                                <div class="card-header bg-surface border-bottom d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3 class="card-title fw-bold text-dark mb-0">{{ $course->title }}</h3>
                                    </div>
                                    <span class="badge {{ $course->type === 'group' ? 'bg-purple-lt' : 'bg-azure-lt' }}">
                                        {{ $course->type === 'group' ? 'Grupowe' : 'Indywidualne' }}
                                    </span>
                                </div>

                                <div class="card-body flex-grow-1">
                                    <div class="mb-3 space-y-2">
                                        @if($course->room)
                                            <div class="d-flex align-items-center text-secondary fs-4 mb-1">
                                                <i class="ti ti-door me-2 text-primary"></i>
                                                <span>Sala: <strong>{{ $course->room->name }}</strong></span>
                                            </div>
                                        @endif

                                        @if($course->min_age || $course->max_age)
                                            <div class="d-flex align-items-center text-secondary fs-4 mb-1">
                                                <i class="ti ti-category me-2 text-primary"></i>
                                                <span>Wiek: 
                                                    <strong>
                                                        @if($course->min_age && $course->max_age)
                                                            {{ $course->min_age }} - {{ $course->max_age }} lat
                                                        @elseif($course->min_age)
                                                            od {{ $course->min_age }} lat
                                                        @else
                                                            do {{ $course->max_age }} lat
                                                        @endif
                                                    </strong>
                                                </span>
                                            </div>
                                        @endif

                                        <div class="d-flex align-items-center text-secondary fs-4 mb-1">
                                            <i class="ti ti-cash me-2 text-primary"></i>
                                            <span>Stawka/Cena: <strong>{{ number_format($course->price_per_unit, 2, ',', ' ') }} zł</strong></span>
                                        </div>
                                    </div>

                                    <!-- Obłożenie -->
                                    <div class="mt-3">
                                        <div class="d-flex justify-content-between text-secondary fs-4 mb-1">
                                            <span>Zapisane dzieci:</span>
                                            <span class="fw-bold text-dark">{{ $registeredCount }} / {{ $maxParticipants }}</span>
                                        </div>
                                        <div class="progress progress-sm">
                                            <div class="progress-bar bg-primary" style="width: {{ $percentage }}%"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer bg-surface border-top d-flex flex-column gap-2">
                                    <a href="{{ route('teacher.lessons.create', $course->id) }}" class="btn btn-success w-100">
                                        <i class="ti ti-checkup-list me-1"></i> Przeprowadź lekcję
                                    </a>
                                    <a href="{{ route('teacher.courses.show', $course->id) }}" class="btn btn-outline-primary w-100">
                                        <i class="ti ti-eye me-1"></i> Szczegóły i dzieci
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
