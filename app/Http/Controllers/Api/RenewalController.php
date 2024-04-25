<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RenewalCreateRequest;
use App\Http\Resources\ContractDetailResource;
use App\Http\Resources\ContractResource;
use App\Http\Resources\RenewalResource;
use App\Models\Renewal;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RenewalController extends Controller {
	public function index( Request $request ) {
		$validate = $request->validate( [ 
			'search' => 'nullable',
			'limit' => 'nullable',
			'upcoming_renewals' => 'nullable',
			'expired_contracts' => 'nullable',
		] );

		$user = auth()->user();
		$search = $request->get( 'search' );
		$upcoming_renewals = $request->get( 'upcoming_renewals' );
		$expired_contracts = $request->get( 'expired_contracts' );
		$limit = $request->get( 'limit' ) ?? 15;
		$qry = Contract::where( 'customer_id', $user->id )->latest();
		if ( ! empty( $search ) ) {
			$qry->where( 'number', 'LIKE', '%' . $search . '%' );
			$qry->orWhere( 'start_date', 'LIKE', '%' . $search . '%' );
		}
		if ( $upcoming_renewals ) {
			$qry->where( 'end_date', '>', Carbon::now() );
		}
		if ( $expired_contracts ) {
			$qry->where( 'end_date', '<', Carbon::now() );
		}
		if ( $limit == 'all' ) {
			$contract = $qry->get();
		} else {
			$contract = $qry->paginate( $limit );
		}
		$rows = ContractResource::collection( $contract );
		return $rows;
	}

	public function store( RenewalCreateRequest $request ) {
		$req_data = $request->validated();

		$contract = Renewal::create( $req_data );

		$row = new RenewalResource( $contract );

		return response()->json( $row, 200 );
	}

	public function show( $id ) {
		$contract = Contract::findOrFail( $id );
		$row = new ContractDetailResource( $contract );
		return response()->json( $row, 200 );
	}
	public function update( RenewalCreateRequest $request, $id ) {
		$req_data = $request->validated();
		$user = auth()->user();
		$req_data['customer_id'] = $user->id;
		$contract = Contract::findOrfail( $id );
		$contract->update( $req_data );

		// $startDate = Carbon::parse($req_data['start_date']);
		// $renewalDate = $startDate->addYear($contract->term->name);
		// $contract->renewal_date= $renewalDate->format('Y-m-d');
		// $contract->save();

		$row = new RenewalResource( $contract );

		return response()->json( $row, 200 );
	}
	public function destroy( $id ) {
		$contract = Contract::findOrFail( $id );
		$contract->delete();
		return response()->json( [], 204 );
	}

	public function breakContractsDownByMonth( Request $request ) {
		$validate = $request->validate( [ 
			'start_date' => 'required',
			'end_date' => 'required',
		] );

		$startDate = $request->start_date;
		$endDate = $request->end_date;

		$contracts = Contract::where( 'customer_id', auth()->user()->id )->where( function ($query) use ($startDate, $endDate) {
			$query->where( 'start_date', '<=', $endDate )
				->where( 'end_date', '>=', $startDate );
		} )->get();

		$monthlyTotals = [];

		foreach ( $contracts as $contract ) {
			// Determine the months that the contract overlaps with
			$start = max( $contract->start_date, $startDate );
			$end = min( $contract->end_date, $endDate );

			// Calculate the total value for each month
			$currentMonth = Carbon::parse( $start );
			$endMonth = Carbon::parse( $end );

			while ( $currentMonth->lessThanOrEqualTo( $endMonth ) ) {
				$month = $currentMonth->format( 'Y-m' );
				$monthlyTotals[ $month ] = isset( $monthlyTotals[ $month ] ) ? $monthlyTotals[ $month ] + $contract->contract_price : $contract->contract_price;
				$currentMonth->addMonth();
			}
		}

		return $monthlyTotals;
	}

	public function upcomingRenewalRevenue( Request $request ) {
		$validate = $request->validate( [ 
			'start_date' => 'required',
			'end_date' => 'required',
		] );

		$startDate = $request->start_date;
		$endDate = $request->end_date;
		// Query contracts set to renew within the specified date range
		$renewableContracts = Contract::where( 'customer_id', auth()->user()->id )->whereBetween( 'end_date', [ $startDate, $endDate ] )->get();

		// Calculate revenue for upcoming year, quarter, and month, considering contract term
		$annualRevenue = 0;
		$quarterlyRevenue = 0;
		$monthlyRevenue = 0;

		foreach ( $renewableContracts as $contract ) {
			$contractTerm = $contract->term->name; // Get contract term in years

			// Calculate revenue based on contract term
			$annualRevenue += $contract->contract_price * $contractTerm;
			$quarterlyRevenue += $contract->contract_price * $contractTerm * 4;
			$monthlyRevenue += $contract->contract_price * $contractTerm * 12;
		}

		return [ 
			'annual' => $annualRevenue,
			'quarterly' => $quarterlyRevenue,
			'monthly' => $monthlyRevenue,
		];
	}
	public function lostContracts( Request $request ) {
		$validate = $request->validate( [ 
			'start_date' => 'required',
			'end_date' => 'required',
		] );

		$startDate = $request->start_date;
		$endDate = $request->end_date;
		$lostContracts = Contract::where( 'customer_id', auth()->user()->id )->where( 'end_date', '>=', $startDate )
			->where( 'end_date', '<=', $endDate )
			->get();

		// Calculate total value of lost contracts
		$lostRevenue = $lostContracts->sum( 'contract_price' );

		return $lostRevenue;
	}

	public function totalContractBaseValue() {
		$existingContracts = Contract::where( 'customer_id', auth()->user()->id )->whereNull( 'end_date' )->get();
		$renewableContracts = Contract::where( 'customer_id', auth()->user()->id )->whereNotNull( 'end_date' )->get();

		// Calculate total value of all contracts
		$totalValue = $existingContracts->sum( 'contract_price' ) + $renewableContracts->sum( 'contract_price' );

		return $totalValue;
	}



}
