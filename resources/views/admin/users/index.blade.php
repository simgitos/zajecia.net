@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title"><i class="ti ti-users me-2"></i> Lista użytkowników</h3>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show m-3 mb-0" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nazwa</th>
                    <th>Kontakt</th>
                    <th>Rola</th>
                    <th class="w-1">Akcje</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }} <br>{{ $user->phone }}</td>
                        <td>
                            @php
                                $userSchoolRoles = $user->rolesForSchool(Auth::user()->school_id);
                            @endphp
                            @if($userSchoolRoles->isNotEmpty())
                                @foreach($userSchoolRoles as $role)
                                    <span class="badge bg-{{ $role->color() }}-lt me-1">
                                        <i class="ti {{ $role->icon() }} me-1"></i>{{ $role->label() }}
                                    </span>
                                @endforeach
                            @else
                                <span class="badge bg-secondary-lt">Brak</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-list flex-nowrap">
                                <a href="{{ route('admin.user.edit', $user->id) }}" class="btn btn-primary btn-sm">
                                    <i class="ti ti-edit me-1"></i> Edytuj
                                </a>
                                <form action="{{ route('admin.user.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Czy na pewno chcesz usunąć tego użytkownika?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="ti ti-trash me-1"></i> Usuń
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Brak użytkowników</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
        <div class="card-footer d-flex align-items-center">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection