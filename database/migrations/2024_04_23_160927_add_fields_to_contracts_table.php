<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up() {
		Schema::table( 'contracts', function (Blueprint $table) {
			$table->string( 'serial_number' )->nullable();
			$table->string( 'mfr_contract_number' )->nullable();
			$table->string( 'name' )->nullable();
		} );
	}

	public function down() {
		Schema::table( 'contracts', function (Blueprint $table) {
			$table->dropColumn( [ 'serial_number', 'mfr_contract_number', 'name' ] );
		} );
	}

};
