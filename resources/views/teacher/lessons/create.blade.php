<x-app-layout>
    <div class="page-header d-print-none mb-4">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="mb-1">
                        <a href="{{ route('teacher.courses.show', $course) }}" class="text-secondary">
                            <i class="ti ti-arrow-left me-1"></i> Powrót do zajęć
                        </a>
                    </div>
                    <h2 class="page-title text-primary fw-bold">
                        <i class="ti ti-checkup-list me-2"></i> Realizacja Lekcji: {{ $course->title }}
                    </h2>
                    <div class="text-secondary mt-1">
                        Wybierz datę przeprowadzenia lekcji i sprawdź listę obecności uczniów.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-3" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-alert-circle fs-2 me-2"></i>
                        <div>
                            <strong>Wystąpiły błędy w formularzu:</strong>
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

            <form action="{{ route('teacher.lessons.store', $course) }}" method="POST">
                @csrf

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-surface border-bottom">
                        <h3 class="card-title fw-bold"><i class="ti ti-calendar me-2 text-primary"></i> Szczegóły przeprowadzonej lekcji</h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label required">Data przeprowadzenia lekcji</label>
                                <input type="date" name="realized_at" class="form-control" value="{{ old('realized_at', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Temat lekcji</label>
                                <input type="text" name="topic" class="form-control" placeholder="np. Wprowadzenie do zasad gry, Lekcja #5..." value="{{ old('topic') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Uwagi / Notatka dla nauczyciela</label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="Opcjonalne uwagi odnośnie przebiegu lekcji">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-surface border-bottom d-flex justify-content-between align-items-center">
                        <h3 class="card-title fw-bold">
                            <i class="ti ti-users me-2 text-primary"></i> Lista obecności (Dzieci: {{ $course->children->count() }})
                        </h3>
                    </div>
                    
                    @if($course->children->isEmpty())
                        <div class="card-body text-center text-muted py-5">
                            <i class="ti ti-user-off fs-1 d-block mb-2 text-secondary"></i>
                            Na te zajęcia nie ma jeszcze zapisanych dzieci. Dodaj uczestników przed sprawdzeniem obecności.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table table-hover">
                                <thead>
                                    <tr>
                                        <th class="w-1">#</th>
                                        <th>Imię i nazwisko dziecka</th>
                                        <th>Status obecności</th>
                                        <th>Uwaga do dziecka (opcjonalnie)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($course->children as $index => $child)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <strong class="text-dark fs-3">{{ $child->name }}</strong>
                                                <small class="text-muted d-block">({{ $child->age }} lat)</small>
                                                @if($child->parent_comment)
                                                    <span class="badge bg-warning-lt mt-1">
                                                        <i class="ti ti-alert-triangle me-1"></i> {{ $child->parent_comment }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group w-100" role="group">
                                                    <input type="radio" class="btn-check" name="attendance[{{ $child->id }}][status]" id="status_present_{{ $child->id }}" value="present" checked>
                                                    <label class="btn btn-outline-success" for="status_present_{{ $child->id }}">
                                                        <i class="ti ti-check me-1"></i> Obecny
                                                    </label>

                                                    <input type="radio" class="btn-check" name="attendance[{{ $child->id }}][status]" id="status_absent_{{ $child->id }}" value="absent">
                                                    <label class="btn btn-outline-danger" for="status_absent_{{ $child->id }}">
                                                        <i class="ti ti-x me-1"></i> Nieobecny
                                                    </label>

                                                    <input type="radio" class="btn-check" name="attendance[{{ $child->id }}][status]" id="status_excused_{{ $child->id }}" value="excused">
                                                    <label class="btn btn-outline-warning" for="status_excused_{{ $child->id }}">
                                                        <i class="ti ti-clock me-1"></i> Usprawiedliwiony
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" name="attendance[{{ $child->id }}][notes]" class="form-control form-control-sm" placeholder="np. spóźnienie, zwolnienie...">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <div class="d-flex justify-content-between align-items-center mb-5">
                    <a href="{{ route('teacher.courses.show', $course) }}" class="btn btn-link link-secondary">Anuluj</a>
                    <button type="submit" class="btn btn-success btn-lg" @if($course->children->isEmpty()) disabled @endif>
                        <i class="ti ti-check me-1"></i> Zapisz i zrealizuj lekcję
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
