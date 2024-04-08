<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContractCreateRequest;
use App\Models\Contract;
use App\Models\Distributor;
use App\Models\Manufacturer;
use App\Models\Term;
use App\Models\Type;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContractController extends Controller {
	public function index( Request $request ) {
		$request->validate( [ 
			'search' => 'nullable',
			'limit' => 'nullable',
		] );
		$user = Auth::user();
		$search = $request->input( 'search' );
		$limit = $request->input( 'limit' ) ?? 15;
		$qry = Contract::select( '*' )->latest();
		if ( ! empty( $search ) ) {
			$qry->where( 'number', 'like', '%' . $search . '%' );
		}
		if ( $user->hasRole( 'customer' ) ) {
			$qry->where( 'customer_id', $user->id );
		}
		$contracts = $qry->paginate( $limit );
		return view( 'admin.contract.index', compact( 'contracts' ) );
	}
	public function create() {
		$customers = User::role( 'customer' )->pluck( 'name', 'id' );
		$types = Type::pluck( 'name', 'id' );
		$manufacturers = Manufacturer::pluck( 'name', 'id' );
		$distributors = Distributor::pluck( 'name', 'id' );
		$terms = Term::pluck( 'name', 'id' );
		return view( 'admin.contract.form', compact( 'customers', 'types', 'manufacturers', 'distributors', 'terms' ) );
	}
	public function show( $id ) {
		$contract = Contract::findOrFail( $id );
		return view( 'admin.contract.show', compact( 'contract' ) );
	}
	public function store( ContractCreateRequest $request ) {
		$requestData = $request->validated();
		$requestData['customer_id'] = $request->customer_id;
		$requestData['number'] = rand( 10000, 99999 ) . Contract::count();

		if ( $request->has( 'contract_price' ) && $request->has( 'contract_cost' ) ) {
			$requestData['contract_revenue'] = $request->contract_price - $request->contract_cost;
		}

		$contract = Contract::create( $requestData );

		// $startDate = Carbon::parse($requestData['start_date']);
		// $renewalDate = $startDate->addYear($contract->term->name);
		// $contract->renewal_date= $renewalDate->format('Y-m-d');
		// $contract->save();
		return redirect()->route( 'contract.index' )->with( 'success', 'Contract added Successfully.' );
	}
	public function edit( $id ) {
		$contract = Contract::findOrFail( $id );
		$customers = User::role( 'customer' )->pluck( 'name', 'id' );
		$types = Type::pluck( 'name', 'id' );
		$manufacturers = Manufacturer::pluck( 'name', 'id' );
		$distributors = Distributor::pluck( 'name', 'id' );
		$terms = Term::pluck( 'name', 'id' );
		return view( 'admin.contract.form', compact( 'contract', 'customers', 'types', 'manufacturers', 'distributors', 'terms' ) );
	}
	public function update( ContractCreateRequest $request, $id ) {
		$contract = Contract::findOrFail( $id );
		$requestData = $request->validated();
		$requestData['customer_id'] = $request->customer_id;
		if ( $request->has( 'contract_price' ) && $request->has( 'contract_cost' ) ) {
			$requestData['contract_revenue'] = $request->contract_price - $request->contract_cost;
		}
		$contract->update( $requestData );

		// $startDate = Carbon::parse($requestData['start_date']);
		// $renewalDate = $startDate->addYear($contract->term->name);
		// $contract->renewal_date= $renewalDate->format('Y-m-d');
		// $contract->save();

		return redirect()->route( 'contract.index' )->with( 'success', 'Contract updated Successfully.' );
	}
	public function destroy( Request $request, $id ) {
		$contract = Contract::findOrFail( $id );
		$contract->delete();
		return redirect()->route( 'contract.index' )->with( 'delete', 'Contract deleted Successfully.' );
	}
}
