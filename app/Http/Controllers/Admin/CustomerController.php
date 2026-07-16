<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.customers.index', [
            'customers' => Customer::withCount('orders')
                ->withSum(['orders as total_spent' => fn ($q) => $q->where('status', '!=', 'cancelled')], 'total')
                ->when($request->query('q'), fn ($q, $term) => $q->where(function ($query) use ($term) {
                    $query->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%");
                }))
                ->latest()
                ->paginate(30)
                ->withQueryString(),
        ]);
    }

    public function show(Customer $customer)
    {
        $customer->load(['orders' => fn ($q) => $q->latest()->limit(25)]);

        return view('admin.customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('admin.customers.form', compact('customer'));
    }

    public function update(CustomerRequest $request, Customer $customer)
    {
        $customer->update($request->validated());

        return redirect()->route('admin.customers.show', $customer)->with('success', 'Customer updated.');
    }

    public function destroy(Customer $customer)
    {
        abort_unless(auth()->user()->can('manage-content'), 403);

        $customer->delete();

        return redirect()->route('admin.customers.index')->with('success', 'Customer deleted.');
    }
}
