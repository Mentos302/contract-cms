<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToContractsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table( 'contracts', function (Blueprint $table) {
			$table->enum( 'status', [ 'quote', 'not', 'another' ] )->nullable();
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table( 'contracts', function (Blueprint $table) {
			$table->dropColumn( 'status' );
		} );
	}
}
