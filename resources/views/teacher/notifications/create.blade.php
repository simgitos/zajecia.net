@extends('layouts.app')

@section('title', 'Wyślij powiadomienie do grupy')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Panel Instruktora</div>
                <h2 class="page-title d-flex align-items-center">
                    <i class="ti ti-send me-2 text-primary"></i> Wyślij powiadomienie do uczestników
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ route('teacher.courses.index') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left me-1"></i> Moje zajęcia
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
                <div class="fw-bold mb-1"><i class="ti ti-alert-triangle me-1"></i> Proszę poprawić błędy:</div>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        @endif

        <div class="row row-cards">
            <div class="col-lg-8">
                <form action="{{ route('teacher.notifications.store') }}" method="POST">
                    @csrf

                    <div class="card shadow-xs border-0 mb-3">
                        <div class="card-header bg-surface py-3">
                            <h3 class="card-title fw-bold">
                                <i class="ti ti-users me-1.5 text-primary"></i> Odbiorcy komunikatu
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label required">Typ odbiorcy</label>
                                <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column gap-2">
                                    <label class="form-selectgroup-item flex-fill">
                                        <input type="radio" name="target_type" value="group" class="form-selectgroup-input" {{ old('target_type', 'group') === 'group' ? 'checked' : '' }} onchange="updateTargetFields()">
                                        <div class="form-selectgroup-label d-flex align-items-center p-3">
                                            <div class="me-3">
                                                <span class="avatar avatar-md bg-primary-lt rounded-circle"><i class="ti ti-books fs-2"></i></span>
                                            </div>
                                            <div class="text-start">
                                                <div class="font-weight-medium">Moja grupa zajęciowa</div>
                                                <div class="text-secondary small">Wszyscy rodzice dzieci zapisanych na wybrane prowadzone przez Ciebie zajęcia</div>
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
                                                <div class="font-weight-medium">Pojedynczy rodzic uczestnika</div>
                                                <div class="text-secondary small">Wiadomość bezpośrednia do konkretnego rodzica z Twoich grup</div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3" id="course-selector-box">
                                <label class="form-label required">Wybierz Twoje zajęcia</label>
                                <select name="course_id" id="course_id" class="form-select">
                                    <option value="">-- Wybierz zajęcia --</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                            {{ $course->title }} (uczestników: {{ $course->children()->count() }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3 d-none" id="user-selector-box">
                                <label class="form-label required">Wybierz rodzica</label>
                                <select name="user_id" id="user_id" class="form-select">
                                    <option value="">-- Wybierz rodzica --</option>
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
                                <i class="ti ti-edit me-1.5 text-primary"></i> Treść komunikatu
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label required">Typ / Ważność</label>
                                <div class="row g-2">
                                    <div class="col-sm-4">
                                        <label class="form-check form-check-inline m-0">
                                            <input class="form-check-input" type="radio" name="type" value="info" {{ old('type', 'info') === 'info' ? 'checked' : '' }}>
                                            <span class="form-check-label badge bg-primary-lt text-primary px-2 py-1"><i class="ti ti-info-circle me-1"></i> Informacja</span>
                                        </label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-check form-check-inline m-0">
                                            <input class="form-check-input" type="radio" name="type" value="warning" {{ old('type') === 'warning' ? 'checked' : '' }}>
                                            <span class="form-check-label badge bg-warning-lt text-warning px-2 py-1"><i class="ti ti-alert-triangle me-1"></i> Ważne / Odwołanie</span>
                                        </label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-check form-check-inline m-0">
                                            <input class="form-check-input" type="radio" name="type" value="success" {{ old('type') === 'success' ? 'checked' : '' }}>
                                            <span class="form-check-label badge bg-success-lt text-success px-2 py-1"><i class="ti ti-check me-1"></i> Sukces / Pochwała</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Temat powiadomienia</label>
                                <input type="text" name="title" id="notification_title" class="form-control" value="{{ old('title') }}" placeholder="np. Odwołanie zajęć w dniu..." required maxlength="255">
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Treść wiadomości</label>
                                <textarea name="message" id="notification_message" class="form-control" rows="5" placeholder="Wpisz treść wiadomości dla rodziców..." required maxlength="5000">{{ old('message') }}</textarea>
                            </div>
                        </div>
                        <div class="card-footer bg-surface d-flex justify-content-end py-3">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="ti ti-send me-1"></i> Wyślij powiadomienie
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-xs border-0 mb-3">
                    <div class="card-header bg-surface py-3">
                        <h3 class="card-title fw-bold">
                            <i class="ti ti-template me-1.5 text-primary"></i> Szybkie szablony instruktora
                        </h3>
                    </div>
                    <div class="card-body p-3">
                        <p class="text-secondary small mb-3">
                            Wybierz szablon, aby szybko uzupełnić formularz informacją dla rodziców:
                        </p>
                        <div class="d-flex flex-column gap-2">
                            @foreach($templates as $tpl)
                                <button type="button" 
                                        class="btn btn-outline-secondary text-start p-2.5 d-flex align-items-center justify-content-between"
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

        const typeInput = document.querySelector(`input[name="type"][value="${tpl.type}"]`);
        if (typeInput) {
            typeInput.checked = true;
        }

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
