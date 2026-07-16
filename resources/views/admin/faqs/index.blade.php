@extends('layouts.admin')

@section('title', 'FAQs')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="text-body-secondary mb-0">Shown on the public FAQ page and in FAQ structured data.</p>
        <a href="{{ route('admin.faqs.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>New FAQ</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" data-reorder-table data-reorder-url="{{ route('admin.reorder', 'faqs') }}">
                <thead><tr><th style="width:90px;">Order</th><th>Question</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                @forelse($faqs as $faq)
                    <tr data-id="{{ $faq->id }}">
                        <td class="text-nowrap">
                            <button class="btn btn-sm btn-light" data-move-up aria-label="Move up"><i class="bi bi-arrow-up"></i></button>
                            <button class="btn btn-sm btn-light" data-move-down aria-label="Move down"><i class="bi bi-arrow-down"></i></button>
                        </td>
                        <td><a class="fw-bold" href="{{ route('admin.faqs.edit', $faq) }}">{{ $faq->question }}</a></td>
                        <td><span class="badge {{ $faq->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $faq->is_active ? 'Active' : 'Hidden' }}</span></td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.faqs.edit', $faq) }}">Edit</a>
                            <form class="d-inline" action="{{ route('admin.faqs.destroy', $faq) }}" method="post" data-confirm="Delete this FAQ?">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-body-secondary py-4">No FAQs yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $faqs->links() }}</div>
@endsection
