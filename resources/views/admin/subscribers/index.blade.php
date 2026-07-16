@extends('layouts.admin')

@section('title', 'Email Subscribers')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="text-body-secondary mb-0">Collected from the footer signup form.</p>
        <a href="{{ route('admin.subscribers.export') }}" class="btn btn-outline-secondary"><i class="bi bi-download me-1"></i>Export CSV</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead><tr><th>Email</th><th>Status</th><th>Joined</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                @forelse($subscribers as $subscriber)
                    <tr>
                        <td>{{ $subscriber->email }}</td>
                        <td><span class="badge {{ $subscriber->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $subscriber->is_active ? 'Active' : 'Unsubscribed' }}</span></td>
                        <td>{{ $subscriber->created_at->format('M j, Y') }}</td>
                        <td class="text-end">
                            <form class="d-inline" action="{{ route('admin.subscribers.toggle', $subscriber) }}" method="post">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-outline-secondary" type="submit">{{ $subscriber->is_active ? 'Deactivate' : 'Reactivate' }}</button>
                            </form>
                            <form class="d-inline" action="{{ route('admin.subscribers.destroy', $subscriber) }}" method="post" data-confirm="Remove this subscriber?">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-body-secondary py-4">No subscribers yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $subscribers->links() }}</div>
@endsection
