<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Renewal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller {


	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Contracts\Support\Renderable
	 */

	/*Language Translation*/
	public function lang( $locale ) {
		if ( $locale ) {
			App::setLocale( $locale );
			Session::put( 'lang', $locale );
			Session::save();
			return redirect()->back()->with( 'locale', $locale );
		} else {
			return redirect()->back();
		}
	}

	public function index() {
		$user = auth()->user();
		$qry = Contract::query();
		if ( $user->hasRole( 'customer' ) ) {
			$qry->where( 'customer_id', $user->id );
		}
		$active_contracts = $this->getActiveContarcts();
		$expiring_soon_contracts = $this->getExpiringSoonContracts();
		$expired_contracts = $this->getExpiredContracts();
		$not_renewing_contracts = $this->notRenewingContracts();

		$profit_revenue = $this->calculateYearlyProfitRevenue();
		$totalCustomer = 0;
		if ( $user->hasRole( 'admin' ) ) {
			$totalCustomer = User::role( 'customer' )->count();
		}
		$graph_by_manufacturer = $this->contractGraphByManufacturer();
		$nearest_contarcts = $this->nearestContract();
		list( $sumOpenContracts, $countOpenContracts ) = $this->openRevenue();
		$open_revenue['sumOpenContracts'] = $sumOpenContracts;
		$open_revenue['countOpenContracts'] = $countOpenContracts;

		list( $sumCloseWonContracts, $countCloseWonContracts ) = $this->closeWonRevenue();
		$close_won_revenue['sumCloseWonContracts'] = $sumCloseWonContracts;
		$close_won_revenue['countCloseWonContracts'] = $countCloseWonContracts;

		list( $sumCloseLostContracts, $countCloseLostContracts ) = $this->closeLostRevenue();
		$close_lost_revenue['sumCloseLostContracts'] = $sumCloseLostContracts;
		$close_lost_revenue['countCloseLostContracts'] = $countCloseLostContracts;

		list( $totalContractRevenue, $minContractRevenue, $renewalContractsCount ) = $this->revenueCount();
		$revenue_and_count['totalContractRevenue'] = $totalContractRevenue;
		$revenue_and_count['minContractRevenue'] = $minContractRevenue;
		$revenue_and_count['renewalContractsCount'] = $renewalContractsCount;

		return view( 'home', compact(
			'graph_by_manufacturer',
			'active_contracts',
			'expiring_soon_contracts',
			'expired_contracts',
			'not_renewing_contracts',
			'totalCustomer',
			'profit_revenue',
			'nearest_contarcts',
			'open_revenue',
			'close_won_revenue',
			'close_lost_revenue',
			'revenue_and_count',
		) );
	}
	public function getActiveContarcts() {
		$user = auth()->user();
		$qry = Contract::query();

		if ( $user->hasRole( 'customer' ) ) {
			$qry->where( 'customer_id', $user->id );
		}

		return $qry->where( 'start_date', '<=', now() )
			->where( 'end_date', '>=', now() )->get();
	}
	public function getExpiringSoonContracts() {
		$user = auth()->user();
		$qry = Contract::query();
		if ( $user->hasRole( 'customer' ) ) {
			$qry->where( 'customer_id', $user->id );
		}
		return $qry->where( 'end_date', '>=', now() )
			->where( 'end_date', '<=', now()->addDays( settings( 'expiration_days' ) ) )
			->get();
	}
	public function getExpiredContracts() {
		$user = auth()->user();
		$qry = Contract::query();
		if ( $user->hasRole( 'customer' ) ) {
			$qry->where( 'customer_id', $user->id );
		}

		return $qry->where( 'end_date', '<', now() )
			->get();
	}
	public function notRenewingContracts() {
		$user = auth()->user();
		$qry = Contract::query();

		if ( $user->hasRole( 'customer' ) ) {
			$qry->where( 'customer_id', $user->id );
		}

		return $qry
			->where( function ($query) {
				$query->whereHas( 'renewals', function ($q) {
					$q->where( 'status', 'Close lost' );
				} )->orWhere( function ($query) {
					$query->doesntHave( 'renewals' )
						->whereDate( 'end_date', '<', now()->subDays( 15 ) );
				} );
			} )

			->get();
	}
	public function contractsStatus( Request $request ) {
		$contracts_status = [];
		if ( $request->status == 'active' ) {
			$contracts_status = $this->getActiveContarcts();
		}
		if ( $request->status == 'expired' ) {
			$contracts_status = $this->getExpiredContracts();
		}
		if ( $request->status == 'expiring-soon' ) {
			$contracts_status = $this->getExpiringSoonContracts();
		}
		return view( 'admin.contract.contract-status', compact( 'contracts_status' ) );
	}


	public function contractGraphByManufacturer() {
		$contractsByManufacturer = Contract::select( 'manufacturer_id', DB::raw( 'COUNT(*) as total, sum(contract_revenue) as revenue' ) )
			->groupBy( 'manufacturer_id' )
			->get();

		$labels = [];
		$data = [];
		$revenue = [];

		foreach ( $contractsByManufacturer as $contract ) {
			$manufacturerName = $contract->manufacturer->name; // Assuming a relationship exists
			$labels[] = $manufacturerName;
			$data[] = $contract->total;
			$revenue[] = (int) $contract->revenue;
		}
		return [ 
			'labels' => $labels,
			'data' => $data,
			'revenue' => $revenue,
		];
	}
	public function nearestContract() {
		$currentDate = Carbon::now();
		$selectedDaysFromNow = $currentDate->addDays( settings( 'expiration_days' ) );
		$nearestContracts = Contract::where( 'end_date', '<=', $selectedDaysFromNow )
			->orderBy( 'end_date' )
			->get()
			->take( 5 );
		return $nearestContracts;
	}
	public function openRevenue() {
		$contractIds = Renewal::where( 'status', 'Open' )->pluck( 'contract_id' );
		$closeLostContracts = Contract::whereIn( 'id', $contractIds )->get();

		$sumCloseLostRevenue = 0;
		$countCloseLostContracts = 0;

		foreach ( $closeLostContracts as $contract ) {
			$revenue = $contract->contract_price - $contract->contract_cost;
			$sumCloseLostRevenue += $revenue;
			$countCloseLostContracts++;
		}

		return [ $sumCloseLostRevenue, $countCloseLostContracts ];
	}
	public function closeWonRevenue() {
		$contractIds = Renewal::where( 'status', 'Close won' )->pluck( 'contract_id' );
		$closeLostContracts = Contract::whereIn( 'id', $contractIds )->get();

		$sumCloseLostRevenue = 0;
		$countCloseLostContracts = 0;

		foreach ( $closeLostContracts as $contract ) {
			$revenue = $contract->contract_price - $contract->contract_cost;
			$sumCloseLostRevenue += $revenue;
			$countCloseLostContracts++;
		}

		return [ $sumCloseLostRevenue, $countCloseLostContracts ];
	}
	public function closeLostRevenue() {
		$contractIds = Renewal::where( 'status', 'Close lsot' )->pluck( 'contract_id' );
		$closeLostContracts = Contract::whereIn( 'id', $contractIds )->get();

		$sumCloseLostRevenue = 0;
		$countCloseLostContracts = 0;

		foreach ( $closeLostContracts as $contract ) {
			$revenue = $contract->contract_price - $contract->contract_cost;
			$sumCloseLostRevenue += $revenue;
			$countCloseLostContracts++;
		}

		return [ $sumCloseLostRevenue, $countCloseLostContracts ];
	}



	public function revenueCount() {
		$currentDate = Carbon::now();

		$totalContractRevenue = Contract::sum( 'contract_revenue' );
		$minContractRevenue = Contract::min( 'contract_revenue' );
		$renewalContractsCount = Contract::where( 'end_date', '>', $currentDate )->count();
		return [ $totalContractRevenue, $minContractRevenue, $renewalContractsCount ];
	}

	// Add functions to calculate monthly, quarterly, and annual costs and profit margins

	// Function to calculate monthly cost
	// private function calculateMonthlyCost($contract)
	// {
	//     $totalValue = $contract->value;
	//     $durationInMonths = $this->calculateDurationInMonths($contract);

	//     $monthlyCost = $totalValue / $durationInMonths;

	//     return round($monthlyCost, 2);
	// }

	// Function to calculate quarterly cost
	private function calculateQuarterlyCost( $contract ) {
		$totalValue = $contract->contract_price;
		$durationInMonths = $this->calculateDurationInMonths( $contract );
		// Calculate the quarterly cost
		$quarterlyCost = ( $totalValue / $durationInMonths ) * 3;

		return round( $quarterlyCost, 2 );
	}

	// Function to calculate annual cost
	// private function calculateAnnualCost($contract)
	// {
	//     $totalValue = $contract->value;
	//     $durationInMonths = $this->calculateDurationInMonths($contract);

	//     // Calculate the annual cost
	//     $annualCost = ($totalValue / $durationInMonths) * 12;

	//     return round($annualCost, 2);
	// }

	// Function to calculate profit margin
	// private function calculateProfitMargin($contract)
	// {
	//     $totalValue = $contract->value;
	//     $totalCosts = $this->calculateTotalCosts($contract);
	//     // Calculate the profit
	//     $profit = $totalValue - $totalCosts;

	//     // Calculate the profit margin as a percentage
	//     $profitMargin = ($profit / $totalValue) * 100;

	//     return $profitMargin;
	// }

	// public function calculateTotalCosts($contract){
	//     $materialCosts = $contract->material_costs;
	//     $laborCosts = $contract->labor_costs;
	//     $overheadCosts = $contract->overhead_costs;
	//     // Calculate the total costs by summing up all the costs
	//     $totalCosts = $materialCosts + $laborCosts + $overheadCosts;

	//     return $totalCosts;
	// }

	// helpers


	public function calculateYearlyProfitRevenue() {
		$contracts = Contract::all();
		$yearlyData = [];

		foreach ( $contracts as $contract ) {
			$startDate = Carbon::parse( $contract->start_date );
			$endDate = Carbon::parse( $contract->end_date );
			$contractYears = $startDate->diffInYears( $endDate );
			$totalRevenue = $contract->contract_revenue;
			$dailyRevenue = $totalRevenue / $startDate->diffInDays( $endDate );

			for ( $i = 0; $i <= $contractYears; $i++ ) {
				$currentYear = $startDate->copy()->addYears( $i )->format( 'Y' );
				$daysInYear = $startDate->copy()->addYears( $i )->isLeapYear() ? 366 : 365;
				$yearlyRevenue = $dailyRevenue * $daysInYear;
				$yearlyProfit = $yearlyRevenue;
				$yearlyData[ $currentYear ]['revenue'] = isset( $yearlyData[ $currentYear ]['revenue'] ) ? $yearlyData[ $currentYear ]['revenue'] + $yearlyRevenue : $yearlyRevenue;
				$yearlyData[ $currentYear ]['profit'] = isset( $yearlyData[ $currentYear ]['profit'] ) ? $yearlyData[ $currentYear ]['profit'] + $yearlyProfit : $yearlyProfit;
			}
		}

		ksort( $yearlyData );

		$yearlyData = array_map( function ($value) {
			return [ 
				'revenue' => number_format( $value['revenue'], 2, '.', '' ),
				'profit' => number_format( $value['profit'], 2, '.', '' )
			];
		}, $yearlyData );

		$graphValuesRevenue = array_column( $yearlyData, 'revenue' );
		$graphValuesProfit = array_column( $yearlyData, 'profit' );
		$graphDates = array_keys( $yearlyData );

		return [ 
			'graphValuesRevenue' => $graphValuesRevenue,
			'graphValuesProfit' => $graphValuesProfit,
			'graphDates' => $graphDates
		];
	}






	// public function calculateDurationInMonths($contract)
	// {
	//     $startDate = Carbon::parse($contract->start_date);
	//     $endDate = Carbon::parse($contract->end_date);
	//     if ($contract->renewal_date) {
	//         $endDate = Carbon::parse($contract->renewal_date);
	//     }
	//     $durationInMonths = $startDate->diffInMonths($endDate);

	//     return $durationInMonths;
	// }
}
