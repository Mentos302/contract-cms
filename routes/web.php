<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ContractController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Auth::routes();

Route::get( '/', function () {
	return redirect()->route( 'login' );
} );

Route::group( [ 'middleware' => 'auth' ], function () {
	Route::get( 'profile', [ App\Http\Controllers\Auth\ProfileController::class, 'profile' ] )->name( 'user.profile' );
	Route::post( '/update-profile/{id}', [ App\Http\Controllers\Auth\ProfileController::class, 'updateProfile' ] )->name( 'updateProfile' );
	Route::post( '/update-password/{id}', [ App\Http\Controllers\Auth\ProfileController::class, 'updatePassword' ] )->name( 'updatePassword' );
	Route::post( '/avatar/update/{id}', [ App\Http\Controllers\Auth\ProfileController::class, 'updateAvatar' ] )->name( 'avatar.update' );
	Route::post( '/company-logo/update/{id}', [ App\Http\Controllers\Auth\ProfileController::class, 'updateCompanyLogo' ] )->name( 'company.logo.update' );

	Route::get( 'index/{locale}', [ App\Http\Controllers\HomeController::class, 'lang' ] );
	Route::get( '/home', [ App\Http\Controllers\HomeController::class, 'index' ] )->name( 'home' );

	Route::group( [ 'middleware' => [ 'role:admin' ] ], function () {
		Route::resource( 'type', App\Http\Controllers\Admin\TypeController::class);
		Route::resource( 'manufacturer', App\Http\Controllers\Admin\ManufacturerController::class);
		Route::resource( 'distributor', App\Http\Controllers\Admin\DistributorController::class);
		Route::resource( 'term', App\Http\Controllers\Admin\TermController::class);
		Route::resource( 'customer', App\Http\Controllers\Admin\CustomerController::class);
	} );

	Route::group( [ 'middleware' => [ 'role:admin|customer' ] ], function () {
		Route::resource( 'renewal', App\Http\Controllers\Admin\RenewalController::class);
		Route::resource( 'contract', App\Http\Controllers\Admin\ContractController::class);
		Route::post( '/contracts/{id}/status', [ ContractController::class, 'updateStatus' ] )->name( 'contract.update.status' );
		Route::post( '/contracts/import', [ ContractController::class, 'contractsImport' ] )->name( 'contracts.import' );
		Route::post( '/support-tickets', [ ContractController::class, 'createSupportTicket' ] )->name( 'support-tickets.create' );
		Route::post( '/', [ App\Http\Controllers\Admin\SettingController::class, 'store' ] )->name( 'setting.store' );

		Route::get( 'contracts-status', [ App\Http\Controllers\HomeController::class, 'contractsStatus' ] )->name( 'contracts.status' );

		Route::group( [ 'prefix' => 'reports' ], function () {
			Route::get( 'break-contracts-down-by-month', [ App\Http\Controllers\Admin\ReportController::class, 'breakContractsDownByMonth' ] )->name( 'break.contracts.downby.month' );
			Route::get( 'upcoming-renewal-revenue', [ App\Http\Controllers\Admin\ReportController::class, 'upcomingRenewalRevenue' ] )->name( 'upcoming.renewal.revenue' );
			Route::get( 'lost-contracts', [ App\Http\Controllers\Admin\ReportController::class, 'lostContracts' ] )->name( 'lost.contracts' );
			Route::get( 'total-contract-base-value', [ App\Http\Controllers\Admin\ReportController::class, 'totalContractBaseValue' ] )->name( 'total.contract.base.value' );
		} );

		Route::post( '/renewal/store', [ App\Http\Controllers\Admin\RenewalController::class, 'storeCustomerRenewal' ] )->name( 'renewal.store.customer' );

		// Add contract history routes
		Route::post( '/contracts/{id}/history', [ ContractController::class, 'addHistory' ] )->name( 'contract.addHistory' );
		Route::delete( '/contracts/{contractId}/history/{historyId}', [ App\Http\Controllers\Admin\ContractController::class, 'deleteHistory' ] )->name( 'contract.deleteHistory' );

	} );
} );
