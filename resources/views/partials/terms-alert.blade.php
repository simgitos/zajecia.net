@auth
    @php
        $userActiveSchool = Auth::user()->activeSchool();
        $needsTermsAcceptance = $userActiveSchool && !Auth::user()->hasAcceptedTermsForActiveSchool();
        $termsDoc = $needsTermsAcceptance ? $userActiveSchool->documents()->where('type', \App\Enums\DocumentType::TERMS)->where('is_active', true)->first() : null;
        $rodoDoc = $needsTermsAcceptance ? $userActiveSchool->documents()->where('type', \App\Enums\DocumentType::RODO)->where('is_active', true)->first() : null;
        $privacyDoc = $needsTermsAcceptance ? $userActiveSchool->documents()->where('type', \App\Enums\DocumentType::PRIVACY_POLICY)->where('is_active', true)->first() : null;
    @endphp

    @if($needsTermsAcceptance)
        <div class="card shadow-sm border-0 border-start border-warning border-4 bg-yellow-lt mb-4">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex align-items-start gap-3 mb-3">
                    <span class="avatar avatar-md bg-warning text-white rounded-circle shadow-xs flex-shrink-0">
                        <i class="ti ti-file-certificate fs-1"></i>
                    </span>
                    <div>
                        <h3 class="card-title fw-bold text-dark mb-1 fs-3">
                            Wymagana akceptacja dokumentów placówki: {{ $userActiveSchool->name }}
                        </h3>
                        <div class="text-secondary fs-4">
                            Aby korzystać z zapisów na zajęcia oraz pełnej oferty placówki, prosimy o zapoznanie się z
                            dokumentami i zaznaczenie wymaganych oświadczeń.
                        </div>
                    </div>
                </div>

                @if($errors->has('terms_accepted') || $errors->has('rodo_accepted'))
                    <div class="alert alert-danger py-2 mb-3">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('school.terms.accept') }}" method="POST" id="school-consents-form">
                    @csrf

                    <div class="card bg-white border p-3 rounded-2 shadow-xs mb-3">
                        <div class="form-check mb-2">
                            <input class="form-check-input consent-checkbox" type="checkbox" name="terms_accepted"
                                id="consent_terms" value="1" required {{ old('terms_accepted') ? 'checked' : '' }}>
                            <label class="form-check-label" for="consent_terms">
                                Zapoznałem i akceptuję 
                                @if($termsDoc)
                                    <a href="{{ route('admin.documents.download', $termsDoc) }}" target="_blank">Regulamin {{ $userActiveSchool->name }}</a>
                                @else
                                    Regulamin {{ $userActiveSchool->name }}
                                @endif
                                .*
                            </label>
                        </div>

                        <div class="form-check mb-2">
                            <input class="form-check-input consent-checkbox" type="checkbox" name="rodo_accepted"
                                id="consent_rodo" value="1" required {{ old('rodo_accepted') ? 'checked' : '' }}>
                            <label class="form-check-label" for="consent_rodo">
                                Zapoznałem/am się z 
                                @if($rodoDoc)
                                    <a href="{{ route('admin.documents.download', $rodoDoc) }}" target="_blank">Klauzulą Informacyjną RODO</a>
                                @else
                                    Klauzulą Informacyjną RODO
                                @endif
                                .*
                            </label>
                        </div>

                        <!-- <div class="form-check mb-2">
                            <input class="form-check-input consent-checkbox" type="checkbox" name="image_consent"
                                id="consent_image" value="1" {{ old('image_consent') ? 'checked' : '' }}>
                            <label class="form-check-label" for="consent_image">
                                Wyrażam zgodę na publikację wizerunku uczestników zajęć w materiałach 
                                {{ $userActiveSchool->name }}.
                            </label>
                        </div>

                        <div class="form-check mb-2">
                            <input class="form-check-input consent-checkbox" type="checkbox" name="marketing_consent"
                                id="consent_marketing" value="1" {{ old('marketing_consent') ? 'checked' : '' }}>
                            <label class="form-check-label" for="consent_marketing">
                                Wyrażam zgodę na kontakt SMS/e-mail w celach promocyjnych.
                            </label>
                        </div> -->

                        <div class="form-check fw-bold mt-2 pt-2 border-top">
                            <input type="checkbox" class="form-check-input" name="select-all"
                                id="btn-select-all-consents">
                            <label class="form-check-label" for="btn-select-all-consents">
                                Zaznacz wszystkie zgody
                            </label>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <button type="submit" class="btn btn-warning fw-bold text-dark ms-auto shadow-sm">
                            <i class="ti ti-check me-1"></i> Potwierdź i zaakceptuj regulamin
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var btnSelectAll = document.getElementById('btn-select-all-consents');
                if (btnSelectAll) {
                    btnSelectAll.addEventListener('change', function () {
                        document.querySelectorAll('.consent-checkbox').forEach(function (cb) {
                            cb.checked = btnSelectAll.checked;
                        });
                    });
                }
            });
        </script>
    @endif
@endauth