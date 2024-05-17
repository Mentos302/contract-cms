<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContractCreateRequest;
use App\Models\Contract;
use App\Models\Distributor;
use App\Models\Manufacturer;
use App\Models\Renewal;
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

		if ( ! $user->hasRole( 'admin' ) ) {
			$qry->where( 'customer_id', $user->id );
		}

		$contracts = $qry->paginate( $limit );

		return view( 'admin.contract.index', compact( 'contracts' ) );
	}

	public function create() {
		$customers = User::role( 'customer' )->get()->map( function ($customer) {
			$name = $customer->first_name && $customer->last_name ?
				$customer->first_name . ' ' . $customer->last_name . ' (' . $customer->email . ')' :
				$customer->email;

			return [ 
				'id' => $customer->id,
				'name' => $name,
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

		$term = Term::findOrFail( $requestData['term_id'] );

		$startDate = Carbon::parse( $requestData['start_date'] );
		$endDate = $startDate->copy()->addYears( $term->name );
		$requestData['end_date'] = $endDate->toDateString();

		Contract::create( $requestData );

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
		$requestData['serial_number'] = $request->serial_number;
		$requestData['mfr_contract_number'] = $request->mfr_contract_number;
		$requestData['name'] = $request->name;

		$term = Term::findOrFail( $requestData['term_id'] );
		$startDate = Carbon::parse( $requestData['start_date'] );
		$endDate = $startDate->copy()->addYears( $term->name );
		$requestData['end_date'] = $endDate->toDateString();

		$contract->update( $requestData );

		return redirect()->route( 'contract.index' )->with( 'success', 'Contract updated successfully.' );
	}

	public function updateStatus( Request $request, $id ) {
		$request->validate( [ 
			'renewal_option' => 'required|in:quote,not,another',
		] );

		if ( $request->renewal_option === 'quote' ) {
			Renewal::create( [ 
				'contract_id' => $id,
				'status' => 'quote',
			] );
		}

		$contract = Contract::findOrFail( $id );
		$contract->status = $request->renewal_option;
		$contract->save();

		return redirect()->route( 'contract.show', $id )->with( 'success', 'Contract status updated successfully!' );
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

				$term = Term::where( 'name', $row[3] )->first();
				if ( ! $term ) {
					$errorRows[] = 'Row ' . $rowNumber . ': The specified term is not supported: ' . $row[3];
					continue;
				}

				$startDate = Carbon::createFromFormat( 'm/d/Y', $row[2] );
				$endDate = $startDate->copy()->addYears( $term->name );

				Contract::create( [ 
					'customer_id' => auth()->user()->id,
					'type_id' => $type->id,
					'manufacturer_id' => $manufacturer->id,
					'term_id' => $term->id,
					'start_date' => $startDate->toDateString(),
					'end_date' => $endDate->toDateString(),
					'location' => $row[4],
					'contract_price' => $row[5],
					'contract_owner' => $row[6] === "Sivility Systems" ? $row[6] : "Other Partner",
					'serial_number' => $row[7],
					'mfr_contract_number' => $row[8],
					'name' => $row[9] ?? null,
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

