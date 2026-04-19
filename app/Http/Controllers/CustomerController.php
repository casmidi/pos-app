<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->string('q'));
        $allowedSorts = [
            'id' => 'id',
            'name' => 'name',
            'phone' => 'phone',
            'email' => 'email',
        ];
        $sort = $request->string('sort')->toString();
        $direction = strtolower($request->string('direction')->toString()) === 'asc' ? 'asc' : 'desc';

        if (! array_key_exists($sort, $allowedSorts)) {
            $sort = 'created_at';
            $direction = 'desc';
        }

        $customers = Customer::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($nestedQuery) use ($search) {
                    $nestedQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();

        return $this->respond($request, 'customers.index', compact('customers', 'search', 'sort', 'direction'), $customers);
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:100'],
            'address' => ['nullable', 'string'],
        ]);

        $customer = Customer::query()->create($validated);

        return $this->respondAfterMutation(
            $request,
            'customers.index',
            'Pelanggan berhasil ditambahkan.',
            $customer,
            201,
        );
    }

    public function show(Request $request, Customer $customer)
    {
        return $this->respond($request, 'customers.show', compact('customer'), $customer);
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:100'],
            'address' => ['nullable', 'string'],
        ]);

        $customer->update($validated);

        return $this->respondAfterMutation(
            $request,
            'customers.index',
            'Pelanggan berhasil diperbarui.',
            $customer->fresh(),
        );
    }

    public function destroy(Request $request, Customer $customer)
    {
        $customer->delete();

        return $this->respondAfterMutation(
            $request,
            'customers.index',
            'Pelanggan berhasil dihapus.',
            ['message' => 'Customer deleted'],
        );
    }
}
