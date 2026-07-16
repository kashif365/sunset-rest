@extends('layouts.admin')

@section('title', 'Website Pages')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="text-body-secondary mb-0">Static pages like Privacy Policy, Terms &amp; Conditions, About Us content.</p>
        <a href="{{ route('admin.pages.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>New Page</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead><tr><th>Title</th><th>Slug</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                @forelse($pages as $page)
                    <tr>
                        <td><a class="fw-bold" href="{{ route('admin.pages.edit', $page) }}">{{ $page->title }}</a></td>
                        <td class="small text-body-secondary">/p/{{ $page->slug }}</td>
                        <td><span class="badge {{ $page->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $page->is_active ? 'Active' : 'Hidden' }}</span></td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.pages.edit', $page) }}">Edit</a>
                            <form class="d-inline" action="{{ route('admin.pages.destroy', $page) }}" method="post" data-confirm="Delete '{{ $page->title }}'?">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-body-secondary py-4">No pages yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $pages->links() }}</div>
@endsection
