@extends('layouts.admin')

@section('title', 'Customers')

@section('content')
    <form class="row g-2 mb-3" method="get">
        <div class="col-auto">
            <label class="visually-hidden" for="filter-q">Search</label>
            <input id="filter-q" class="form-control" type="search" name="q" value="{{ request('q') }}" placeholder="Name, email or phone…">
        </div>
        <div class="col-auto"><button class="btn btn-outline-secondary" type="submit">Search</button></div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Orders</th><th>Total Spent</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                @forelse($customers as $customer)
                    <tr>
                        <td><a class="fw-bold" href="{{ route('admin.customers.show', $customer) }}">{{ $customer->name }}</a></td>
                        <td>{{ $customer->email }}</td>
                        <td>{{ $customer->phone }}</td>
                        <td>{{ $customer->orders_count }}</td>
                        <td>${{ number_format((float) ($customer->total_spent ?? 0), 2) }}</td>
                        <td class="text-end"><a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.customers.show', $customer) }}">View</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-body-secondary py-4">No customers yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $customers->links() }}</div>
@endsection
