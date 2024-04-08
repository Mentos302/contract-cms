<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Distributor;
use App\Models\Manufacturer;
use App\Models\Term;
use App\Models\Type;
use Illuminate\Http\Request;

class DropdownController extends Controller
{
    public function types()
    {
        $types = Type::orderBy('name')->pluck('name', 'id');
        return response()->json($types, 200);
    }
    public function manufacturers()
    {
        $manufacturers = Manufacturer::orderBy('name')->pluck('name', 'id');
        return response()->json($manufacturers, 200);
    }
    public function distributors()
    {
        $distributors = Distributor::orderBy('name')->pluck('name', 'id');
        return response()->json($distributors, 200);
    }
    public function terms()
    {
        $terms = Term::orderBy('name')->pluck('name', 'id');
        return response()->json($terms, 200);
    }
}
