<x-app-layout>
    <div class="page-header d-print-none mb-4">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title text-primary fw-bold">
                        <i class="ti ti-mood-kid me-2"></i> Moje Dzieci
                    </h2>
                    <div class="text-secondary mt-1">
                        Zarządzaj profilami swoich dzieci i zapisuj je na wybrane zajęcia w szkole.
                    </div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <button type="button" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#modal-add-child">
                        <i class="ti ti-plus me-1"></i> Dodaj nowe dziecko
                    </button>
                    <button type="button" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#modal-add-child" aria-label="Dodaj dziecko">
                        <i class="ti ti-plus"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-check fs-2 me-2"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
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

            @if($children->isEmpty())
                <div class="card card-md shadow-sm text-center py-5">
                    <div class="card-body">
                        <div class="avatar avatar-xl bg-primary-subtle text-primary rounded-circle mb-3 fs-1">
                            <i class="ti ti-mood-kid"></i>
                        </div>
                        <h3 class="fw-bold">Nie dodałeś jeszcze żadnego dziecka</h3>
                        <p class="text-secondary max-w-md mx-auto">
                            Aby zapisać dziecko na zajęcia w szkole, kliknij poniższy przycisk i wypełnij formularz danych dziecka.
                        </p>
                        <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#modal-add-child">
                            <i class="ti ti-plus me-1"></i> Dodaj pierwsze dziecko
                        </button>
                    </div>
                </div>
            @else
                <div class="row row-cards">
                    @foreach($children as $child)
                        <div class="col-md-6 col-lg-6">
                            <div class="card shadow-sm h-100 border-0">
                                <div class="card-header bg-surface d-flex justify-content-between align-items-center border-bottom">
                                    <div class="d-flex align-items-center">
                                        <span class="avatar bg-blue-lt me-3 fw-bold fs-3">
                                            {{ mb_substr($child->name, 0, 1) }}
                                        </span>
                                        <div>
                                            <h3 class="card-title fw-bold mb-0">{{ $child->name }}</h3>
                                            <small class="text-secondary">
                                                <i class="ti ti-calendar me-1"></i>
                                                {{ $child->birth_date->format('d.m.Y') }} ({{ $child->age }} lat)
                                            </small>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-ghost-secondary btn-icon" type="button" data-bs-toggle="dropdown">
                                            <i class="ti ti-dots-vertical fs-3"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="{{ route('user.children.edit', $child) }}">
                                                <i class="ti ti-edit me-2"></i> Edytuj dane
                                            </a>
                                            <form action="{{ route('user.children.destroy', $child) }}" method="POST" onsubmit="return confirm('Czy na pewno chcesz usunąć profil tego dziecka?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="ti ti-trash me-2"></i> Usuń profil
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    @if($child->parent_comment)
                                        <div class="mb-3 p-2 bg-light rounded text-secondary fs-4">
                                            <i class="ti ti-notes me-1"></i> <strong>Uwagi:</strong> {{ $child->parent_comment }}
                                        </div>
                                    @endif

                                    <h4 class="fw-bold mb-2 text-uppercase text-secondary fs-5 tracking-wider">
                                        Zapisane zajęcia ({{ $child->courses->count() }})
                                    </h4>

                                    @if($child->courses->isEmpty())
                                        <div class="p-3 text-center bg-light rounded text-secondary border border-dashed">
                                            <i class="ti ti-calendar-off fs-2 d-block mb-1"></i>
                                            Dziecko nie jest jeszcze zapisane na żadne zajęcia.
                                        </div>
                                    @else
                                        <div class="list-group list-group-flush mb-3">
                                            @foreach($child->courses as $course)
                                                <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <div class="fw-bold text-dark">{{ $course->title }}</div>
                                                        <small class="text-secondary d-block">
                                                            @if($course->instructor)
                                                                <i class="ti ti-user me-1"></i>{{ $course->instructor->name }} |
                                                            @endif
                                                            @if($course->room)
                                                                <i class="ti ti-door me-1"></i>{{ $course->room->name }} |
                                                            @endif
                                                            <span class="badge bg-blue-lt">{{ number_format($course->price_per_unit, 2) }} zł</span>
                                                        </small>
                                                    </div>
                                                    <form action="{{ route('user.children.unenroll', ['child' => $child->id, 'course' => $course->id]) }}" method="POST" onsubmit="return confirm('Czy na pewno chcesz wypisać dziecko z tych zajęć?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Wypisz z zajęć">
                                                            <i class="ti ti-user-x me-1"></i> Wypisz
                                                        </button>
                                                    </form>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <div class="card-footer bg-surface border-top text-end">
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modal-enroll-{{ $child->id }}">
                                        <i class="ti ti-circle-plus me-1"></i> Zapisz na nowe zajęcia
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Modal zapisywania dziecka na zajęcia -->
                        <div class="modal modal-blur fade" id="modal-enroll-{{ $child->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <form action="{{ route('user.children.enroll') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="child_id" value="{{ $child->id }}">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Zapisz dziecko: {{ $child->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p class="text-secondary mb-3">
                                                Wybierz zajęcia z oferty Twojej szkoły, na które chcesz zapisać dziecko.
                                            </p>
                                            <div class="mb-3">
                                                <label class="form-label required">Dostępne zajęcia</label>
                                                <select name="course_id" class="form-select" required>
                                                    <option value="" disabled selected>-- Wybierz zajęcia --</option>
                                                    @foreach($courses as $courseOption)
                                                        @php
                                                            $alreadyEnrolled = $child->courses->contains($courseOption->id);
                                                            $isFull = $courseOption->children_count >= $courseOption->max_participants;
                                                            $slotsLeft = max(0, $courseOption->max_participants - $courseOption->children_count);
                                                        @endphp
                                                        <option value="{{ $courseOption->id }}" 
                                                            @if($alreadyEnrolled || $isFull) disabled @endif>
                                                            {{ $courseOption->title }} 
                                                            ({{ number_format($courseOption->price_per_unit, 2) }} zł) 
                                                            @if($alreadyEnrolled)
                                                                - [JUŻ ZAPISANE]
                                                            @elseif($isFull)
                                                                - [BRAK MIEJSC]
                                                            @else
                                                                - [Wolne miejsca: {{ $slotsLeft }}/{{ $courseOption->max_participants }}]
                                                            @endif
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
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Modal dodawania nowego dziecka -->
    <div class="modal modal-blur fade" id="modal-add-child" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('user.children.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">
                            <i class="ti ti-mood-kid me-1 text-primary"></i> Dodaj dane dziecka
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label required">Imię i nazwisko dziecka</label>
                            <input type="text" name="name" class="form-control" placeholder="np. Jan Kowalski" value="{{ old('name') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Data urodzenia</label>
                            <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Numer PESEL <span class="text-secondary">(Opcjonalnie, szyfrowany)</span></label>
                            <input type="text" name="pesel" class="form-control" placeholder="11 cyfr" maxlength="11" value="{{ old('pesel') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Uwagi / Komentarz dla organizatora</label>
                            <textarea name="parent_comment" class="form-control" rows="3" placeholder="np. alerte na orzechy, informacje o zdrowiu">{{ old('parent_comment') }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Anuluj</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-plus me-1"></i> Zapisz profil dziecka
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
