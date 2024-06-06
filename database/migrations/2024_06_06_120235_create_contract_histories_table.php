<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractHistoriesTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create( 'contract_histories', function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger( 'contract_id' );
			$table->integer( 'year' );
			$table->string( 'quote_number' );
			$table->string( 'quote_file' );
			$table->integer( 'purchase_order_number' );
			$table->string( 'po_file' );
			$table->integer( 'invoice_number' );
			$table->timestamps();

			$table->foreign( 'contract_id' )->references( 'id' )->on( 'contracts' )->onDelete( 'cascade' );
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists( 'contract_histories' );
	}
}
