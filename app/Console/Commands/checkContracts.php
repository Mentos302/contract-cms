<?php

namespace App\Console\Commands;

use App\Models\Contract;
use App\Notifications\ContractRenewalNotification;
use App\Notifications\ExpiredContractNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class checkContracts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-contracts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = ' checking for upcoming renewals and expired contracts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();

        // Find contracts with renewal date within the next 30 days
        $upcomingRenewals = Contract::where('end_date', '>=', $today)
            ->where('end_date', '<=', $today->addDays(settings('expiration_days')))
            ->get();

        // Send email notifications for upcoming renewals
        foreach ($upcomingRenewals as $contract) {
            $contract->customer->notify(new ContractRenewalNotification($contract));
        }

        // Find expired contracts
        $expiredContracts = Contract::where('end_date', '<', $today)
            ->get();

        // Send email notifications for expired contracts
        foreach ($expiredContracts as $contract) {
            $contract->customer->notify(new ExpiredContractNotification($contract));
        }
    }
}
