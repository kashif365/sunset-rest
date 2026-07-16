@extends('layouts.admin')

@section('title', $user->exists ? 'Edit User: '.$user->name : 'New User')

@section('content')
    <form action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}" method="post" class="row g-3" style="max-width: 34rem;">
        @csrf
        @if($user->exists) @method('PUT') @endif

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="name">Name *</label>
                        <input class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required maxlength="120">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="email">Email *</label>
                        <input class="form-control" id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required maxlength="190">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="password">Password {{ $user->exists ? '(leave blank to keep current)' : '*' }}</label>
                        <input class="form-control" id="password" name="password" type="password" @required(! $user->exists) minlength="8" autocomplete="new-password">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="role">Role *</label>
                        <select class="form-select" id="role" name="role" required @disabled($user->exists && $user->is(auth()->user()))>
                            @foreach(\App\Models\User::ROLES as $role)
                                <option value="{{ $role }}" @selected(old('role', $user->role ?? 'staff') === $role)>{{ ucwords(str_replace('_', ' ', $role)) }}</option>
                            @endforeach
                        </select>
                        @if($user->exists && $user->is(auth()->user()))
                            <input type="hidden" name="role" value="{{ $user->role }}">
                            <div class="form-text">You cannot change your own role.</div>
                        @endif
                    </div>
                    <div class="form-check form-switch">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                               @checked(old('is_active', $user->exists ? $user->is_active : true)) @disabled($user->exists && $user->is(auth()->user()))>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>
            </div>
            <button class="btn btn-primary mt-3" type="submit">{{ $user->exists ? 'Save Changes' : 'Create User' }}</button>
            <a class="btn btn-outline-secondary mt-3" href="{{ route('admin.users.index') }}">Cancel</a>
        </div>
    </form>
@endsection
