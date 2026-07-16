@extends('layouts.admin')

@section('title', 'Modifier Groups')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="text-body-secondary mb-0">Groups like "Bagel Type", "Cream Cheese", "Meat Choice" — attach them to menu items.</p>
        <a href="{{ route('admin.modifier-groups.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>New Group</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead><tr><th>Name</th><th>Type</th><th>Required</th><th>Options</th><th>Used By</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                @forelse($groups as $group)
                    <tr>
                        <td><a class="fw-bold" href="{{ route('admin.modifier-groups.edit', $group) }}">{{ $group->name }}</a></td>
                        <td><span class="text-capitalize">{{ $group->selection_type }}</span></td>
                        <td>@if($group->is_required)<span class="badge text-bg-danger">Required</span>@else<span class="badge text-bg-light border">Optional</span>@endif</td>
                        <td>{{ $group->options_count }}</td>
                        <td>{{ $group->menu_items_count }} item(s)</td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.modifier-groups.edit', $group) }}">Edit</a>
                            <form class="d-inline" action="{{ route('admin.modifier-groups.destroy', $group) }}" method="post" data-confirm="Delete '{{ $group->name }}' and all its options?">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-body-secondary py-4">No modifier groups yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $groups->links() }}</div>
@endsection
