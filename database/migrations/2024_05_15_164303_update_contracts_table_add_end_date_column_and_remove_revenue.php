<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateContractsTableAddEndDateColumnAndRemoveRevenue extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		if ( ! Schema::hasColumn( 'contracts', 'end_date' ) ) {
			Schema::table( 'contracts', function (Blueprint $table) {
				$table->date( 'end_date' )->nullable()->after( 'start_date' );
			} );

			$contracts = \App\Models\Contract::all();
			foreach ( $contracts as $contract ) {
				if ( $contract->start_date && $contract->term ) {
					$contract->end_date = $contract->start_date->addYears( $contract->term->name );
					$contract->save();
				}
			}
		}

		Schema::table( 'contracts', function (Blueprint $table) {
			$table->dropColumn( 'contract_revenue' );
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table( 'contracts', function (Blueprint $table) {
			$table->decimal( 'contract_revenue', 12, 2 )->nullable()->after( 'contract_cost' );
		} );

		// No action needed for removing 'end_date' column on down migration
	}
}
