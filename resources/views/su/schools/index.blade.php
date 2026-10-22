@extends('layouts.app')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle text-danger fw-bold">
                <i class="ti ti-shield-bolt me-1"></i> PANEL SUPERADMINA (SU)
            </div>
            <h2 class="page-title text-dark fw-bold fs-2">
                Zarządzanie Szkołami i Placówkami
            </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="{{ route('su.schools.create') }}" class="btn btn-primary shadow-sm">
                <i class="ti ti-plus me-1"></i> Dodaj nową placówkę
            </a>
        </div>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm mb-3" role="alert">
        <i class="ti ti-circle-check me-2 fs-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-3" role="alert">
        <i class="ti ti-alert-triangle me-2 fs-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Kafelki podsumowujące -->
<div class="row row-cards mb-4">
    <div class="col-sm-6 col-lg-4">
        <div class="card card-sm shadow-xs border-0">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar bg-primary-lt text-primary rounded">
                            <i class="ti ti-building-community fs-2"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium">Wszystkie placówki</div>
                        <div class="text-secondary fs-3 fw-bold">{{ $stats['total'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card card-sm shadow-xs border-0">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar bg-success-lt text-success rounded">
                            <i class="ti ti-check fs-2"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium">Aktywne placówki</div>
                        <div class="text-secondary fs-3 fw-bold">{{ $stats['active'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card card-sm shadow-xs border-0">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar bg-green-lt text-success rounded">
                            <i class="ti ti-shield-check fs-2"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium">Biała lista Tpay Collect</div>
                        <div class="text-secondary fs-3 fw-bold">{{ $stats['whitelisted'] }} / {{ $stats['total'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabela Placówek -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-surface py-3">
        <form method="GET" action="{{ route('su.schools.index') }}" class="row g-2 align-items-center w-100">
            <div class="col-md-4">
                <div class="input-icon">
                    <span class="input-icon-addon">
                        <i class="ti ti-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control" placeholder="Szukaj po nazwie, NIP, e-mail, koncie..." value="{{ $search }}">
                </div>
            </div>
            <div class="col-md-2">
                <select name="plan" class="form-select">
                    <option value="">Wszystkie plany</option>
                    <option value="solo" {{ $planFilter === 'solo' ? 'selected' : '' }}>Solo</option>
                    <option value="start" {{ $planFilter === 'start' ? 'selected' : '' }}>Start</option>
                    <option value="standard" {{ $planFilter === 'standard' ? 'selected' : '' }}>Standard</option>
                    <option value="pro" {{ $planFilter === 'pro' ? 'selected' : '' }}>Pro / Maxi</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">Status placówki</option>
                    <option value="active" {{ $statusFilter === 'active' ? 'selected' : '' }}>Aktywne</option>
                    <option value="suspended" {{ $statusFilter === 'suspended' ? 'selected' : '' }}>Zawieszone</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="whitelist" class="form-select">
                    <option value="">Biała lista Tpay</option>
                    <option value="verified" {{ ($whitelistFilter ?? '') === 'verified' ? 'selected' : '' }}>Zweryfikowane</option>
                    <option value="unverified" {{ ($whitelistFilter ?? '') === 'unverified' ? 'selected' : '' }}>Niezweryfikowane</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-secondary w-100">Filtruj</button>
                @if($search || $planFilter || $statusFilter || $whitelistFilter)
                    <a href="{{ route('su.schools.index') }}" class="btn btn-outline-secondary" title="Wyczyść filtry">
                        <i class="ti ti-x"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-vcenter card-table table-hover">
            <thead>
                <tr>
                    <th>Placówka</th>
                    <th>NIP / Kontakt</th>
                    <th>Plan taryfowy</th>
                    <th>Rachunek bankowy & Biała lista Tpay</th>
                    <th>Statystyki</th>
                    <th>Status</th>
                    <th class="w-1 text-end">Akcje</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($schools as $school)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="avatar avatar-sm bg-primary-lt text-primary rounded fw-bold">
                                    {{ mb_substr($school->name, 0, 1) }}
                                </span>
                                <div>
                                    <div class="fw-bold text-dark fs-4">{{ $school->name }}</div>
                                    @if($school->full_name && $school->full_name !== $school->name)
                                        <div class="text-muted small text-truncate" style="max-width: 250px;" title="{{ $school->full_name }}">
                                            {{ $school->full_name }}
                                        </div>
                                    @endif
                                    <div class="text-secondary small">
                                        slug: <code>/{{ $school->slug }}</code>
                                        @if($school->city) • {{ $school->city }} @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($school->nip)
                                <div><i class="ti ti-id me-1 text-muted"></i>NIP: <strong>{{ $school->nip }}</strong></div>
                            @endif
                            @if($school->email)
                                <div class="small text-secondary"><i class="ti ti-mail me-1"></i>{{ $school->email }}</div>
                            @endif
                            @if($school->phone)
                                <div class="small text-secondary"><i class="ti ti-phone me-1"></i>{{ $school->phone }}</div>
                            @endif
                        </td>
                        <td>
                            @php
                                $planColors = [
                                    'solo' => 'secondary',
                                    'start' => 'info',
                                    'standard' => 'primary',
                                    'pro' => 'success',
                                    'maxi' => 'success',
                                ];
                                $planBadge = $planColors[strtolower((string) $school->subscription_plan)] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $planBadge }}-lt fs-5 text-uppercase fw-bold">
                                {{ $school->subscription_plan ?? 'Brak' }}
                            </span>
                        </td>
                        <td>
                            @if(!empty($school->bank_account_number))
                                <div class="font-monospace small text-dark mb-1">
                                    {{ $school->formattedIban() }}
                                </div>
                                @if($school->tpay_whitelist_status === 'verified')
                                    <span class="badge bg-success text-white" title="Rachunek został pomyślnie zweryfikowany na białej liście Tpay Collect">
                                        <i class="ti ti-shield-check me-1"></i> Zweryfikowane (Biała lista)
                                    </span>
                                @elseif($school->tpay_whitelist_status === 'pending')
                                    <span class="badge bg-warning text-dark" title="Zgłoszenie weryfikacji w toku">
                                        <i class="ti ti-clock me-1"></i> Oczekuje w Tpay
                                    </span>
                                @else
                                    <span class="badge bg-warning-lt text-warning-emphasis" title="Konto nie zostało jeszcze zgłoszone do białej listy Tpay Collect">
                                        <i class="ti ti-alert-triangle me-1"></i> Niezweryfikowane
                                    </span>
                                @endif
                            @else
                                <span class="badge bg-secondary-lt text-muted">
                                    <i class="ti ti-credit-card-off me-1"></i> Brak numeru konta
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="badge bg-light text-dark" title="Liczba kursów">
                                    <i class="ti ti-books me-1 text-primary"></i>{{ $school->courses_count }} kursów
                                </span>
                                <span class="badge bg-light text-dark" title="Zapisani uczestnicy">
                                    <i class="ti ti-users me-1 text-info"></i>{{ $school->children_count }} dzieci
                                </span>
                            </div>
                        </td>
                        <td>
                            @if($school->status === 'active')
                                <span class="badge bg-success text-white">Aktywna</span>
                            @else
                                <span class="badge bg-danger text-white">Zawieszona</span>
                            @endif
                        </td>
                        <td class="text-end text-nowrap">
                            <form action="{{ route('su.schools.switch', $school) }}" method="POST" class="d-inline" title="Wejdź do panelu tej placówki">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                    <i class="ti ti-login me-1"></i> Wejdź
                                </button>
                            </form>
                            <a href="{{ route('su.schools.edit', $school) }}" class="btn btn-sm btn-secondary ms-1" title="Edytuj dane i weryfikuj Tpay">
                                <i class="ti ti-edit"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="ti ti-building-off fs-1 d-block mb-2"></i>
                            Brak placówek spełniających kryteria wyszukiwania.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($schools->hasPages())
        <div class="card-footer d-flex align-items-center justify-content-between">
            {{ $schools->links() }}
        </div>
    @endif
</div>
@endsection
