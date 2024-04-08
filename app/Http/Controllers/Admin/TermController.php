<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Term;
use Illuminate\Http\Request;

class TermController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'search' => 'nullable',
            'limit' => 'nullable',
        ]);
        $search = $request->input('search');
        $limit = $request->input('limit') ?? 15;
        $qry = Term::select('*')->orderBy('name');
        if (!empty($search)) {
            $qry->where('name', 'like', '%' . $search . '%');
        }
        $terms = $qry->paginate($limit);
        return view('admin.term.index', compact('terms'));
    }
    public function create()
    {
        return view('admin.term.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:terms|max:250',
        ]);
        $term = Term::query()->create([
            'name' => $request->input('name'),
        ]);
        return redirect()->route('term.index')->with('success', 'Term added Successfully.');
    }
    public function edit($id)
    {
        $term = Term::findOrFail($id);
        return view('admin.term.form', compact('term'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:250|unique:terms,name,' . $id . ',id',
        ]);
        $term = Term::findOrFail($id);
        $term->update([
            'name' => $request->input('name'),
        ]);

        return redirect()->route('term.index')->with('success', 'Term updated Successfully.');
    }
    public function destroy(Request $request, $id)
    {
        $term = Term::findOrFail($id);
        $term->delete();
        return redirect()->route('term.index')->with('delete', 'Term deleted Successfully.');
    }
}
