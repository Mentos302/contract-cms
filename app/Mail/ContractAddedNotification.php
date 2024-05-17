<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Contract;
use App\Models\User;

class ContractAddedNotification extends Mailable {
	use Queueable, SerializesModels;

	public $contract;
	public $manufacturerName;
	public $contractNumber;
	public $userFirstName;

	public function __construct( Contract $contract, $manufacturerName, $contractNumber ) {
		$this->contract = $contract;
		$this->manufacturerName = $manufacturerName;
		$this->contractNumber = $contractNumber;
		$this->userFirstName = User::findOrFail( $contract->customer_id )->first_name;
	}

	public function build() {
		return $this->subject( 'CMS NOTIFICATION - ' . $this->manufacturerName . ' Contract #' . $this->contractNumber . ' was Successfully Added' )
			->view( 'emails.new_contract_added' );
	}
}
