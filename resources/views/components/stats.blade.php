<div class="col-6 col-lg-3">
    <div class="card shadow border-0 border-start border-{{$color}} border-4 h-100">
        <div class="card-body p-2 p-sm-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="subheader fw-bold text-uppercase fs-7">{{$title}}</div>
                <span class="avatar bg-{{$color}}-subtle text-{{$color}} rounded-circle fs-2">
                    <i class="{{$icon}}"></i>
                </span>
            </div>
            <div class="h1 mb-1 fw-extrabold text-dark fs-1">{{ $value }}</div>
            <div class="text-secondary small">{{ $description }}
                <a href="{{ $route }}" class="text-{{$color}} stretched-link"></a>
            </div>
        </div>
    </div>
</div>