<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewUserWelcome extends Mailable {
	use Queueable, SerializesModels;

	public $email;
	public $password;

	/**
	 * Create a new message instance.
	 *
	 * @return void
	 */
	public function __construct( $email, $password = null ) {
		$this->email = $email;
		$this->password = $password;
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build() {
		if ( $this->password ) {
			return $this->subject( 'Welcome to our platform' )
				->view( 'emails.new_user_created' );
		} else {
			return $this->subject( 'Welcome to our platform' )
				->view( 'emails.new_user_signup' );
		}
	}
}
