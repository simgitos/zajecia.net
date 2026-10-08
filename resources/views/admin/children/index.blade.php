@extends('layouts.app')

@section('content')
<div class="page-header d-print-none mb-4">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title text-primary fw-bold">
                    <i class="ti ti-users me-2"></i> Uczestnicy
                </h2>
                <div class="text-secondary mt-1">
                    Lista wszystkich zarejestrowanych uczestników oraz ich zapisów na zajęcia.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-surface d-flex justify-content-between align-items-center">
                <h3 class="card-title fw-bold">Wszyscy uczestnicy ({{ $children->total() }})</h3>
                <form action="{{ route('admin.children.index') }}" method="GET" class="d-flex gap-2">
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="ti ti-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control" placeholder="Szukaj uczestnika lub opiekuna..." value="{{ request('search') }}">
                    </div>
                    <button type="submit" class="btn btn-secondary">Szukaj</button>
                    @if(request('search'))
                        <a href="{{ route('admin.children.index') }}" class="btn btn-outline-secondary">Wyczyść</a>
                    @endif
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-vcenter card-table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Imię i nazwisko uczestnika</th>
                            <th>Data ur. (Wiek)</th>
                            <th>Numer PESEL</th>
                            <th>Opiekun</th>
                            <th>Zapisane zajęcia</th>
                            <th>Uwagi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($children as $index => $child)
                            <tr>
                                <td>{{ $children->firstItem() + $index }}</td>
                                <td>
                                    <strong class="text-dark fs-3">{{ $child->name }}</strong>
                                </td>
                                <td>
                                    {{ $child->birth_date ? $child->birth_date->format('d.m.Y') : '-' }}
                                    <small class="text-muted d-block">({{ $child->age }} lat)</small>
                                </td>
                                <td>
                                    @if($child->pesel)
                                        <code class="text-dark">{{ $child->pesel }}</code>
                                    @else
                                        <span class="text-muted">Brak</span>
                                    @endif
                                </td>
                                <td>
                                    @if($child->parent)
                                        <div class="fw-bold">{{ $child->parent->name }}</div>
                                        <small class="text-muted">{{ $child->parent->email }}</small>
                                    @else
                                        <span class="text-muted">Brak powiązanego konta</span>
                                    @endif
                                </td>
                                <td>
                                    @if($child->courses->isEmpty())
                                        <span class="badge bg-secondary-lt">Brak zapisów</span>
                                    @else
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($child->courses as $c)
                                                <a href="{{ route('admin.courses.show', $c->id) }}" class="badge bg-blue-lt text-decoration-none">
                                                    {{ $c->title }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($child->parent_comment)
                                        <span class="badge bg-warning-lt text-wrap" style="max-width: 180px;">
                                            {{ $child->parent_comment }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="ti ti-mood-kid-off fs-1 d-block mb-2 text-secondary"></i>
                                    Brak zarejestrowanych dzieci spełniających kryteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($children->hasPages())
                <div class="card-footer d-flex align-items-center">
                    {{ $children->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
