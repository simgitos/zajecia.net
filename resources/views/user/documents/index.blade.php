<x-app-layout>

<x-user-header title="Dokumenty i Zgody" description="Dokumenty obowiązujące w placówce {{ $school->name }} oraz status Twoich oświadczeń i zgód." icon="ti ti-files"></x-user-header>

   
    <div class="page-body">
        <div class="container-xl">

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm mb-3" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-circle-check fs-2 me-2"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row row-cards">
                {{-- Lista oficjalnych dokumentów placówki --}}
                <div class="col-lg-7">
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-header bg-surface d-flex justify-content-between align-items-center">
                            <h3 class="card-title fw-bold mb-0">
                                <i class="ti ti-folder-check me-2 text-primary"></i> Oficjalne dokumenty placówki
                            </h3>
                            <span class="badge bg-primary-lt">{{ $documents->count() }} {{ trans_choice('dokument|dokumenty|dokumentów', $documents->count()) }}</span>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-vcenter card-table table-hover">
                                <thead>
                                    <tr>
                                        <th>Typ & Tytuł</th>
                                        <th>Rozmiar</th>
                                        <th class="w-1 text-end">Pobierz</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($documents as $doc)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-start gap-2">
                                                    <span class="avatar avatar-sm bg-{{ $doc->type->badgeColor() }}-lt text-{{ $doc->type->badgeColor() }} rounded flex-shrink-0 mt-1">
                                                        <i class="ti ti-file-text fs-2"></i>
                                                    </span>
                                                    <div>
                                                        <div class="fw-bold text-dark">{{ $doc->title }}</div>
                                                        <div class="d-flex align-items-center gap-2 mt-1">
                                                            <span class="badge bg-{{ $doc->type->badgeColor() }}-lt">
                                                                {{ $doc->type->label() }}
                                                            </span>
                                                            @if($doc->is_required)
                                                                <span class="badge bg-warning-lt">Wymagany</span>
                                                            @endif
                                                        </div>
                                                        @if($doc->description)
                                                            <div class="text-secondary small mt-1">{{ $doc->description }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-secondary small text-nowrap">
                                                {{ $doc->formattedSize() }}
                                            </td>
                                            <td class="text-end text-nowrap">
                                                <a href="{{ route('user.documents.download', $doc) }}" class="btn btn-sm btn-outline-primary fw-semibold shadow-xs">
                                                    <i class="ti ti-download me-1"></i> Pobierz
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4 text-secondary">
                                                <i class="ti ti-file-off fs-1 d-block mb-2 text-muted"></i>
                                                Placówka nie udostępniła jeszcze żadnych dokumentów do pobrania.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Status oświadczeń i zgód użytkownika --}}
                <div class="col-lg-5">
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-header bg-surface">
                            <h3 class="card-title fw-bold mb-0">
                                <i class="ti ti-shield-check me-2 text-success"></i> Twoje zgody i oświadczenia
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush mb-3">
                                {{-- Regulamin --}}
                                <div class="list-group-item px-0 py-3">
                                    <div class="d-flex align-items-start justify-content-between gap-2">
                                        <div>
                                            <div class="fw-bold text-dark">Regulamin i Polityka Prywatności</div>
                                            <div class="small text-secondary">Wymagana akceptacja warunków uczestnictwa w zajęciach.</div>
                                        </div>
                                        <div class="text-end flex-shrink-0">
                                            @if($membership && $membership->pivot->terms_accepted_at)
                                                <span class="badge bg-success text-white">Zaakceptowano</span>
                                                <div class="small text-muted mt-1">
                                                    {{ \Carbon\Carbon::parse($membership->pivot->terms_accepted_at)->format('d.m.Y H:i') }}
                                                </div>
                                                @if($membership->pivot->terms_accepted_ip)
                                                    <div class="text-muted" style="font-size: 0.75rem;">IP: {{ $membership->pivot->terms_accepted_ip }}</div>
                                                @endif
                                            @else
                                                <span class="badge bg-danger text-white">Oczekuje na akceptację</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- RODO --}}
                                <div class="list-group-item px-0 py-3">
                                    <div class="d-flex align-items-start justify-content-between gap-2">
                                        <div>
                                            <div class="fw-bold text-dark">Klauzula Informacyjna RODO</div>
                                            <div class="small text-secondary">Potwierdzenie zapoznania się z przetwarzaniem danych osobowych.</div>
                                        </div>
                                        <div class="text-end flex-shrink-0">
                                            @if($membership && $membership->pivot->rodo_accepted_at)
                                                <span class="badge bg-success text-white">Zapoznano się</span>
                                                <div class="small text-muted mt-1">
                                                    {{ \Carbon\Carbon::parse($membership->pivot->rodo_accepted_at)->format('d.m.Y H:i') }}
                                                </div>
                                                @if($membership->pivot->rodo_accepted_ip)
                                                    <div class="text-muted" style="font-size: 0.75rem;">IP: {{ $membership->pivot->rodo_accepted_ip }}</div>
                                                @endif
                                            @else
                                                <span class="badge bg-danger text-white">Oczekuje na akceptację</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Formularz edycji opcjonalnych zgód --}}
                            <form action="{{ route('user.documents.consents') }}" method="POST">
                                @csrf
                                <div class="bg-light-lt p-3 rounded-2 border mb-3">
                                    <div class="fw-bold text-dark mb-2">Zgody opcjonalne (możesz zmienić w każdej chwili):</div>

                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="image_consent" id="user_consent_image" value="1" {{ ($membership && $membership->pivot->image_consent_accepted_at) ? 'checked' : '' }}>
                                        <label class="form-check-label text-dark" for="user_consent_image">
                                            Wyrażam zgodę na publikację wizerunku dziecka (Opcjonalnie).
                                        </label>
                                        @if($membership && $membership->pivot->image_consent_accepted_at)
                                            <div class="small text-success ms-1">
                                                <i class="ti ti-check"></i> Wyrażono: {{ \Carbon\Carbon::parse($membership->pivot->image_consent_accepted_at)->format('d.m.Y H:i') }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="marketing_consent" id="user_consent_marketing" value="1" {{ ($membership && $membership->pivot->marketing_consent_accepted_at) ? 'checked' : '' }}>
                                        <label class="form-check-label text-dark" for="user_consent_marketing">
                                            Wyrażam zgodę na kontakt SMS/e-mail w celach promocyjnych (Opcjonalnie).
                                        </label>
                                        @if($membership && $membership->pivot->marketing_consent_accepted_at)
                                            <div class="small text-success ms-1">
                                                <i class="ti ti-check"></i> Wyrażono: {{ \Carbon\Carbon::parse($membership->pivot->marketing_consent_accepted_at)->format('d.m.Y H:i') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-outline-primary btn-sm w-100 fw-bold">
                                    <i class="ti ti-device-floppy me-1"></i> Zapisz zgody opcjonalne
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
