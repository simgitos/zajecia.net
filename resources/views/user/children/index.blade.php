<x-app-layout>
    
<x-user-header title="Uczestnicy Zajęć" description="Zarządzaj danymi swoich uczestników i zapisuj ich na dostępne zajęcia w bieżącym sezonie." icon="ti ti-mood-kid">
    <x-slot:actions>
        <button type="button" class="btn btn-sm btn-outline-secondary fw-bold shadow-sm py-2 px-3" data-bs-toggle="modal" data-bs-target="#modal-add-child">
            <i class="ti ti-user-plus me-1.5 fs-2"></i> Dodaj uczestnika
        </button>
    </x-slot:actions>
</x-user-header>


    <div class="page-body">
        <div class="container-xl">
            @if($children->isEmpty())
                <div class="card card-md shadow-sm text-center py-5 border-0 rounded-3">
                    <div class="card-body">
                        <div class="avatar avatar-xl bg-primary-subtle text-primary rounded-circle mb-3 fs-1 shadow-xs">
                            <i class="ti ti-mood-kid"></i>
                        </div>
                        <h3 class="fw-extrabold text-dark fs-2">Nie dodano jeszcze żadanego uczestnika</h3>
                        <p class="text-secondary max-w-md mx-auto fs-4">
                            Aby móc zapisywać uczestnika na wybrane zajęcia w placówce, kliknij poniższy przycisk i wypełnij prosty formularz.
                        </p>
                        <button type="button" class="btn btn-primary btn-lg mt-2 fw-bold shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#modal-add-child">
                            <i class="ti ti-user-plus me-1.5 fs-2"></i> Dodaj pierwszego uczestnika
                        </button>
                    </div>
                </div>
            @else
                <div class="row row-cards g-4">
                    @foreach($children as $child)
                        <div class="col-12 col-md-4 col-lg-4">
                            <div class="card shadow h-100 border-0 rounded-3 bg-surface overflow-hidden">
                                <div class="card-header bg-surface d-flex justify-content-between align-items-center border-bottom p-3">
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-md bg-blue-lt me-3 fw-bold fs-2 rounded-circle shadow-xs">
                                            {{ mb_substr($child->name, 0, 1) }}
                                        </span>
                                        <div>
                                            <h3 class="card-title fw-extrabold text-dark mb-0 fs-3">{{ $child->name }}</h3>
                                            <small class="text-secondary">
                                                <i class="ti ti-cake me-1"></i>
                                                {{ $child->birth_date ? $child->birth_date->format('d.m.Y') : '-' }} ({{ $child->age }} lat)
                                            </small>
                                        </div>
                                    </div>
                                    <div>
                                        <a href="{{ route('user.children.edit', $child) }}" class="btn btn-outline-secondary btn-sm fw-bold px-2 py-1" title="Edytuj dane uczestnika">
                                            <i class="ti ti-edit me-1"></i> Edytuj
                                        </a>
                                    </div>
                                </div>

                                <div class="card-body p-3">
                                    @if($child->parent_comment)
                                        <div class="mb-3 p-2.5 bg-light rounded text-secondary fs-5 border">
                                            <i class="ti ti-notes me-1 text-primary"></i> <strong>Uwagi:</strong> {{ $child->parent_comment }}
                                        </div>
                                    @endif

                                    <h4 class="fw-bold mb-2 text-uppercase text-secondary fs-6 tracking-wider">
                                        Zapisane zajęcia ({{ $child->courses->count() }})
                                    </h4>

                                    @if($child->courses->isEmpty())
                                        <div class="p-3 text-center bg-light rounded text-secondary border border-dashed mb-2">
                                            <i class="ti ti-calendar-off fs-2 d-block mb-1 text-muted"></i>
                                            Uczestnik nie jest jeszcze zapisany na żadne zajęcia.
                                        </div>
                                    @else
                                        <div class="list-group list-group-flush mb-2 border rounded overflow-hidden">
                                            @foreach($child->courses as $course)
                                                <div class="list-group-item p-2.5 d-flex justify-content-between align-items-center bg-surface">
                                                    <div>
                                                        <div class="fw-bold text-dark fs-4 mb-0.5">{{ $course->title }}</div>
                                                        <small class="text-secondary d-block">
                                                            @if($course->instructor)
                                                                <i class="ti ti-user me-1"></i>{{ $course->instructor->name }} • 
                                                            @endif
                                                            @if($course->room)
                                                                <i class="ti ti-door me-1"></i>{{ $course->room->name }} • 
                                                            @endif
                                                            <span class="badge bg-blue-lt fw-bold">{{ number_format($course->price_per_unit, 2, ',', ' ') }} zł</span>
                                                        </small>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <div class="card-footer bg-surface border-top p-3 text-end">
                                    <button type="button" class="btn btn-primary w-100 fw-bold py-2" data-bs-toggle="modal" data-bs-target="#modal-enroll-{{ $child->id }}">
                                        <i class="ti ti-circle-plus me-1.5 fs-3"></i> Zapisz na nowe zajęcia
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Modal zapisywania uczestnika na zajęcia -->
                        <div class="modal modal-blur fade" id="modal-enroll-{{ $child->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <form action="{{ route('user.children.enroll') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="child_id" value="{{ $child->id }}">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold fs-3">Zapisz uczestnika: {{ $child->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p class="text-secondary mb-3">
                                                Wybierz zajęcia z oferty placówki, na które chcesz zapisać uczestnika.<br>
                                                Szczegółowe informacje o danym kursie, w tym dni i godziny zajęć, znajdziesz <a href="{{ route('user.courses.index') }}" class="text-primary fw-bold">tutaj</a>. 
                                            </p>
                                            <div class="mb-3">
                                                <label class="form-label required fw-bold">Dostępne zajęcia</label>
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
                                                            ({{ number_format($courseOption->price_per_unit, 2, ',', ' ') }} zł) 
                                                            @if($alreadyEnrolled)
                                                                - [JUŻ ZAPISANY/A]
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
                                            <button type="submit" class="btn btn-primary fw-bold">
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

    <!-- Modal dodawania nowego uczestnika -->
    <div class="modal modal-blur fade" id="modal-add-child" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('user.children.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold fs-3">
                            <i class="ti ti-user-plus me-1.5 text-primary"></i> Dodaj dane uczestnika zajęć
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label required fw-bold">Imię i nazwisko uczestnika</label>
                            <input type="text" name="name" class="form-control" placeholder="np. Jan Kowalski" value="{{ old('name') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label required fw-bold">Data urodzenia</label>
                            <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date') }}" required>
                        </div>
                        <!-- <div class="mb-3">
                            <label class="form-label fw-bold">Numer PESEL <span class="text-secondary font-normal">(Opcjonalnie, szyfrowany)</span></label>
                            <input type="text" name="pesel" class="form-control" placeholder="11 cyfr" maxlength="11" value="{{ old('pesel') }}">
                        </div> -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Uwagi / Informacje dla prowadzącego (opcjonalnie)</label>
                            <textarea name="parent_comment" class="form-control" rows="3" placeholder="np. informacje dodatkowe">{{ old('parent_comment') }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Anuluj</button>
                        <button type="submit" class="btn btn-primary fw-bold">
                            <i class="ti ti-plus me-1"></i> Zapisz uczestnika
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
