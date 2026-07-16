@extends('layouts.admin')

@section('title', 'Users & Roles')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="text-body-secondary mb-0">Super Admins manage settings &amp; users. Managers manage menu/content. Staff handle orders only.</p>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>New User</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                @forelse($users as $user)
                    <tr>
                        <td class="fw-bold">{{ $user->name }} {{ $user->is(auth()->user()) ? '(you)' : '' }}</td>
                        <td>{{ $user->email }}</td>
                        <td><span class="badge text-bg-light border">{{ $user->roleLabel() }}</span></td>
                        <td><span class="badge {{ $user->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $user->is_active ? 'Active' : 'Disabled' }}</span></td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.users.edit', $user) }}">Edit</a>
                            @unless($user->is(auth()->user()))
                                <form class="d-inline" action="{{ route('admin.users.destroy', $user) }}" method="post" data-confirm="Delete user '{{ $user->name }}'?">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                </form>
                            @endunless
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-body-secondary py-4">No users yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $users->links() }}</div>
@endsection
