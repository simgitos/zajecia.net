<x-app-layout>
    <div class="page-header d-print-none mb-4">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title text-primary fw-bold">
                        <i class="ti ti-edit me-2"></i> Edycja Profilu Dziecka
                    </h2>
                    <div class="text-secondary mt-1">
                        Zaktualizuj dane swojego dziecka.
                    </div>
                </div>
                <div class="col-auto ms-auto">
                    <a href="{{ route('user.children.index') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left me-1"></i> Powrót do listy
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                            <strong>Wystąpiły błędy:</strong>
                            <ul class="mb-0 mt-1 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-surface border-bottom">
                            <h3 class="card-title fw-bold">Dane dziecka: {{ $child->name }}</h3>
                        </div>
                        <form action="{{ route('user.children.update', $child) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label required">Imię i nazwisko dziecka</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $child->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label required">Data urodzenia</label>
                                    <input type="date" name="birth_date" class="form-control @error('birth_date') is-invalid @enderror" value="{{ old('birth_date', $child->birth_date ? $child->birth_date->format('Y-m-d') : '') }}" required>
                                    @error('birth_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Numer PESEL <span class="text-secondary">(Opcjonalnie, bezpiecznie szyfrowany w bazie)</span></label>
                                    <input type="text" name="pesel" class="form-control @error('pesel') is-invalid @enderror" maxlength="11" value="{{ old('pesel', $child->pesel) }}" placeholder="11 cyfr">
                                    @error('pesel')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Uwagi / Komentarz dla organizatora</label>
                                    <textarea name="parent_comment" class="form-control @error('parent_comment') is-invalid @enderror" rows="3" placeholder="Informacje o zdrowiu, alergiach itp.">{{ old('parent_comment', $child->parent_comment) }}</textarea>
                                    @error('parent_comment')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="card-footer bg-surface border-top d-flex justify-content-between align-items-center">
                                <a href="{{ route('user.children.index') }}" class="btn btn-link link-secondary">Anuluj</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-check me-1"></i> Zapisz zmiany
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
