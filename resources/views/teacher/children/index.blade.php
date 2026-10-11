<x-app-layout>
    <div class="page-header d-print-none mb-3">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="text-secondary fw-semibold" style="font-size:0.8rem; text-transform:uppercase; letter-spacing:.06em;">
                        <i class="ti ti-users me-1"></i> Instrukcja / Uczestnicy
                    </div>
                    <h2 class="page-title fw-bold mb-0">
                        Moi Uczestnicy
                    </h2>
                    <div class="text-secondary mt-1" style="font-size:0.85rem;">
                        Lista wszystkich dzieci zapisanych na Twoje zajęcia.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="card shadow-sm border-0" style="border-radius:14px; overflow:hidden;">
                <div class="card-header bg-body d-flex align-items-center justify-content-between">
                    <h3 class="fw-bold mb-0" style="font-size:0.95rem;">
                        <i class="ti ti-users me-2 text-warning"></i> Zapisani uczestnicy
                        <span class="badge bg-warning-lt text-warning ms-1" style="font-size:0.72rem;">{{ $assignedChildren->count() }}</span>
                    </h3>
                </div>

                @if($assignedChildren->isEmpty())
                    <div class="p-5 text-center text-muted">
                        <i class="ti ti-mood-empty d-block mb-2" style="font-size:2.5rem; opacity:0.3;"></i>
                        Brak zapisanych uczestników w Twoich zajęciach.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-vcenter table-hover mb-0" style="font-size:0.85rem;">
                            <thead style="background: rgba(var(--tblr-surface-color-rgb),1);">
                                <tr>
                                    <th>Uczestnik</th>
                                    <th class="d-none d-sm-table-cell">Wiek</th>
                                    <th>Zajęcia / Grupy</th>
                                    <th class="d-none d-md-table-cell">Opiekun (Rodzic)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assignedChildren as $child)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar bg-yellow-lt text-yellow fw-bold rounded-2">
                                                    {{ mb_strtoupper(mb_substr($child->name, 0, 1)) }}
                                                </div>
                                                <span class="fw-semibold text-dark">{{ $child->name }}</span>
                                            </div>
                                        </td>
                                        <td class="d-none d-sm-table-cell text-secondary">
                                            {{ $child->age ? $child->age . ' lat' : '—' }}
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($child->courses as $c)
                                                    <span class="badge bg-surface-secondary text-secondary border" style="font-size:0.72rem; border-radius:6px;">
                                                        {{ $c->title }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="d-none d-md-table-cell text-secondary" style="font-size:0.78rem;">
                                            @if($child->parent)
                                                <div><i class="ti ti-user me-1"></i>{{ $child->parent->name }}</div>
                                                @if($child->parent->phone)
                                                    <div class="text-muted"><i class="ti ti-phone me-1"></i>{{ $child->parent->phone }}</div>
                                                @endif
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
