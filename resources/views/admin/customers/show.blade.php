@extends('layouts.admin')

@section('title', $customer->name)

@section('content')
    <div class="row g-3">
        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-header bg-white"><strong>Edit Customer</strong></div>
                <div class="card-body">
                    <form action="{{ route('admin.customers.update', $customer) }}" method="post">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label class="form-label" for="name">Name</label>
                            <input class="form-control" id="name" name="name" value="{{ old('name', $customer->name) }}" required maxlength="120">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="email">Email</label>
                            <input class="form-control" id="email" name="email" type="email" value="{{ old('email', $customer->email) }}" required maxlength="190">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="phone">Phone</label>
                            <input class="form-control" id="phone" name="phone" value="{{ old('phone', $customer->phone) }}" maxlength="30">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="notes">Internal notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" maxlength="2000">{{ old('notes', $customer->notes) }}</textarea>
                        </div>
                        <button class="btn btn-primary w-100" type="submit">Save</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header bg-white"><strong>Order History</strong></div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead><tr><th>Order</th><th>Date</th><th>Total</th><th>Status</th></tr></thead>
                        <tbody>
                        @forelse($customer->orders as $order)
                            <tr>
                                <td><a href="{{ route('admin.orders.show', $order) }}">{{ $order->order_number }}</a></td>
                                <td>{{ $order->created_at->format('M j, Y') }}</td>
                                <td>${{ number_format((float) $order->total, 2) }}</td>
                                <td><span class="badge badge-status {{ $order->statusBadgeClass() }}">{{ ucfirst($order->status) }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-body-secondary py-4">No orders yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
