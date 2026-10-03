<x-app-layout>
    <div class="page-header d-print-none mb-4">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title text-primary fw-bold">
                        <i class="ti ti-school me-2"></i> Katalog Zajęć w Szkole
                    </h2>
                    <div class="text-secondary mt-1">
                        Przeglądaj aktualną ofertę zajęć i zapisuj swoje dzieci.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm mb-3" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-check fs-2 me-2"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-3" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-alert-circle fs-2 me-2"></i>
                        <div>
                            <strong>Wystąpiły błędy:</strong>
                            <ul class="mb-0 mt-1 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($courses->isEmpty())
                <div class="card card-md shadow-sm text-center py-5">
                    <div class="card-body">
                        <div class="avatar avatar-xl bg-blue-subtle text-primary rounded-circle mb-3 fs-1">
                            <i class="ti ti-calendar-off"></i>
                        </div>
                        <h3 class="fw-bold">Brak dostępnych zajęć</h3>
                        <p class="text-secondary">
                            Obecnie w Twojej szkole nie ma aktywnych zajęć. Skontaktuj się z administratorem placówki.
                        </p>
                    </div>
                </div>
            @else
                <div class="row row-cards">
                    @foreach($courses as $course)
                        @php
                            $registeredCount = $course->children_count;
                            $maxParticipants = $course->max_participants;
                            $availableSlots = max(0, $maxParticipants - $registeredCount);
                            $percentage = min(100, round(($registeredCount / max(1, $maxParticipants)) * 100));
                            $isFull = $registeredCount >= $maxParticipants;
                        @endphp
                        <div class="col-md-6 col-lg-4">
                            <div class="card shadow-sm h-100 border-0 d-flex flex-column">
                                <div class="card-header bg-surface border-bottom d-flex justify-content-between align-items-start">
                                    <div>
                                        <h3 class="card-title fw-bold text-dark mb-1">{{ $course->title }}</h3>
                                        <span class="badge bg-blue-lt me-1">
                                            {{ $course->type === 'group' ? 'Grupowe' : 'Indywidualne' }}
                                        </span>
                                        <span class="badge bg-green-lt">
                                            {{ number_format($course->price_per_unit, 2) }} zł
                                        </span>
                                    </div>
                                    @if($isFull)
                                        <span class="badge bg-danger">Brak miejsc</span>
                                    @else
                                        <span class="badge bg-success">{{ $availableSlots }} wolnych</span>
                                    @endif
                                </div>

                                <div class="card-body flex-grow-1">
                                    @if($course->description)
                                        <p class="text-secondary mb-3 fs-4">
                                            {{ Str::limit($course->description, 120) }}
                                        </p>
                                    @endif

                                    <div class="mb-3 space-y-2">
                                        @if($course->instructor)
                                            <div class="d-flex align-items-center text-secondary fs-4 mb-1">
                                                <i class="ti ti-user me-2 text-primary"></i>
                                                <span>Instruktor: <strong>{{ $course->instructor->name }}</strong></span>
                                            </div>
                                        @endif

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
                                    </div>

                                    <!-- Wskaźnik wolnych miejsc -->
                                    <div class="mt-3">
                                        <div class="d-flex justify-content-between text-secondary fs-4 mb-1">
                                            <span>Uczestnicy:</span>
                                            <span class="fw-bold">{{ $registeredCount }} / {{ $maxParticipants }}</span>
                                        </div>
                                        <div class="progress progress-sm">
                                            <div class="progress-bar {{ $isFull ? 'bg-danger' : ($percentage > 75 ? 'bg-warning' : 'bg-primary') }}" 
                                                 style="width: {{ $percentage }}%" 
                                                 role="progressbar" 
                                                 aria-valuenow="{{ $percentage }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer bg-surface border-top">
                                    @if($isFull)
                                        <button class="btn btn-secondary w-100" disabled>
                                            <i class="ti ti-ban me-1"></i> Brak wolnych miejsc
                                        </button>
                                    @elseif($children->isEmpty())
                                        <a href="{{ route('user.children.index') }}" class="btn btn-outline-primary w-100">
                                            <i class="ti ti-mood-kid me-1"></i> Dodaj dziecko, aby zapisać
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#modal-enroll-course-{{ $course->id }}">
                                            <i class="ti ti-circle-plus me-1"></i> Zapisz dziecko na te zajęcia
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Modal wyboru dziecka do tego kursu -->
                        @if(!$children->isEmpty() && !$isFull)
                            <div class="modal modal-blur fade" id="modal-enroll-course-{{ $course->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <form action="{{ route('user.children.enroll') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="course_id" value="{{ $course->id }}">
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold">Zapis na: {{ $course->title }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="text-secondary mb-3">
                                                    Wybierz dziecko, które chcesz zapisać na te zajęcia:
                                                </p>
                                                <div class="mb-3">
                                                    <label class="form-label required">Wybierz dziecko</label>
                                                    <select name="child_id" class="form-select" required>
                                                        <option value="" disabled selected>-- Wybierz dziecko --</option>
                                                        @foreach($children as $childOption)
                                                            @php
                                                                $alreadyEnrolled = $course->children->contains($childOption->id);
                                                            @endphp
                                                            <option value="{{ $childOption->id }}" @if($alreadyEnrolled) disabled @endif>
                                                                {{ $childOption->name }} ({{ $childOption->age }} lat)
                                                                @if($alreadyEnrolled) - [JUŻ ZAPISANE] @endif
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Anuluj</button>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="ti ti-check me-1"></i> Potwierdź zapis
                                                </button>
                                            </div>
                                        </form>
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
