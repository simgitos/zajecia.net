@auth
    @php
        $userActiveSchool = Auth::user()->activeSchool();
        $needsTermsAcceptance = $userActiveSchool && !Auth::user()->hasAcceptedTermsForActiveSchool();
    @endphp

    @if($needsTermsAcceptance)
        <div class="card shadow-sm border-0 border-start border-warning border-4 bg-yellow-lt mb-4">
            <div class="card-body p-3 p-md-4">
                <div class="row align-items-center g-3">
                    <div class="col-auto">
                        <span class="avatar avatar-md bg-warning text-white rounded-circle shadow-xs">
                            <i class="ti ti-file-certificate fs-1"></i>
                        </span>
                    </div>
                    <div class="col">
                        <h3 class="card-title fw-bold text-dark mb-1 fs-3">
                            Wymagana akceptacja regulaminu placówki: {{ $userActiveSchool->name }}
                        </h3>
                        <div class="text-secondary fs-4">
                            Aby korzystać z zapisów na kursy oraz pełnej oferty placówki, prosimy o zapoznanie się i zaakceptowanie regulaminu placówki.
                        </div>
                    </div>
                    <div class="col-12 col-md-auto mt-2 mt-md-0">
                        <form action="{{ route('school.terms.accept') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-md fw-bold text-dark shadow-sm">
                                <i class="ti ti-check me-1"></i> Akceptuję regulamin placówki
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endauth
