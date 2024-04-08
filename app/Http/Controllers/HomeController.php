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
		// // Retrieve contract data from the database
		// $contracts = Contract::paginate(15);
		// // Calculate monthly, quarterly, and annual costs for each contract
		// foreach ($contracts as $contract) {
		//     // Perform calculations and store the results in the contract object
		//     $contract->monthly_cost = $this->calculateMonthlyCost($contract);
		//     $contract->quarterly_cost = $this->calculateQuarterlyCost($contract);
		//     $contract->annual_cost = $this->calculateAnnualCost($contract);
		//     // $contract->profit_margin = $this->calculateProfitMargin($contract);
		// }
		// $profit_revenue= $this->calculateYearlyProfitRevenue();
		//  dd($profit_revenue);
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
			->whereDoesntHave( 'renewals', function ($query) {
				$query->where( 'status', 'Open' );
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
		// Retrieve the contract data from the database
		$user = Auth::user();
		$qry = Contract::query();

		$contracts = $qry->get();

		// Initialize an array to store yearly profit/revenue
		$yearlyData = [];

		// Loop through each contract
		foreach ( $contracts as $contract ) {
			// Get the start date and end date of the contract
			$startDate = Carbon::parse( $contract->start_date );
			$endDate = Carbon::parse( $contract->end_date );

			// Calculate the contract duration in years
			$contractYears = $startDate->diffInYears( $endDate );

			// Loop through each year of the contract
			for ( $i = 0; $i <= $contractYears; $i++ ) {
				// Get the year for the current iteration
				$currentYear = $startDate->copy()->addYears( $i )->format( 'Y' );

				// Add the contract value to the corresponding year in the array
				$yearlyData[ $currentYear ] = isset( $yearlyData[ $currentYear ] ) ? $yearlyData[ $currentYear ] + $contract->contract_revenue : $contract->contract_revenue;
			}
		}

		// Prepare the data for graph values and dates
		$graphValues = array_values( $yearlyData );
		$graphDates = array_keys( $yearlyData );

		return [ 
			'graphValues' => $graphValues,
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
