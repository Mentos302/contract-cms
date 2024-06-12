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
					'value' => $contract->mfr_contract_number ? '#' . $contract->mfr_contract_number . ' - ' . $contract->distributor->name : $contract->distributor->name,
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
		$validatedData = $request->validated();

		$contract = Contract::findOrFail( $validatedData['contract_id'] );

		$year = isset( $validatedData['year'] ) ? $validatedData['year'] : null;

		if ( $year && $contract->renewals()->whereYear( 'expiring_date', $year )->exists() ) {
			return redirect()->back()->with( 'error', 'A renewal for this contract in the specified year already exists.' );
		}

		$quoteFilePath = $request->hasFile( 'quote_file' ) ? $request->file( 'quote_file' )->store( 'quotes', 'public' ) : null;
		$poFilePath = $request->hasFile( 'po_file' ) ? $request->file( 'po_file' )->store( 'pos', 'public' ) : null;
		$invoiceFilePath = $request->hasFile( 'invoice_file' ) ? $request->file( 'invoice_file' )->store( 'invoices', 'public' ) : null;

		$renewalData = [ 
			'contract_id' => $validatedData['contract_id'],
			'quote_number' => $validatedData['quote_number'] ?? null,
			'quote_file' => $quoteFilePath,
			'purchase_order_number' => $validatedData['purchase_order_number'] ?? null,
			'po_file' => $poFilePath,
			'invoice_number' => $validatedData['invoice_number'] ?? null,
			'invoice_file' => $invoiceFilePath,
			'status' => $validatedData['status']
		];

		$renewal = new Renewal( $renewalData );

		$contract = $renewal->contract;

		if ( $contract ) {
			if ( isset( $validatedData['year'] ) ) {
				$contractEndDate = Carbon::parse( $contract->end_date );

				$renewal->expiring_date = $contractEndDate->copy()->year( $validatedData['year'] );
			} else {
				$renewal->expiring_date = $contract->end_date;
			}
		}

		$renewal->save();

		return redirect()->route( 'contract.show', $validatedData['contract_id'] )->with( 'success', 'Contract renewal added successfully.' );
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

		$contractId = $renewal->contract()->first()->id;

		return redirect()->route( 'contract.show', $contractId )->with( 'success', 'Renewal Quote deleted successfully.' );
	}
	public function destroy( Request $request, $id ) {
		$renewal = Renewal::findOrFail( $id );
		$contractId = $renewal->contract()->first()->id;
		$renewal->delete();

		return redirect()->route( 'contract.show', $contractId )->with( 'success', 'Renewal Quote deleted successfully.' );
	}
	public function storeCustomerRenewal( RenewalCreateRequest $request ) {
		$requestData = $request->validated();

		$contractId = $requestData['contract_id'];

		$contract = Contract::findOrFail( $contractId );

		if ( $contract->customer_id === Auth::id() ) {
			Renewal::create( $requestData );

			return response()->json( "Renewal Quoted!" . Auth::id(), 200 );
		} else {
			return response()->json( "You are not authorized to create a renewal for this contract", 403 );
		}
	}


}
