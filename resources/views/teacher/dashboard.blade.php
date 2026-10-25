<x-app-layout>
    <style>
        .stat-card {
            border-radius: 16px;
            border: none;
            transition: transform 0.18s ease, box-shadow 0.18s ease;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.10) !important;
        }

        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }

        .stat-card .stat-number {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1;
        }

        .stat-card .stat-label {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .stat-card .stat-link {
            font-size: 0.78rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            margin-top: 0.5rem;
        }

        .stat-card .stat-link:hover {
            text-decoration: underline;
        }

        /* Sekcja list */
        .section-card {
            border-radius: 14px;
            border: 1px solid rgba(var(--tblr-border-color-rgb), 0.6);
            overflow: hidden;
        }

        .section-card .card-header {
            background: rgba(var(--tblr-body-bg-rgb), 1);
            border-bottom: 1px solid rgba(var(--tblr-border-color-rgb), 0.5);
            padding: 0.85rem 1.1rem;
        }

        .participant-row {
            padding: 0.65rem 1.1rem;
            border-bottom: 1px solid rgba(var(--tblr-border-color-rgb), 0.4);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .participant-row:last-child {
            border-bottom: none;
        }

        .participant-avatar {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 700;
            flex-shrink: 0;
        }

        .lesson-row {
            padding: 0.7rem 1.1rem;
            border-bottom: 1px solid rgba(var(--tblr-border-color-rgb), 0.4);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            text-decoration: none;
            color: inherit;
            transition: background 0.12s;
        }

        .lesson-row:last-child {
            border-bottom: none;
        }

        .lesson-row:hover {
            background: rgba(var(--tblr-primary-rgb), 0.04);
        }

        .filter-month-form .form-select {
            font-size: 0.82rem;
            padding: 0.3rem 0.75rem;
            border-radius: 8px;
            height: auto;
        }

        @media (max-width: 575px) {
            .stat-card .stat-number {
                font-size: 1.6rem;
            }
        }
    </style>

    <div class="page-header d-print-none mb-3">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="text-secondary fw-semibold"
                        style="font-size:0.8rem; text-transform:uppercase; letter-spacing:.06em;">
                        <i class="ti ti-user-check me-1"></i> Panel Instruktora
                    </div>
                    <h2 class="page-title fw-bold mb-0">
                        Witaj, {{ Auth::user()->name }} 👋
                        @if(isset($activeSeason) && $activeSeason)
                            <span class="badge bg-primary-lt fs-6 ms-2 align-middle">
                                <i class="ti ti-calendar me-1"></i> {{ $activeSeason->name }}
                            </span>
                        @endif
                    </h2>
                    <div class="text-secondary mt-1" style="font-size:0.85rem;">
                        Zarządzaj swoimi grupami, realizuj zajęcia i dbaj o ewidencję obecności.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">

            {{-- ===================== 3 KAFELKI STATYSTYK ===================== --}}
            <div class="row g-3 mb-4">


                {{-- Kafelek: Moje zajęcia --}}
                
                <x-stats icon="ti ti-books" color="success" title="Moje zajęcia" value="{{ $teacherCourses->count() }}" route="{{ route('teacher.courses.index') }}" description="Zarządzaj swoimi zajęciami" ></x-stats>
                

                {{-- Kafelek: Uczestnicy --}}
                <x-stats icon="ti ti-users" color="warning" title="Uczestnicy" value="{{ $assignedChildrenCount }}" route="{{ route('teacher.children.index') }}" description="Zarządzaj uczestnikami" ></x-stats>
                

                {{-- Kafelek: Zrealizowane --}}
                <x-stats icon="ti ti-calendar-check" color="primary" title="Zrealizowane" value="{{ $currentMonthLessonsCount }}" route="{{ route('teacher.lessons.index') }}" description="Zrealizowane zajęcia" ></x-stats>
                
                
            </div>


        </div>
    </div>
</x-app-layout>