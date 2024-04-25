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

		$active_contracts = $this->getActiveContracts( $qry );
		$expiring_soon_contracts = $this->getExpiringSoonContracts( $qry );
		$expired_contracts = $this->getExpiredContracts( $qry );
		$not_renewing_contracts = $this->notRenewingContracts( $qry );

		$profit_revenue_monthly = $this->calculateProfitRevenueByTerm( "monthly" );
		$profit_revenue_quarterly = $this->calculateProfitRevenueByTerm( "quarterly" );
		$profit_revenue_annually = $this->calculateProfitRevenueByTerm( "annually" );

		$totalCustomer = $user->hasRole( 'admin' ) ? User::role( 'customer' )->count() : 0;

		$graph_by_manufacturer = $this->contractGraphByManufacturer();
		$nearest_contracts = $this->nearestContract();

		$open_revenue = $this->getOpenRevenue();
		$close_won_revenue = $this->getCloseWonRevenue();
		$close_lost_revenue = $this->getCloseLostRevenue();
		$revenue_and_count = $this->revenueCount( $qry );

		$owners_revenue = $this->compareOwnersRevenue();

		return view( 'home', compact(
			'graph_by_manufacturer',
			'active_contracts',
			'expiring_soon_contracts',
			'expired_contracts',
			'not_renewing_contracts',
			'totalCustomer',
			'profit_revenue_monthly',
			'profit_revenue_quarterly',
			'profit_revenue_annually',
			'nearest_contracts',
			'open_revenue',
			'close_won_revenue',
			'close_lost_revenue',
			'revenue_and_count',
			'owners_revenue'
		) );
	}

	public function getActiveContracts( $qry ) {
		return $qry->where( 'start_date', '<=', now() )
			->where( 'end_date', '>=', now() )
			->get();
	}

	public function getExpiringSoonContracts( $qry ) {
		return $qry->where( 'end_date', '>=', now() )
			->where( 'end_date', '<=', now()->addDays( settings( 'expiration_days' ) ) )
			->get();
	}

	public function getExpiredContracts( $qry ) {
		return $qry->where( 'end_date', '<', now() )
			->get();
	}

	public function notRenewingContracts( $qry ) {
		return $qry->where( function ($query) {
			$query->whereHas( 'renewals', function ($q) {
				$q->where( 'status', 'Close lost' );
			} )->orWhere( function ($query) {
				$query->doesntHave( 'renewals' )
					->whereDate( 'end_date', '<', now()->subDays( 15 ) );
			} );
		} )->get();
	}

	public function contractsStatus( Request $request ) {
		$contracts_status = [];

		$qry = Contract::query()->where( 'contract_owner', 'Sivility Systems' );

		if ( $request->status == 'active' ) {
			$contracts_status = $this->getActiveContracts( $qry );
		} elseif ( $request->status == 'expired' ) {
			$contracts_status = $this->getExpiredContracts( $qry );
		} elseif ( $request->status == 'expiring-soon' ) {
			$contracts_status = $this->getExpiringSoonContracts( $qry );
		}

		return view( 'admin.contract.contract-status', compact( 'contracts_status' ) );
	}

	public function contractGraphByManufacturer() {
		$contractsByManufacturer = Contract::select( 'manufacturer_id', DB::raw( 'COUNT(*) as total, sum(contract_revenue) as revenue' ) )
			->where( 'contract_owner', 'Sivility Systems' )
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
			->take( 5 )
			->get();

		return $nearestContracts->count() ? $nearestContracts : [];
	}

	public function getOpenRevenue() {
		$contractIds = Renewal::where( 'status', 'Open' )->pluck( 'contract_id' );
		$openContracts = Contract::whereIn( 'id', $contractIds )->get();

		$sumOpenRevenue = 0;
		$countOpenContracts = 0;

		foreach ( $openContracts as $contract ) {
			$revenue = $contract->contract_price - $contract->contract_cost;
			$sumOpenRevenue += $revenue;
			$countOpenContracts++;
		}

		return [ 'sumOpenContracts' => $sumOpenRevenue, 'countOpenContracts' => $countOpenContracts ];
	}

	public function getCloseWonRevenue() {
		$contractIds = Renewal::where( 'status', 'Close won' )->pluck( 'contract_id' );
		$closeWonContracts = Contract::whereIn( 'id', $contractIds )->get();

		$sumCloseWonRevenue = 0;
		$countCloseWonContracts = 0;

		foreach ( $closeWonContracts as $contract ) {
			$revenue = $contract->contract_price - $contract->contract_cost;
			$sumCloseWonRevenue += $revenue;
			$countCloseWonContracts++;
		}

		return [ 'sumCloseWonContracts' => $sumCloseWonRevenue, 'countCloseWonContracts' => $countCloseWonContracts ];
	}

	public function getCloseLostRevenue() {
		$contractIds = Renewal::where( 'status', 'Close lost' )->pluck( 'contract_id' );
		$closeLostContracts = Contract::whereIn( 'id', $contractIds )->get();

		$sumCloseLostRevenue = 0;
		$countCloseLostContracts = 0;

		foreach ( $closeLostContracts as $contract ) {
			$revenue = $contract->contract_price - $contract->contract_cost;
			$sumCloseLostRevenue += $revenue;
			$countCloseLostContracts++;
		}

		return [ 'sumCloseLostContracts' => $sumCloseLostRevenue, 'countCloseLostContracts' => $countCloseLostContracts ];
	}

	public function revenueCount( $qry ) {
		$totalContractRevenue = $qry->sum( 'contract_revenue' );
		$minContractRevenue = $qry->min( 'contract_revenue' );
		$renewalContractsCount = $qry->where( 'end_date', '>', Carbon::now() )->count();

		return [ $totalContractRevenue, $minContractRevenue, $renewalContractsCount ];
	}



	public function calculateProfitRevenueByTerm( $term ) {
		$contracts = Contract::where( 'contract_owner', 'Sivility Systems' )->get();

		$data = [];

		foreach ( $contracts as $contract ) {
			$startDate = Carbon::parse( $contract->start_date );
			$endDate = Carbon::parse( $contract->end_date );
			$contractYears = $startDate->diffInYears( $endDate );
			$totalRevenue = $contract->contract_price;
			$totalCost = $contract->contract_cost;
			$dailyRevenue = $totalRevenue / $startDate->diffInDays( $endDate );
			$dailyCost = $totalCost / $startDate->diffInDays( $endDate );

			if ( $term === 'annually' ) {
				for ( $i = 0; $i <= $contractYears; $i++ ) {
					$currentYear = $startDate->copy()->addYears( $i )->format( 'Y' );
					$daysInYear = $startDate->copy()->addYears( $i )->isLeapYear() ? 366 : 365;
					$yearlyRevenue = $dailyRevenue * $daysInYear;
					$yearlyCost = $dailyCost * $daysInYear;
					$yearlyProfit = $yearlyRevenue - $yearlyCost;

					if ( ! isset( $data[ $currentYear ] ) ) {
						$data[ $currentYear ] = [ 'revenue' => 0, 'profit' => 0 ];
					}

					$data[ $currentYear ]['revenue'] += $yearlyRevenue;
					$data[ $currentYear ]['profit'] += $yearlyProfit;
				}
			} elseif ( $term === 'quarterly' ) {
				for ( $i = 0; $i <= $contractYears; $i++ ) {
					$currentYear = $startDate->copy()->addYears( $i )->format( 'Y' );

					for ( $j = 1; $j <= 4; $j++ ) {
						$startMonth = ( $j - 1 ) * 3 + 1;
						$endMonth = $startMonth + 2;
						$startQuarter = $startDate->copy()->addYears( $i )->month( $startMonth );
						$endQuarter = $startDate->copy()->addYears( $i )->month( $endMonth )->endOfMonth();
						$daysInQuarter = $startQuarter->diffInDays( $endQuarter ) + 1; // Add 1 to include the end date
						$quarterlyRevenue = $dailyRevenue * $daysInQuarter;
						$quarterlyCost = $dailyCost * $daysInQuarter;
						$quarterlyProfit = $quarterlyRevenue - $quarterlyCost;

						if ( ! isset( $data[ $currentYear . '-Q' . $j ] ) ) {
							$data[ $currentYear . '-Q' . $j ] = [ 'revenue' => 0, 'profit' => 0 ];
						}

						$data[ $currentYear . '-Q' . $j ]['revenue'] += $quarterlyRevenue;
						$data[ $currentYear . '-Q' . $j ]['profit'] += $quarterlyProfit;
					}
				}
			} elseif ( $term === 'monthly' ) {
				for ( $i = 0; $i <= $contractYears; $i++ ) {
					$currentYear = $startDate->copy()->addYears( $i )->format( 'Y' );

					for ( $j = 1; $j <= 12; $j++ ) {
						$daysInMonth = $startDate->copy()->addYears( $i )->month( $j )->daysInMonth;
						$monthlyRevenue = $dailyRevenue * $daysInMonth;
						$monthlyCost = $dailyCost * $daysInMonth;
						$monthlyProfit = $monthlyRevenue - $monthlyCost;

						if ( ! isset( $data[ $currentYear . '-' . str_pad( $j, 2, '0', STR_PAD_LEFT ) ] ) ) {
							$data[ $currentYear . '-' . str_pad( $j, 2, '0', STR_PAD_LEFT ) ] = [ 'revenue' => 0, 'profit' => 0 ];
						}

						$data[ $currentYear . '-' . str_pad( $j, 2, '0', STR_PAD_LEFT ) ]['revenue'] += $monthlyRevenue;
						$data[ $currentYear . '-' . str_pad( $j, 2, '0', STR_PAD_LEFT ) ]['profit'] += $monthlyProfit;
					}
				}
			}
		}

		$formattedData = [];

		foreach ( $data as $year => $values ) {
			$formattedData[ $year ] = [ 
				'revenue' => number_format( $values['revenue'], 2, '.', '' ),
				'profit' => number_format( $values['profit'], 2, '.', '' ),
			];
		}

		return $formattedData;
	}

	public function compareOwnersRevenue() {
		$allContractsRevenue = Contract::sum( 'contract_price' );
		$sivilityContractsRevenue = Contract::where( 'contract_owner', 'Sivility Systems' )->sum( 'contract_price' );
		$difference = $allContractsRevenue - $sivilityContractsRevenue;

		return [ 'allContractsRevenue' => $allContractsRevenue, 'sivilityContractsRevenue' => $sivilityContractsRevenue, 'difference' => $difference ];
	}

}

