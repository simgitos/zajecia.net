@props(['title' => '', 'description' => ''])

@php
    $user = Auth::user();
    $activeRole = $user?->activeRole()?->value ?? $user?->role;
    $panelLabel = match ($activeRole) {
        'admin', \App\Enums\UserRole::ADMIN->value => 'Panel Administratora',
        'teacher', \App\Enums\UserRole::TEACHER->value => 'Panel Instruktora',
        default => 'Panel Użytkownika',
    };
    $activeSchool = $user?->activeSchool();
@endphp

<div class="page-header d-print-none mb-3 border-bottom pb-3 mt-0">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="text-secondary fw-semibold"
                    style="font-size:0.8rem; text-transform:uppercase; letter-spacing:.06em;">
                    <i class="ti ti-user-check me-1"></i>
                    {{ $panelLabel }}{{ $activeSchool ? ' - ' . $activeSchool->name : '' }}
                </div>
                <h2 class="page-title fw-bold mb-0">
                    @if(!empty($icon)) <i class="{{ $icon }} me-2"></i> 
                    @endif 
                    {{ $title }}
                </h2>
                @if(!empty($description))
                    <div class="text-secondary mt-1" style="font-size:0.85rem;">
                        {{ $description }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>