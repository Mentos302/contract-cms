<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function breakContractsDownByMonth(Request $request)
    {
        $user= auth()->user();
        $startDate= $request->start_date;
        $endDate= $request->end_date;
        $customer_id= $request->customer_id;
        $endDate= $request->end_date;
        $monthlyTotals = [];
        if( $startDate &&  $endDate){
            $qry = Contract::query();
            if($user->hasRole('admin')){
                $qry->where('customer_id', $customer_id);
            }
            else{
                $qry->where('customer_id', $user->id);
            }
            $contracts= $qry->where(function ($query) use ($startDate, $endDate) {
                $query->where('start_date', '<=', $endDate)
                      ->where('end_date', '>=', $startDate);
            })->get();

            foreach ($contracts as $contract) {
                // Determine the months that the contract overlaps with
                $start = max($contract->start_date, $startDate);
                $end = min($contract->end_date, $endDate);

                // Calculate the total value for each month
                $currentMonth = Carbon::parse($start);
                $endMonth = Carbon::parse($end);

                while ($currentMonth->lessThanOrEqualTo($endMonth)) {
                    $month = $currentMonth->format('Y-m');
                    $monthlyTotals[$month] = isset($monthlyTotals[$month]) ? $monthlyTotals[$month] + $contract->contract_price : $contract->contract_price;
                    $currentMonth->addMonth();
                }
            }
        }
        return view('admin.reports.break-contracts-downby-month', compact('monthlyTotals'));
    }

    public function upcomingRenewalRevenue(Request $request)
    {
        $startDate= $request->start_date;
        $endDate= $request->end_date;
        $user= auth()->user();
        $customer_id= $request->customer_id;

         // Calculate revenue for upcoming year, quarter, and month, considering contract term
         $annualRevenue = 0;
         $quarterlyRevenue = 0;
         $monthlyRevenue = 0;

        // Query contracts set to renew within the specified date range
        if( $startDate &&  $endDate){
            $qry = Contract::query();
            if($user->hasRole('admin')){
                $qry->where('customer_id', $customer_id);
            }
            else{
                $qry->where('customer_id', $user->id);
            }
            $renewableContracts = $qry->whereBetween('end_date', [$startDate, $endDate])->get();

            foreach ($renewableContracts as $contract) {
                $contractTerm = $contract->term->name; // Get contract term in years

                // Calculate revenue based on contract term
                $annualRevenue += $contract->contract_revenue * $contractTerm;
                $quarterlyRevenue += $contract->contract_revenue * $contractTerm * 4;
                $monthlyRevenue += $contract->contract_revenue * $contractTerm * 12;
            }
        }

        return view('admin.reports.upcoming-renewal-revenue', compact('annualRevenue','quarterlyRevenue','monthlyRevenue'));

    }
    public function lostContracts(Request $request)
    {

        $startDate= $request->start_date;
        $endDate= $request->end_date;
        $user= auth()->user();
        $customer_id= $request->customer_id;
        $lostRevenue=0;
        if( $startDate &&  $endDate){
            $qry = Contract::query();
            if($user->hasRole('admin')){
                $qry->where('customer_id', $customer_id);
            }
            else{
                $qry->where('customer_id', $user->id);
            }
            $lostContracts = $qry->where('end_date', '>=', $startDate)
                                    ->where('end_date', '<=', $endDate)
                                    ->get();
            // Calculate total value of lost contracts
            $lostRevenue = $lostContracts->sum('contract_revenue');
        }
        return view('admin.reports.lost-contracts', compact('lostRevenue'));
    }

    public function totalContractBaseValue(Request $request)
    {
        // $existingContracts = Contract::where('customer_id',auth()->user()->id)->whereNull('renewal_date')->get();
            $user= auth()->user();
            $customer_id= $request->customer_id;
           $qry = Contract::query();
            if($user->hasRole('admin')){
                $qry->where('customer_id', $customer_id);
            }
            else{
                $qry->where('customer_id', $user->id);
            }
        $renewableContracts = $qry
        ->whereNotNull('end_date')->get();

        // Calculate total value of all contracts
        $totalValue = $renewableContracts->sum('contract_price');

        return view('admin.reports.total-contract-base-value', compact('totalValue'));
    }

}
