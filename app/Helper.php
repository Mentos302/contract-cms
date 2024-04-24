<?php

use App\Models\Setting;

function settings( $key ) {
	$data = Setting::select( 'value' )->where( 'key', $key )->first();
	return $data->value ?? '';
}

use Carbon\Carbon;

if ( ! function_exists( 'formatDate' ) ) {
	/**
	 * Format date as Month-Day-Year.
	 *
	 * @param string $date
	 * @return string
	 */
	function formatDate( $date ) {
		return Carbon::parse( $date )->format( 'm-d-Y' );
	}
}
