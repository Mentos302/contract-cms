<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContractCreateRequest;
use App\Http\Requests\RenewalCreateRequest;
use App\Models\Contract;
use App\Models\Renewal;
use App\Models\Distributor;
use App\Models\Manufacturer;
use App\Models\Term;
use App\Models\Type;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RenewalController extends Controller {
	public function index( Request $request ) {
		$request->validate( [ 
			'search' => 'nullable',
			'limit' => 'nullable',
		] );
		$user = Auth::user();
		$search = $request->input( 'search' );
		$limit = $request->input( 'limit' ) ?? 15;
		$qry = Renewal::select( '*' )->latest();
		if ( ! empty( $search ) ) {
			$qry->where( 'number', 'like', '%' . $search . '%' );
		}
		if ( $user->hasRole( 'customer' ) ) {
			$qry->where( 'customer_id', $user->id );
		}
		$renewals = $qry->paginate( $limit );

		return view( 'admin.renewal.index', compact( 'renewals' ) );
	}
	public function create() {
		$contracts = Contract::whereDoesntHave( 'renewals' )
			->with( 'distributor' )
			->get()
			->map( function ($contract) {
				return [ 
					'id' => $contract->id,
					'value' => $contract->distributor ? $contract->number . ' - ' . $contract->distributor->name : $contract->number,
				];
			} )
			->pluck( 'value', 'id' );



		return view( 'admin.renewal.form', compact( 'contracts' ) );
	}

	public function show( $id ) {
		$renewal = Renewal::findOrFail( $id );

		$contract = Contract::findOrFail( $renewal->contract_id );

		$selectedContract = [ 
			'id' => $contract->id,
			'value' => $contract->distributor ? $contract->distributor->name . ' - ' . $contract->number : $contract->number,
		];

		return view( 'admin.renewal.show', compact( 'selectedContract', 'renewal' ) );
	}
	public function store( RenewalCreateRequest $request ) {
		$requestData = $request->validated();

		Renewal::create( $requestData );

		return redirect()->route( 'renewal.index' )->with( 'success', 'Renewal added Successfully.' );
	}
	public function edit( $id ) {
		$renewal = Renewal::findOrFail( $id );

		$contract = Contract::findOrFail( $renewal->contract_id );

		$selectedContract = [ 
			'id' => $contract->id,
			'value' => $contract->distributor ? $contract->distributor->name . ' - ' . $contract->number : $contract->number,
		];

		return view( 'admin.renewal.form', compact( 'selectedContract', 'renewal' ) );
	}
	public function update( RenewalCreateRequest $request, $id ) {
		$renewal = Renewal::findOrFail( $id );
		$requestData = $request->validated();

		$renewal->update( $requestData );

		return redirect()->route( 'renewal.index' )->with( 'success', 'Contract updated Successfully.' );
	}
	public function destroy( Request $request, $id ) {
		$renewal = Renewal::findOrFail( $id );
		$renewal->delete();

		return redirect()->route( 'renewal.index' )->with( 'delete', 'Contract deleted Successfully.' );
	}

}
