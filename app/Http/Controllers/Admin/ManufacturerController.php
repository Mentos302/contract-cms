<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Manufacturer;
use Illuminate\Http\Request;

class ManufacturerController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'search' => 'nullable',
            'limit' => 'nullable',
        ]);
        $search = $request->input('search');
        $limit = $request->input('limit') ?? 15;
        $qry = Manufacturer::select('*')->orderBy('name');
        if (!empty($search)) {
            $qry->where('name', 'like', '%' . $search . '%');
        }
        $manufacturers = $qry->paginate($limit);
        return view('admin.manufacturer.index', compact('manufacturers'));
    }
    public function create()
    {
        return view('admin.manufacturer.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:manufacturers|max:250',
        ]);
        $manufacturer = Manufacturer::query()->create([
            'name' => $request->input('name'),
        ]);
        return redirect()->route('manufacturer.index')->with('success', 'Manufacturer added Successfully.');
    }
    public function edit($id)
    {
        $manufacturer = Manufacturer::findOrFail($id);
        return view('admin.manufacturer.form', compact('manufacturer'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:250|unique:manufacturers,name,' . $id . ',id',
        ]);
        $manufacturer = Manufacturer::findOrFail($id);
        $manufacturer->update([
            'name' => $request->input('name'),
        ]);

        return redirect()->route('manufacturer.index')->with('success', 'Manufacturer updated Successfully.');
    }
    public function destroy(Request $request, $id)
    {
        $manufacturer = Manufacturer::findOrFail($id);
        $manufacturer->delete();
        return redirect()->route('manufacturer.index')->with('delete', 'Manufacturer deleted Successfully.');
    }
}
