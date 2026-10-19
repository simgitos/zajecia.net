@extends('layouts.app')

@section('title', 'Wyślij powiadomienie')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Komunikacja i powiadomienia</div>
                <h2 class="page-title d-flex align-items-center">
                    <i class="ti ti-send me-2 text-primary"></i> Wyślij nowe powiadomienie
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ route('notifications.index') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-list me-1"></i> Moje powiadomienia
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible shadow-xs mb-3" role="alert">
                <div class="d-flex">
                    <div><i class="ti ti-check fs-2 me-2"></i></div>
                    <div>{{ session('success') }}</div>
                </div>
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible shadow-xs mb-3" role="alert">
                <div class="d-flex">
                    <div><i class="ti ti-alert-circle fs-2 me-2"></i></div>
                    <div>{{ session('error') }}</div>
                </div>
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible shadow-xs mb-3" role="alert">
                <div class="fw-bold mb-1"><i class="ti ti-alert-triangle me-1"></i> Proszę poprawić błędy w formularzu:</div>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        @endif

        <div class="row row-cards">
            {{-- Główny formularz --}}
            <div class="col-lg-8">
                <form action="{{ route('admin.notifications.store') }}" method="POST">
                    @csrf

                    <div class="card shadow-xs border-0 mb-3">
                        <div class="card-header bg-surface py-3">
                            <h3 class="card-title fw-bold">
                                <i class="ti ti-users me-1.5 text-primary"></i> 1. Wybór grupy odbiorców
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label required">Do kogo kierujesz powiadomienie?</label>
                                <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column gap-2">
                                    <label class="form-selectgroup-item flex-fill">
                                        <input type="radio" name="target_type" value="group" class="form-selectgroup-input" {{ old('target_type', 'group') === 'group' ? 'checked' : '' }} onchange="updateTargetFields()">
                                        <div class="form-selectgroup-label d-flex align-items-center p-3">
                                            <div class="me-3">
                                                <span class="avatar avatar-md bg-primary-lt rounded-circle"><i class="ti ti-books fs-2"></i></span>
                                            </div>
                                            <div class="text-start">
                                                <div class="font-weight-medium">Cała grupa zajęciowa</div>
                                                <div class="text-secondary small">Wszyscy rodzice dzieci zapisanych na wybrane zajęcia</div>
                                            </div>
                                        </div>
                                    </label>

                                    <label class="form-selectgroup-item flex-fill">
                                        <input type="radio" name="target_type" value="single_user" class="form-selectgroup-input" {{ old('target_type') === 'single_user' ? 'checked' : '' }} onchange="updateTargetFields()">
                                        <div class="form-selectgroup-label d-flex align-items-center p-3">
                                            <div class="me-3">
                                                <span class="avatar avatar-md bg-azure-lt rounded-circle"><i class="ti ti-user fs-2"></i></span>
                                            </div>
                                            <div class="text-start">
                                                <div class="font-weight-medium">Pojedyncza osoba / rodzic</div>
                                                <div class="text-secondary small">Wiadomość indywidualna skierowana do konkretnego użytkownika</div>
                                            </div>
                                        </div>
                                    </label>

                                    <label class="form-selectgroup-item flex-fill">
                                        <input type="radio" name="target_type" value="instructors" class="form-selectgroup-input" {{ old('target_type') === 'instructors' ? 'checked' : '' }} onchange="updateTargetFields()">
                                        <div class="form-selectgroup-label d-flex align-items-center p-3">
                                            <div class="me-3">
                                                <span class="avatar avatar-md bg-warning-lt rounded-circle"><i class="ti ti-school fs-2"></i></span>
                                            </div>
                                            <div class="text-start">
                                                <div class="font-weight-medium">Wszyscy instruktorzy placówki</div>
                                                <div class="text-secondary small">Wszyscy nauczyciele i instruktorzy powiązani ze szkołą</div>
                                            </div>
                                        </div>
                                    </label>

                                    <label class="form-selectgroup-item flex-fill">
                                        <input type="radio" name="target_type" value="all_parents" class="form-selectgroup-input" {{ old('target_type') === 'all_parents' ? 'checked' : '' }} onchange="updateTargetFields()">
                                        <div class="form-selectgroup-label d-flex align-items-center p-3">
                                            <div class="me-3">
                                                <span class="avatar avatar-md bg-green-lt rounded-circle"><i class="ti ti-broadcast fs-2"></i></span>
                                            </div>
                                            <div class="text-start">
                                                <div class="font-weight-medium">Wszyscy rodzice / użytkownicy szkoły</div>
                                                <div class="text-secondary small">Ogłoszenie ogólne rozsyłane do całej społeczności placówki</div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            {{-- Dynamiczny selektor grupy zajęciowej --}}
                            <div class="mb-3" id="course-selector-box">
                                <label class="form-label required">Wybierz grupę zajęciową</label>
                                <select name="course_id" id="course_id" class="form-select">
                                    <option value="">-- Wybierz zajęcia --</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                            {{ $course->title }} ({{ $course->instructor ? $course->instructor->name : 'Brak instruktora' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Dynamiczny selektor pojedynczego użytkownika --}}
                            <div class="mb-3 d-none" id="user-selector-box">
                                <label class="form-label required">Wybierz użytkownika / rodzica</label>
                                <select name="user_id" id="user_id" class="form-select">
                                    <option value="">-- Wybierz użytkownika --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-xs border-0 mb-3">
                        <div class="card-header bg-surface py-3">
                            <h3 class="card-title fw-bold">
                                <i class="ti ti-edit me-1.5 text-primary"></i> 2. Treść powiadomienia
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label required">Typ / Priorytet komunikatu</label>
                                <div class="row g-2">
                                    <div class="col-6 col-sm-3">
                                        <label class="form-check form-check-inline m-0">
                                            <input class="form-check-input" type="radio" name="type" value="info" {{ old('type', 'info') === 'info' ? 'checked' : '' }}>
                                            <span class="form-check-label badge bg-primary-lt text-primary px-2 py-1"><i class="ti ti-info-circle me-1"></i> Informacja</span>
                                        </label>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <label class="form-check form-check-inline m-0">
                                            <input class="form-check-input" type="radio" name="type" value="warning" {{ old('type') === 'warning' ? 'checked' : '' }}>
                                            <span class="form-check-label badge bg-warning-lt text-warning px-2 py-1"><i class="ti ti-alert-triangle me-1"></i> Ostrzeżenie</span>
                                        </label>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <label class="form-check form-check-inline m-0">
                                            <input class="form-check-input" type="radio" name="type" value="danger" {{ old('type') === 'danger' ? 'checked' : '' }}>
                                            <span class="form-check-label badge bg-danger-lt text-danger px-2 py-1"><i class="ti ti-alert-circle me-1"></i> Pilne / Płatność</span>
                                        </label>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <label class="form-check form-check-inline m-0">
                                            <input class="form-check-input" type="radio" name="type" value="success" {{ old('type') === 'success' ? 'checked' : '' }}>
                                            <span class="form-check-label badge bg-success-lt text-success px-2 py-1"><i class="ti ti-check me-1"></i> Sukces</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Temat powiadomienia</label>
                                <input type="text" name="title" id="notification_title" class="form-control" value="{{ old('title') }}" placeholder="np. Odwołanie dzisiejszych zajęć" required maxlength="255">
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Treść wiadomości</label>
                                <textarea name="message" id="notification_message" class="form-control" rows="5" placeholder="Wpisz treść komunikatu..." required maxlength="5000">{{ old('message') }}</textarea>
                                <div class="form-hint">Możesz stosować podział na akapity. Wiadomość wyświetli się czytelnie w panelu odbiorcy.</div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Link docelowy (opcjonalnie)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ti ti-link"></i></span>
                                        <input type="text" name="url" id="notification_url" class="form-control" value="{{ old('url') }}" placeholder="np. /user/platnosci lub /dashboard">
                                    </div>
                                    <div class="form-hint">Adres, do którego odbiorca zostanie przekierowany po kliknięciu.</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Napis na przycisku (opcjonalnie)</label>
                                    <input type="text" name="action_text" id="notification_action_text" class="form-control" value="{{ old('action_text') }}" placeholder="np. Zobacz płatności">
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-surface d-flex justify-content-between align-items-center py-3">
                            <span class="text-secondary small d-flex align-items-center">
                                <i class="ti ti-bolt text-warning me-1"></i> Natychmiastowa wysyłka do bazy danych
                            </span>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="ti ti-send me-1"></i> Wyślij powiadomienie
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Prawy panel: Gotowe szablony i rozbudowa --}}
            <div class="col-lg-4">
                <div class="card shadow-xs border-0 mb-3">
                    <div class="card-header bg-surface py-3">
                        <h3 class="card-title fw-bold">
                            <i class="ti ti-template me-1.5 text-primary"></i> Gotowe szablony
                        </h3>
                    </div>
                    <div class="card-body p-3">
                        <p class="text-secondary small mb-3">
                            Kliknij wybrany szablon, aby błyskawicznie wypełnić temat, treść, sugerowany typ oraz link:
                        </p>
                        <div class="d-flex flex-column gap-2">
                            @foreach($templates as $tpl)
                                <button type="button" 
                                        class="btn btn-outline-secondary text-start p-2.5 d-flex align-items-center justify-content-between template-btn"
                                        onclick="applyTemplate(@js($tpl))">
                                    <div>
                                        <div class="fw-semibold text-dark fs-5">{{ $tpl['name'] }}</div>
                                        <div class="text-secondary small text-truncate" style="max-width: 250px;">{{ $tpl['title'] }}</div>
                                    </div>
                                    <i class="ti ti-arrow-right text-muted ms-2"></i>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Karta modułu płatności --}}
                <div class="card shadow-xs border-0 mb-3 bg-primary-lt-subtle border-start border-3 border-primary">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <span class="avatar avatar-sm bg-primary text-white rounded-circle me-2">
                                <i class="ti ti-receipt-2"></i>
                            </span>
                            <h4 class="card-title mb-0 fw-bold">Rozliczenia miesięczne</h4>
                        </div>
                        <p class="text-secondary small mb-3">
                            Chcesz wysłać zbiorcze podsumowanie opłat za dany miesiąc bezpośrednio z danymi finansowymi rodziców?
                        </p>
                        <a href="{{ route('admin.billing.index') }}" class="btn btn-sm btn-outline-primary w-100">
                            <i class="ti ti-calculator me-1"></i> Przejdź do modułu płatności
                        </a>
                    </div>
                </div>

                {{-- Karta gotowości do SMS i Mail --}}
                <div class="card shadow-xs border-0 mb-3">
                    <div class="card-header bg-surface py-2.5">
                        <h4 class="card-title fs-5 mb-0"><i class="ti ti-plug me-1 text-secondary"></i> Kanały dostarczania</h4>
                    </div>
                    <div class="card-body p-3">
                        <ul class="list-unstyled mb-0 small">
                            <li class="d-flex align-items-center text-success mb-2">
                                <i class="ti ti-check-circle me-2 fs-3"></i>
                                <span><strong>Powiadomienia w aplikacji</strong> (aktywne)</span>
                            </li>
                            <li class="d-flex align-items-center text-secondary mb-2">
                                <i class="ti ti-mail me-2 fs-3"></i>
                                <span><strong>Powiadomienia E-mail</strong> (architektura gotowa)</span>
                            </li>
                            <li class="d-flex align-items-center text-secondary">
                                <i class="ti ti-message-dots me-2 fs-3"></i>
                                <span><strong>Bramka SMS</strong> (architektura gotowa)</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updateTargetFields() {
        const selected = document.querySelector('input[name="target_type"]:checked')?.value;
        const courseBox = document.getElementById('course-selector-box');
        const userBox = document.getElementById('user-selector-box');

        if (selected === 'group') {
            courseBox.classList.remove('d-none');
            userBox.classList.add('d-none');
        } else if (selected === 'single_user') {
            courseBox.classList.add('d-none');
            userBox.classList.remove('d-none');
        } else {
            courseBox.classList.add('d-none');
            userBox.classList.add('d-none');
        }
    }

    function applyTemplate(tpl) {
        document.getElementById('notification_title').value = tpl.title || '';
        document.getElementById('notification_message').value = tpl.message || '';
        document.getElementById('notification_url').value = tpl.url || '';
        document.getElementById('notification_action_text').value = tpl.action_text || '';

        const typeInput = document.querySelector(`input[name="type"][value="${tpl.type}"]`);
        if (typeInput) {
            typeInput.checked = true;
        }

        // Delikatne podświetlenie pól po wstawieniu szablonu
        ['notification_title', 'notification_message'].forEach(id => {
            const el = document.getElementById(id);
            el.classList.add('is-valid');
            setTimeout(() => el.classList.remove('is-valid'), 1500);
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateTargetFields();
    });
</script>
@endsection
