@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm mb-3" role="alert">
        <div class="d-flex align-items-center">
            <i class="ti ti-circle-check fs-2 me-2"></i>
            <div>{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-3" role="alert">
        <i class="ti ti-alert-triangle me-2 fs-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-3" role="alert">
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