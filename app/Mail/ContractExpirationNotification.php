<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContractExpirationNotification extends Mailable {
	use Queueable, SerializesModels;

	public $email;
	public $first_name;
	public $contract;
	public $days_remaining;
	public $manufacturer_name;

	public $type;

	/**
	 * Create a new message instance.
	 *
	 * @return void
	 */
	public function __construct( $email, $first_name, $contract, $days_remaining, $manufacturer_name ) {
		$this->email = $email;
		$this->first_name = $first_name;
		$this->contract = $contract;
		$this->days_remaining = $days_remaining;
		$this->manufacturer_name = $manufacturer_name;
		$this->type = $days_remaining;
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build() {
		$subject = "{$this->contract->mfr_contract_number} Contract {$this->contract->mfr_contract_number} {$this->type} Day Expiration Notice";

		$template = $this->type === 90 ? 'emails.contract_expiration_90days_notification' : 'emails.contract_expiration_30days_notification';

		return $this->subject( $subject )->view( $template )
			->with( [ 
				'id' => $this->contract->id,
				'first_name' => $this->first_name,
				'contract_number' => $this->contract->mfr_contract_number,
				'expiration_date' => $this->contract->end_date,
				'manufacturer_name' => $this->manufacturer_name,
			] );
	}
}
