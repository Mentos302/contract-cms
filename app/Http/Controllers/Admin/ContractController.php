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
		$customers = User::role( 'customer' )->get()->map( function ($customer) {
			return [ 
				'id' => $customer->id,
				'name' => $customer->first_name . ' ' . $customer->last_name
			];
		} )->pluck( 'name', 'id' );

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
		$customers = User::role( 'customer' )->get()->map( function ($customer) {
			return [ 
				'id' => $customer->id,
				'name' => $customer->first_name . ' ' . $customer->last_name
			];
		} )->pluck( 'name', 'id' );
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

		$requestData['serial_number'] = $request->serial_number;
		$requestData['mfr_contract_number'] = $request->mfr_contract_number;
		$requestData['name'] = $request->name;
		$contract->update( $requestData );

		// $startDate = Carbon::parse($requestData['start_date']);
		// $renewalDate = $startDate->addYear($contract->term->name);
		// $contract->renewal_date= $renewalDate->format('Y-m-d');
		// $contract->save();

		return redirect()->route( 'contract.index' )->with( 'success', 'Contract updated successfully.' );
	}
	public function destroy( Request $request, $id ) {
		$contract = Contract::findOrFail( $id );
		$contract->delete();
		return redirect()->route( 'contract.index' )->with( 'delete', 'Contract deleted successfully.' );
	}

	public function contractsImport( Request $request ) {
		$this->validate( $request, [ 
			'csv_file' => 'required|mimes:csv,txt|max:10240',
		] );

		if ( $request->hasFile( 'csv_file' ) ) {
			$file = $request->file( 'csv_file' );

			$csvData = array_map( 'str_getcsv', file( $file ) );

			$errorRows = [];

			$rowNumber = 0;

			foreach ( $csvData as $row ) {
				$rowNumber++;

				if ( $rowNumber === 1 ) {
					continue;
				}

				$type = Type::where( 'name', $row[0] )->first();
				if ( ! $type ) {
					$type = Type::firstOrCreate( [ 'name' => 'Contract' ] );
				}

				$manufacturer = Manufacturer::where( 'name', $row[1] )->first();
				if ( ! $manufacturer ) {
					$errorRows[] = 'Row ' . $rowNumber . ': The specified manufacturer is not supported: ' . $row[1];
					continue;
				}

				// Find the term
				$term = Term::where( 'name', $row[2] )->first();
				if ( ! $term ) {
					$errorRows[] = 'Row ' . $rowNumber . ': The specified term is not supported: ' . $row[2];
					continue;
				}

				Contract::create( [ 
					'customer_id' => auth()->user()->id,
					'type_id' => $type->id,
					'manufacturer_id' => $manufacturer->id,
					'term_id' => $term->id,
					'start_date' => Carbon::createFromFormat( 'm/d/Y', $row[3] )->toDateString(),
					'end_date' => Carbon::createFromFormat( 'm/d/Y', $row[4] )->toDateString(),
					'location' => $row[5],
					'contract_price' => $row[6],
					'contract_owner' => $row[7] === "Sivility Systems" ? $row[7] : "Other Partner",
					'serial_number' => $row[8],
					'mfr_contract_number' => $row[9],
					'name' => $row[10] ?? null,
				] );
			}

			if ( ! empty( $errorRows ) ) {
				return redirect( '/contract' )->with( 'errors', $errorRows );
			}

			return redirect( '/contract' )->with( 'success', 'Contracts imported successfully' );
		}

		return redirect( '/contract' )->with( 'error', 'CSV file upload failed' );
	}

}
