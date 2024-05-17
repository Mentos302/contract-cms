<?php

namespace App\Console\Commands;

use App\Models\Contract;
use App\Mail\ContractExpirationNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendContractExpirationEmails extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'contract:expiration-emails';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Send email notifications for contracts expiring in 90 and 30 days';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle() {
		$contracts90 = Contract::whereDate( 'end_date', Carbon::today()->addDays( 90 ) )->with( 'customer', 'manufacturer' )->get();
		$contracts30 = Contract::whereDate( 'end_date', Carbon::today()->addDays( 30 ) )->with( 'customer', 'manufacturer' )->get();

		foreach ( $contracts90 as $contract ) {
			$user = $contract->customer;
			$manufacturer = $contract->manufacturer;
			if ( $user && $manufacturer ) {
				Mail::to( $user->email )->send( new ContractExpirationNotification( $user->email, $user->first_name, $contract, 90, $manufacturer->name ) );
			}
		}

		foreach ( $contracts30 as $contract ) {
			$user = $contract->customer;
			$manufacturer = $contract->manufacturer;
			if ( $user && $manufacturer ) {
				Mail::to( $user->email )->send( new ContractExpirationNotification( $user->email, $user->first_name, $contract, 30, $manufacturer->name ) );
			}
		}
	}
}
