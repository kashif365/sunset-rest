@extends('layouts.admin')

@section('title', $title)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="text-body-secondary mb-0">Used as filters and badges on the public menu.</p>
        <a href="{{ route('admin.'.$type.'.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>New {{ \Illuminate\Support\Str::singular($title) }}</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead><tr><th>Icon</th><th>Name</th><th>Slug</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                @forelse($records as $record)
                    <tr>
                        <td>@if($record->icon)<i class="bi {{ $record->icon }}"></i>@endif</td>
                        <td><a class="fw-bold" href="{{ route('admin.'.$type.'.edit', $record) }}">{{ $record->name }}</a></td>
                        <td class="small text-body-secondary">{{ $record->slug }}</td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.'.$type.'.edit', $record) }}">Edit</a>
                            <form class="d-inline" action="{{ route('admin.'.$type.'.destroy', $record) }}" method="post" data-confirm="Delete '{{ $record->name }}'?">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-body-secondary py-4">Nothing here yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $records->links() }}</div>
@endsection
