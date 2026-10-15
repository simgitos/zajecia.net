<x-app-layout>
    {{-- Stylowanie kart katalogu --}}
    <style>
        .course-card {
            border: 1px solid rgba(var(--tblr-border-color-rgb), 0.6);
            border-radius: 14px;
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
            overflow: hidden;
        }
        .course-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 32px rgba(0,0,0,0.10);
            border-color: rgba(var(--tblr-primary-rgb), 0.35);
        }
        .course-card .card-header {
            background: linear-gradient(135deg, rgba(var(--tblr-primary-rgb), 0.08) 0%, rgba(var(--tblr-primary-rgb), 0.02) 100%);
            border-bottom: 1px solid rgba(var(--tblr-border-color-rgb), 0.5);
            padding: 1rem 1.1rem 0.85rem;
        }
        .course-card .card-body {
            padding: 1rem 1.1rem;
        }
        .course-card .card-footer {
            background: transparent;
            border-top: 1px solid rgba(var(--tblr-border-color-rgb), 0.4);
            padding: 0.85rem 1.1rem;
        }
        .badge-billing {
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            padding: 0.3em 0.65em;
            border-radius: 6px;
        }
        .badge-slots {
            font-size: 0.7rem;
            font-weight: 700;
            padding: 0.3em 0.65em;
            border-radius: 6px;
        }
        .course-meta-item {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            font-size: 0.82rem;
            color: var(--tblr-secondary);
            margin-bottom: 0.35rem;
        }
        .course-meta-item i {
            font-size: 1rem;
            color: var(--tblr-primary);
            flex-shrink: 0;
            width: 16px;
            text-align: center;
        }
        .course-meta-item strong {
            color: var(--tblr-body-color);
            font-weight: 600;
        }
        .schedule-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            background: rgba(var(--tblr-primary-rgb), 0.08);
            color: var(--tblr-primary);
            border-radius: 8px;
            padding: 0.3rem 0.65rem;
            font-size: 0.78rem;
            font-weight: 600;
            width: 100%;
        }
        .btn-enroll {
            background: linear-gradient(135deg, var(--tblr-primary), #5a67d8);
            border: none;
            color: #fff;
            font-weight: 600;
            border-radius: 10px;
            padding: 0.55rem 1rem;
            transition: opacity 0.15s;
        }
        .btn-enroll:hover { opacity: 0.88; color: #fff; }
        .btn-add-child {
            border: 2px dashed rgba(var(--tblr-primary-rgb), 0.5);
            color: var(--tblr-primary);
            border-radius: 10px;
            font-weight: 600;
            background: rgba(var(--tblr-primary-rgb), 0.04);
            transition: background 0.15s, border-color 0.15s;
        }
        .btn-add-child:hover {
            background: rgba(var(--tblr-primary-rgb), 0.1);
            border-color: var(--tblr-primary);
            color: var(--tblr-primary);
        }
        .slots-info {
            font-size: 0.78rem;
            font-weight: 600;
        }
        @media (max-width: 575px) {
            .course-card { border-radius: 12px; }
        }
    </style>

    <div class="page-header d-print-none mb-3">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title fw-bold">
                        <i class="ti ti-list-search me-2 text-primary"></i> Katalog zajęć
                        @if(isset($activeSeason) && $activeSeason)
                            <span class="badge bg-primary-lt fs-6 ms-2">
                                <i class="ti ti-calendar me-1"></i> {{ $activeSeason->name }}
                            </span>
                        @endif
                    </h2>
                    <div class="text-secondary mt-1 fs-5">
                        @if(isset($activeSeason) && $activeSeason)
                            Oferta zajęć na okres: <strong>{{ $activeSeason->dateRangeFormatted() }}</strong>
                        @else
                            Przeglądaj ofertę i zapisuj uczestników na zajęcia
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-3 rounded-3" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i class="ti ti-circle-check fs-3 text-success"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-3 rounded-3" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i class="ti ti-alert-circle fs-3 text-danger"></i>
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
                <div class="card text-center py-5 border-0 shadow-sm rounded-3">
                    <div class="card-body">
                        <div class="mb-3" style="font-size:3rem; opacity:0.3;">
                            <i class="ti ti-calendar-off"></i>
                        </div>
                        <h3 class="fw-bold mb-1">Brak dostępnych zajęć</h3>
                        <p class="text-secondary mb-0">
                            Obecnie w placówce nie ma aktywnych zajęć.<br>
                            Skontaktuj się z administratorem.
                        </p>
                    </div>
                </div>
            @else
                <div class="row g-3">
                    @foreach($courses as $course)
                        @php
                            $registeredCount = $course->children_count;
                            $maxParticipants = $course->max_participants;
                            $availableSlots = max(0, $maxParticipants - $registeredCount);
                            $isFull = $registeredCount >= $maxParticipants;

                            $billingLabel = match($course->billing_type) {
                                'monthly_flat'       => 'Ryczałt mies.',
                                'per_lesson_monthly' => 'Za lekcję',
                                'per_lesson_single'  => 'Jednorazowo',
                                default              => $course->billing_type,
                            };
                            $billingClass = match($course->billing_type) {
                                'monthly_flat'       => 'bg-azure-lt text-azure',
                                'per_lesson_monthly' => 'bg-indigo-lt text-indigo',
                                'per_lesson_single'  => 'bg-teal-lt text-teal',
                                default              => 'bg-secondary-lt text-secondary',
                            };
                            $billingIcon = match($course->billing_type) {
                                'monthly_flat'       => 'ti-calendar-repeat',
                                'per_lesson_monthly' => 'ti-coin',
                                'per_lesson_single'  => 'ti-receipt',
                                default              => 'ti-currency-zloty',
                            };
                        @endphp

                        <div class="col-12 col-md-6 col-xl-4">
                            <div class="card course-card h-100 d-flex flex-column shadow-sm border-0">

                                {{-- Nagłówek karty: tytuł + sloty --}}
                                <div class="card-header">
                                    <div class="d-flex align-items-start justify-content-between gap-2">
                                        <h3 class="fw-bold mb-0 lh-sm" style="font-size:1rem; color:var(--tblr-body-color);">
                                            {{ $course->title }}
                                        </h3>
                                        @if($isFull)
                                            <span class="badge badge-slots bg-danger text-white flex-shrink-0">Brak miejsc</span>
                                        @else
                                            <span class="badge badge-slots bg-success-lt text-success flex-shrink-0">
                                                {{ $availableSlots }} {{ $availableSlots === 1 ? 'miejsce' : 'miejsc' }}
                                            </span>
                                        @endif
                                    </div>
                                   
                                    
                                </div>

                                {{-- Treść karty --}}
                                <div class="card-body flex-grow-1">

                                    {{-- Tagi: typ + rozliczenie + cena --}}
                                    <div class="d-flex flex-wrap gap-1 mb-3">
                                        <span class="badge badge-billing bg-blue-lt text-blue">
                                            <i class="ti {{ $course->type === 'group' ? 'ti-users' : 'ti-user' }} me-1"></i>
                                            {{ $course->type === 'group' ? 'Grupowe' : 'Indywidualne' }}
                                        </span>
                                        <span class="badge badge-billing {{ $billingClass }}">
                                            <i class="ti {{ $billingIcon }} me-1"></i>
                                            {{ $billingLabel }}
                                        </span>
                                        <span class="badge badge-billing bg-green-lt text-green fw-bold">
                                            {{ number_format($course->price_per_unit, 2) }} zł
                                        </span>
                                    </div>
{{-- Harmonogram bezpośrednio pod tytułem --}}
                                    @if($course->schedule_info)
                                        <div class="course-meta-item">
                                            <i class="ti ti-clock"></i>
                                            {{ $course->schedule_info }}
                                        </div>
                                    @endif
                                    {{-- Meta: instruktor, sala, wiek --}}
                                    @if($course->instructor)
                                        <div class="course-meta-item">
                                            <i class="ti ti-user-circle"></i>
                                            <span>Instruktor: <strong>{{ $course->instructor->name }}</strong></span>
                                        </div>
                                    @endif

                                    @if($course->room)
                                        <div class="course-meta-item">
                                            <i class="ti ti-door"></i>
                                            <span>Sala: <strong>{{ $course->room->name }}</strong></span>
                                        </div>
                                    @endif

                                    @if($course->min_age || $course->max_age)
                                        <div class="course-meta-item">
                                            <i class="ti ti-mood-boy"></i>
                                            <span>Wiek:
                                                <strong>
                                                    @if($course->min_age && $course->max_age)
                                                        {{ $course->min_age }}–{{ $course->max_age }} lat
                                                    @elseif($course->min_age)
                                                        od {{ $course->min_age }} lat
                                                    @else
                                                        do {{ $course->max_age }} lat
                                                    @endif
                                                </strong>
                                            </span>
                                        </div>
                                    @endif

                                    {{-- Opis --}}
                                    @if($course->description)
                                        <p class="text-secondary mt-2 mb-0" style="font-size:0.82rem; line-height:1.5;">
                                            {{ Str::limit($course->description, 110) }}
                                        </p>
                                    @endif

                                </div>


                                {{-- Stopka karty z akcją --}}
                                <div class="card-footer">
                                    @if($isFull)
                                        <button class="btn w-100 btn-secondary" disabled style="border-radius:10px; font-weight:600;">
                                            <i class="ti ti-ban me-1"></i> Brak wolnych miejsc
                                        </button>
                                    @elseif($children->isEmpty())
                                        <a href="{{ route('user.children.index') }}" class="btn btn-add-child w-100">
                                            <i class="ti ti-mood-kid me-1"></i> Dodaj uczestnika, aby zapisać
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-enroll w-100"
                                                data-bs-toggle="modal" data-bs-target="#modal-enroll-course-{{ $course->id }}">
                                            <i class="ti ti-circle-plus me-1"></i> Zapisz uczestnika
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Modal zapisu uczestnika --}}
                        @if(!$children->isEmpty() && !$isFull)
                            <div class="modal modal-blur fade" id="modal-enroll-course-{{ $course->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                                    <div class="modal-content rounded-3">
                                        <form action="{{ route('user.children.enroll') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="course_id" value="{{ $course->id }}">
                                            <div class="modal-header border-0 pb-0">
                                                <h5 class="modal-title fw-bold fs-4">Zapis na zajęcia</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body pt-2">
                                                <p class="text-secondary mb-1" style="font-size:0.85rem;">
                                                    <strong>{{ $course->title }}</strong>
                                                </p>
                                                @if($course->schedule_info)
                                                    <p class="text-primary mb-3" style="font-size:0.82rem;">
                                                        <i class="ti ti-clock me-1"></i>{{ $course->schedule_info }}
                                                    </p>
                                                @endif
                                                <div class="mb-2">
                                                    <label class="form-label fw-semibold">Wybierz uczestnika</label>
                                                    <select name="child_id" class="form-select" required>
                                                        <option value="" disabled selected>-- Wybierz uczestnika --</option>
                                                        @foreach($children as $childOption)
                                                            @php
                                                                $alreadyEnrolled = $course->children->contains($childOption->id);
                                                            @endphp
                                                            <option value="{{ $childOption->id }}" @if($alreadyEnrolled) disabled @endif>
                                                                {{ $childOption->name }}{{ $childOption->age ? ' (' . $childOption->age . ' lat)' : '' }}
                                                                @if($alreadyEnrolled) — już zapisany @endif
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 pt-0">
                                                <button type="button" class="btn btn-link text-secondary me-auto" data-bs-dismiss="modal">Anuluj</button>
                                                <button type="submit" class="btn btn-enroll px-4">
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
