<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCorporateDetailsToUsersTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table( 'users', function (Blueprint $table) {
			if ( ! Schema::hasColumn( 'users', 'first_name' ) ) {
				$table->string( 'first_name' )->nullable()->after( 'name' );
			}
			if ( ! Schema::hasColumn( 'users', 'last_name' ) ) {
				$table->string( 'last_name' )->nullable()->after( 'first_name' );
			}
			if ( ! Schema::hasColumn( 'users', 'job_title' ) ) {
				$table->string( 'job_title' )->nullable()->after( 'last_name' );
			}
			if ( ! Schema::hasColumn( 'users', 'department' ) ) {
				$table->string( 'department' )->nullable()->after( 'job_title' );
			}
			if ( ! Schema::hasColumn( 'users', 'company_name' ) ) {
				$table->string( 'company_name' )->nullable()->after( 'department' );
			}
			if ( ! Schema::hasColumn( 'users', 'phone' ) ) {
				$table->string( 'phone' )->nullable()->after( 'company_name' );
			}
			if ( ! Schema::hasColumn( 'users', 'avatar' ) ) {
				$table->string( 'avatar' )->nullable()->after( 'phone' );
			}
			if ( ! Schema::hasColumn( 'users', 'company_logo' ) ) {
				$table->string( 'company_logo' )->nullable()->after( 'avatar' );
			}
		} );

		Schema::table( 'users', function (Blueprint $table) {
			$table->dropColumn( [ 'name' ] );
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table( 'users', function (Blueprint $table) {
			$table->dropColumn( [ 
				'first_name',
				'last_name',
				'job_title',
				'department',
				'company_name',
				'phone',
				'avatar',
				'company_logo',
			] );
		} );

		Schema::table( 'users', function (Blueprint $table) {
			$table->string( 'name' )->nullable()->after( 'id' );
		} );
	}
}
