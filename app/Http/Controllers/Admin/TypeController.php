<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Type;
use Illuminate\Http\Request;

class TypeController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'search' => 'nullable',
            'limit' => 'nullable',
        ]);
        $search = $request->input('search');
        $limit = $request->input('limit') ?? 15;
        $qry = Type::select('*')->orderBy('name');
        if (!empty($search)) {
            $qry->where('name', 'like', '%' . $search . '%');
        }
        $types = $qry->paginate($limit);
        return view('admin.type.index', compact('types'));
    }
    public function create()
    {
        return view('admin.type.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:types|max:250',
        ]);
        $type = Type::query()->create([
            'name' => $request->input('name'),
        ]);
        return redirect()->route('type.index')->with('success', 'Type added Successfully.');
    }
    public function edit($id)
    {
        $type = Type::findOrFail($id);
        return view('admin.type.form', compact('type'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:250|unique:types,name,' . $id . ',id',
        ]);
        $type = Type::findOrFail($id);
        $type->update([
            'name' => $request->input('name'),
        ]);

        return redirect()->route('type.index')->with('success', 'Type updated Successfully.');
    }
    public function destroy(Request $request, $id)
    {
        $type = Type::findOrFail($id);
        $type->delete();
        return redirect()->route('type.index')->with('delete', 'Type deleted Successfully.');
    }
}
