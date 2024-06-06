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
use App\Models\ContractHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContractAddedNotification;
use App\Mail\SupportTicketNotification;

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

		$contract = Contract::create( $requestData );

		$manufacturer = $contract->manufacturer;
		$contractNumber = $contract->mfr_contract_number;

		$customer = User::find( $contract->customer_id );

		if ( $customer ) {
			Mail::to( $customer->email )->send( new ContractAddedNotification( $contract, $manufacturer->name, $contractNumber ) );
		}

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

	public function createSupportTicket( Request $request ) {
		$validatedData = $request->validate( [ 
			'subject' => 'required|string|max:255',
			'message' => 'required|string|max:1000',
			'contract_name' => 'required|string|max:255',
			'contract_id' => 'required|exists:contracts,id',
		] );

		$ticket = array(
			'subject' => $validatedData['subject'],
			'message' => $validatedData['message'],
			'contract_name' => $validatedData['contract_name'],
			'contract_id' => $validatedData['contract_id'],
		);

		Mail::to( config( 'mail.support_email' ) )->send( new SupportTicketNotification( $ticket ) );

		return redirect()->route( 'contract.show', $validatedData['contract_id'] )->with( 'success', 'Support ticket created successfully!' );
	}

	public function addHistory( Request $request, $contractId ) {
		$request->validate( [ 
			'year' => [ 
				'required',
				'integer',
				function ($attribute, $value, $fail) use ($contractId) {
					$exists = ContractHistory::where( 'contract_id', $contractId )
						->where( 'year', $value )
						->exists();
					if ( $exists ) {
						$fail( 'The year has already been taken for this contract.' );
					}
				},
			],
			'quote_number' => 'required|string',
			'quote_file' => 'required|file|mimes:pdf',
			'purchase_order_number' => 'required|integer',
			'po_file' => 'required|file|mimes:pdf',
			'invoice_number' => 'required|integer',
		] );

		$contract = Contract::findOrFail( $contractId );

		$quoteFilePath = $request->file( 'quote_file' )->store( 'quotes', 'public' );
		$poFilePath = $request->file( 'po_file' )->store( 'pos', 'public' );

		$contract->histories()->create( [ 
			'year' => $request->year,
			'quote_number' => $request->quote_number,
			'quote_file' => $quoteFilePath,
			'purchase_order_number' => $request->purchase_order_number,
			'po_file' => $poFilePath,
			'invoice_number' => $request->invoice_number,
		] );

		return redirect()->route( 'contract.show', $contractId )->with( 'success', 'Contract history added successfully.' );
	}

	public function deleteHistory( $contractId, $historyId ) {
		$contract = Contract::findOrFail( $contractId );
		$history = $contract->histories()->findOrFail( $historyId );

		$history->delete();

		return redirect()->route( 'contract.show', $contractId )->with( 'success', 'Contract history deleted successfully.' );
	}

}

