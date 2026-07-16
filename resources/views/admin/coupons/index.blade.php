@extends('layouts.admin')

@section('title', 'Coupons')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="text-body-secondary mb-0">Applied at checkout using the coupon code.</p>
        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>New Coupon</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead><tr><th>Code</th><th>Discount</th><th>Min Order</th><th>Usage</th><th>Expires</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                @forelse($coupons as $coupon)
                    <tr>
                        <td><a class="fw-bold font-monospace" href="{{ route('admin.coupons.edit', $coupon) }}">{{ $coupon->code }}</a></td>
                        <td>{{ $coupon->type === 'percent' ? $coupon->value.'%' : '$'.number_format((float) $coupon->value, 2) }}</td>
                        <td>{{ $coupon->min_order !== null ? '$'.number_format((float) $coupon->min_order, 2) : '—' }}</td>
                        <td>{{ $coupon->used_count }}{{ $coupon->usage_limit ? ' / '.$coupon->usage_limit : '' }}</td>
                        <td>{{ $coupon->expires_at?->format('M j, Y') ?? 'Never' }}</td>
                        <td><span class="badge {{ $coupon->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $coupon->is_active ? 'Active' : 'Disabled' }}</span></td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.coupons.edit', $coupon) }}">Edit</a>
                            <form class="d-inline" action="{{ route('admin.coupons.destroy', $coupon) }}" method="post" data-confirm="Delete coupon '{{ $coupon->code }}'?">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-body-secondary py-4">No coupons yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $coupons->links() }}</div>
@endsection
