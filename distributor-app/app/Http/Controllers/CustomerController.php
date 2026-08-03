<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Area;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::with(['user', 'area']);

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        if ($request->filled('status')) {
            $query->whereHas('user', fn ($q) => $q->where('status', $request->status));
        }

        $customers = $query->orderBy('company_name')->paginate(20);
        $areas = Area::orderBy('name')->get();

        return inertia('Admin/Customer/Index', [
            'customers' => $customers,
            'areas' => $areas,
            'filters' => [
                'search' => $search ?? '',
                'area_id' => $request->area_id ?? '',
                'status' => $request->status ?? '',
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'area_id' => 'nullable|exists:areas,id',
            'customer_type' => 'nullable|string|max:50',
            'user_id' => 'nullable|exists:users,id',
        ]);

        Customer::create($validated);

        return redirect('/admin/customers')->with('success', 'Customer berhasil ditambahkan.');
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'area_id' => 'nullable|exists:areas,id',
            'customer_type' => 'nullable|string|max:50',
        ]);

        $customer->update($validated);

        return redirect('/admin/customers')->with('success', 'Customer berhasil diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect('/admin/customers')->with('success', 'Customer berhasil dihapus.');
    }
}
