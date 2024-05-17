<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupportTicketNotification extends Mailable {
	use Queueable, SerializesModels;

	public $subject;
	public $msg;
	public $contractName;
	public $contractId;

	/**
	 * Create a new message instance.
	 *
	 * @return void
	 */
	public function __construct( $ticket ) {
		$this->subject = $ticket['subject'];
		$this->msg = $ticket['message'];
		$this->contractName = $ticket['contract_name'];
		$this->contractId = $ticket['contract_id'];
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build() {
		$subject = "[CMS Support Ticket] " . $this->subject;

		return $this->subject( $subject )
			->view( 'emails.support_ticket_notification' )->replyTo( auth()->user()->email );
		;
	}
}
