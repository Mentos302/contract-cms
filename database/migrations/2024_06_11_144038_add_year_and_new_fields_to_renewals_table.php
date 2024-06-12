<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddYearAndNewFieldsToRenewalsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table( 'renewals', function (Blueprint $table) {
			if ( ! Schema::hasColumn( 'renewals', 'year' ) ) {
				$table->smallInteger( 'year' )->nullable();
			}
			if ( ! Schema::hasColumn( 'renewals', 'quote_number' ) ) {
				$table->string( 'quote_number' )->nullable();
			}
			if ( ! Schema::hasColumn( 'renewals', 'quote_file' ) ) {
				$table->string( 'quote_file' )->nullable();
			}
			if ( ! Schema::hasColumn( 'renewals', 'purchase_order_number' ) ) {
				$table->string( 'purchase_order_number' )->nullable();
			}
			if ( ! Schema::hasColumn( 'renewals', 'po_file' ) ) {
				$table->string( 'po_file' )->nullable();
			}
			if ( ! Schema::hasColumn( 'renewals', 'invoice_number' ) ) {
				$table->string( 'invoice_number' )->nullable();
			}
			if ( ! Schema::hasColumn( 'renewals', 'invoice_file' ) ) {
				$table->string( 'invoice_file' )->nullable();
			}
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table( 'renewals', function (Blueprint $table) {
			$table->dropColumn( 'year' );
			$table->dropColumn( 'quote_number' );
			$table->dropColumn( 'quote_file' );
			$table->dropColumn( 'purchase_order_number' );
			$table->dropColumn( 'po_file' );
			$table->dropColumn( 'invoice_number' );
			$table->dropColumn( 'invoice_file' );
		} );
	}
}
