<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Contract;

class ContractPolicy {
	public function view( User $user, Contract $contract ) {
		// Allow contract owner or admin to view the contract
		return $user->id === $contract->customer_id || $user->hasRole( 'admin' );
	}
}
