<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Distributor;
use Illuminate\Http\Request;

class DistributorController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'search' => 'nullable',
            'limit' => 'nullable',
        ]);
        $search = $request->input('search');
        $limit = $request->input('limit') ?? 15;
        $qry = Distributor::select('*')->orderBy('name');
        if (!empty($search)) {
            $qry->where('name', 'like', '%' . $search . '%');
        }
        $distributors = $qry->paginate($limit);
        return view('admin.distributor.index', compact('distributors'));
    }
    public function create()
    {
        return view('admin.distributor.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:distributors|max:250',
        ]);
        $distributor = Distributor::query()->create([
            'name' => $request->input('name'),
        ]);
        return redirect()->route('distributor.index')->with('success', 'Distributor added Successfully.');
    }
    public function edit($id)
    {
        $distributor = Distributor::findOrFail($id);
        return view('admin.distributor.form', compact('distributor'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:250|unique:distributors,name,' . $id . ',id',
        ]);
        $distributor = Distributor::findOrFail($id);
        $distributor->update([
            'name' => $request->input('name'),
        ]);

        return redirect()->route('distributor.index')->with('success', 'Distributor updated Successfully.');
    }
    public function destroy(Request $request, $id)
    {
        $distributor = Distributor::findOrFail($id);
        $distributor->delete();
        return redirect()->route('distributor.index')->with('delete', 'Distributor deleted Successfully.');
    }
}
