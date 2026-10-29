@props([
    'title' => '',
    'description' => '',
])

@php
    $user = Auth::user();
    $activeRole = $user?->activeRole();
    $userRoles = $user ? $user->rolesForActiveSchool() : collect();
    $hasMultipleRoles = $userRoles->count() > 1;

    $activeSchool = $user?->activeSchool();
    $userSchools = $user ? $user->schools : collect();
    $hasMultipleSchools = $userSchools->count() > 1;

    $activeSeason = $activeSchool?->activeSeason();
    $seasons = $activeSchool ? $activeSchool->seasons()->orderByDesc('start_date')->get() : collect();
    $hasMultipleSeasons = $seasons->count() > 1;
@endphp

<div class="page-header d-print-none mb-3 border-bottom pb-3">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">

             {{-- Tytuł i opis --}}
                @if($title)
                    <div class="text-secondary fw-semibold h3">
                        <i class="ti ti-users me-1"></i> {{$title}}
                    </div>
                @endif
                @if($description)
                    <div class="text-secondary mt-1" style="font-size:0.85rem;">
                        {{ $description }}
                    </div>
                @endif
            </div>
            @if(isset($actions))
                <div class="col-auto ms-auto d-print-none">
                    {{ $actions }}ss
                </div>
            @endif
            
                {{-- Globalne info: Rola, Placówka, Sezon z opcją przełączania jeśli jest kilka pozycji --}}
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    {{-- 1. Rola --}}
                    @if($activeRole)
                        @if($hasMultipleRoles)
                            <div class="dropdown d-inline-block">
                                <button type="button" class="btn btn-sm badge bg-{{ $activeRole->color() }}-lt border-0 dropdown-toggle px-2 py-1 fw-semibold align-middle text-reset"
                                    data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.75rem; letter-spacing: normal; cursor: pointer;">
                                    <i class="ti {{ $activeRole->icon() }} me-1"></i>{{ $activeRole->label() }}
                                </button>
                                <ul class="dropdown-menu shadow-sm">
                                    <li class="dropdown-header text-uppercase fs-6">Przełącz rolę:</li>
                                    @foreach($userRoles as $role)
                                        <li>
                                            <form action="{{ route('role.switch') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="role" value="{{ $role->value }}">
                                                <button type="submit" class="dropdown-item d-flex align-items-center justify-content-between py-1 px-3 {{ $activeRole->value === $role->value ? 'active' : '' }}">
                                                    <span>
                                                        <i class="ti {{ $role->icon() }} text-{{ $role->color() }} me-2"></i>{{ $role->label() }}
                                                    </span>
                                                    @if($activeRole->value === $role->value)
                                                        <i class="ti ti-check text-success ms-2"></i>
                                                    @endif
                                                </button>
                                            </form>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <span class="badge bg-{{ $activeRole->color() }}-lt border-0 px-2 py-1 fw-semibold align-middle" style="font-size: 0.75rem;">
                                <i class="ti {{ $activeRole->icon() }} me-1"></i>{{ $activeRole->label() }}
                            </span>
                        @endif
                    @endif

                    {{-- 2. Placówka / Szkoła --}}
                    @if($activeSchool)
                        @if($hasMultipleSchools)
                            <div class="dropdown d-inline-block">
                                <button type="button" class="btn btn-sm badge bg-purple-lt border-0 dropdown-toggle px-2 py-1 fw-semibold align-middle text-reset"
                                    data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.75rem; letter-spacing: normal; cursor: pointer;">
                                    <i class="ti ti-school me-1"></i>{{ $activeSchool->name }}
                                </button>
                                <ul class="dropdown-menu shadow-sm">
                                    <li class="dropdown-header text-uppercase fs-6">Przełącz placówkę:</li>
                                    @foreach($userSchools as $schoolItem)
                                        <li>
                                            <form action="{{ route('school.switch') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="school_id" value="{{ $schoolItem->id }}">
                                                <button type="submit" class="dropdown-item d-flex align-items-center justify-content-between py-1 px-3 {{ $activeSchool->id === $schoolItem->id ? 'active' : '' }}">
                                                    <span class="text-truncate">
                                                        <i class="ti ti-school text-purple me-2"></i>{{ $schoolItem->name }}
                                                    </span>
                                                    @if($activeSchool->id === $schoolItem->id)
                                                        <i class="ti ti-check text-success ms-2"></i>
                                                    @endif
                                                </button>
                                            </form>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <span class="badge bg-purple-lt border-0 px-2 py-1 fw-semibold align-middle" style="font-size: 0.75rem;">
                                <i class="ti ti-school me-1"></i>{{ $activeSchool->name }}
                            </span>
                        @endif
                    @endif

                    {{-- 3. Sezon --}}
                    @if($activeSeason)
                        @if($hasMultipleSeasons)
                            <div class="dropdown d-inline-block">
                                <button type="button" class="btn btn-sm badge bg-teal-lt border-0 dropdown-toggle px-2 py-1 fw-semibold align-middle text-reset"
                                    data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.75rem; letter-spacing: normal; cursor: pointer;">
                                    <i class="ti ti-calendar me-1"></i>{{ $activeSeason->name }}
                                </button>
                                <ul class="dropdown-menu shadow-sm">
                                    <li class="dropdown-header text-uppercase fs-6">Przełącz sezon:</li>
                                    @foreach($seasons as $seasonItem)
                                        <li>
                                            <form action="{{ route('season.switch') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="season_id" value="{{ $seasonItem->id }}">
                                                <button type="submit" class="dropdown-item d-flex align-items-center justify-content-between py-1 px-3 {{ $activeSeason->id === $seasonItem->id ? 'active' : '' }}">
                                                    <span class="text-truncate">
                                                        <i class="ti ti-calendar text-teal me-2"></i>{{ $seasonItem->name }}
                                                    </span>
                                                    @if($activeSeason->id === $seasonItem->id)
                                                        <i class="ti ti-check text-success ms-2"></i>
                                                    @endif
                                                </button>
                                            </form>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <span class="badge bg-teal-lt border-0 px-2 py-1 fw-semibold align-middle" style="font-size: 0.75rem;">
                                <i class="ti ti-calendar me-1"></i>{{ $activeSeason->name }}
                            </span>
                        @endif
                    @endif
                </div>

               
        </div>
    </div>
</div>