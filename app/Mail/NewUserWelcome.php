<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewUserWelcome extends Mailable {
	use Queueable, SerializesModels;

	public $email;
	public $password;
	public $first_name;

	/**
	 * Create a new message instance.
	 *
	 * @param string $email
	 * @param string|null $password
	 * @param string $first_name
	 * @return void
	 */
	public function __construct( $email, $first_name, $password = null ) {
		$this->email = $email;
		$this->password = $password;
		$this->first_name = $first_name;
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build() {
		$subject = "Welcome to CMS by Sivility Systems";

		if ( $this->password ) {
			return $this->subject( $subject )
				->view( 'emails.new_user_created' );
		} else {
			return $this->subject( $subject )
				->view( 'emails.new_user_signup' );
		}
	}
}
