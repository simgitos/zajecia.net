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
                <div class="col-12 col-md-4">
                    <div class="card  shadow-sm h-100" style="border-left: 4px solid var(--tblr-success) !important;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="subheader fw-bold text-uppercase text-secondary mb-1 fs-6">Moje
                                    zajęcia</span>
                                <div class="bg-success-lt text-success">
                                    <i class="ti ti-books"></i>
                                </div>
                            </div>
                            <div class="h1 mb-1 fw-extrabold text-dark fs-1">{{ $teacherCourses->count() }}</div>
                            <div class="text-secondary" style="font-size:0.78rem;">przypisanych grup / kursów</div>
                            <a href="{{ route('teacher.courses.index') }}"
                                class="btn btn-primary w-100 btn-sm fw-bold shadow-xs py-1.5">
                                <i class="ti ti-users me-1"></i> Lista grup
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Kafelek: Uczestnicy --}}
                <div class="col-6 col-md-4">
                    <div class="card  shadow-sm h-100" style="border-left: 4px solid var(--tblr-warning) !important;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="subheader fw-bold text-uppercase text-secondary mb-1 fs-6">Uczestnicy</span>
                                <div class="bg-warning-lt text-warning">
                                    <i class="ti ti-users"></i>
                                </div>
                            </div>
                            <div class="text-dark mb-1">{{ $assignedChildrenCount }}</div>
                            <div class="text-secondary" style="font-size:0.78rem;">zapisanych uczestników</div>
                            <a href="{{ route('teacher.children.index') }}" class="btn btn-warning btn-sm fw-bold shadow-xs py-1.5">
                                 Lista uczestników
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Kafelek: Zrealizowane --}}
                <div class="col-6 col-md-4">
                    <div class="card shadow-sm h-100" style="border-left: 4px solid var(--tblr-primary) !important;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="subheader fw-bold text-uppercase text-secondary mb-1 fs-6">Zrealizowane</span>
                                <div class="bg-blue-lt text-primary">
                                    <i class="ti ti-calendar-check"></i>
                                </div>
                            </div>
                            <div class="text-dark mb-1">{{ $currentMonthLessonsCount }}</div>
                            <div class="text-secondary" style="font-size:0.78rem;">
                                terminy w
                                {{ \Carbon\Carbon::create()->month($filterMonth)->locale('pl')->isoFormat('MMMM') }}
                                {{ $filterYear !== now()->year ? $filterYear : '' }}
                            </div>
                            <a href="{{ route('teacher.lessons.index') }}" class="btn btn-primary btn-sm fw-bold shadow-xs py-1.5">
                                Lista lekcji
                            </a>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</x-app-layout>