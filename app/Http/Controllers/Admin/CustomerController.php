<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'search' => 'nullable',
            'limit' => 'nullable',
        ]);
        $search = $request->input('search');
        $limit = $request->input('limit') ?? 15;
        $qry = User::select('*')->role('customer')->latest();
        if (!empty($search)) {
            $qry->where('name', 'like', '%' . $search . '%');
        }
        $customers = $qry->paginate($limit);
        return view('admin.customer.index', compact('customers'));
    }
    public function create()
    {
        return view('admin.customer.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $requestData = $request->except(['_token', 'method']);
        $requestData['password'] = Hash::make($request->password);
        $customer = User::create($requestData);
        $customer->assignRole('customer');
        return redirect()->route('customer.index')->with('success', 'Customer added Successfully.');
    }
    public function edit($id)
    {
        $customer = User::findOrFail($id);
        return view('admin.customer.form', compact('customer'));
    }
    public function update(Request $request, $id)
    {
        $customer = User::findOrFail($id);
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255', 'email', Rule::unique('users')->ignore($customer->id)],
        ]);
        $requestData = $request->except(['_token', 'method']);
        if ($request->password) {
            $request->validate([
                'password' => ['string', 'min:8', 'confirmed'],
            ]);
            $requestData['password'] = Hash::make($request->password);
        }else{
            unset($requestData['password']);
        }
        $customer->update($requestData);
        return redirect()->route('customer.index')->with('success', 'Customer updated Successfully.');
    }
    public function destroy(Request $request, $id)
    {
        $customer = User::findOrFail($id);
        $customer->delete();
        return redirect()->route('customer.index')->with('delete', 'Customer deleted Successfully.');
    }
}
