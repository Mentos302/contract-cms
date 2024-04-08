<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post( 'register', [ App\Http\Controllers\Api\Auth\AuthController::class, 'register' ] );
Route::post( 'login', [ App\Http\Controllers\Api\Auth\AuthController::class, 'login' ] );

Route::middleware( [ 'auth:sanctum' ] )->group( function () {
	Route::post( 'logout', [ App\Http\Controllers\Api\Auth\AuthController::class, 'logout' ] );

	Route::post( 'update-password', [ App\Http\Controllers\Api\Auth\UpdatePasswordConroller::class, 'updatePassword' ] );
	Route::get( 'profile', [ App\Http\Controllers\Api\UserProfileConroller::class, 'userProfile' ] );
	Route::post( 'profile', [ App\Http\Controllers\Api\UserProfileConroller::class, 'userProfilePost' ] );

	Route::get( 'types', [ App\Http\Controllers\Api\DropdownController::class, 'types' ] );
	Route::get( 'manufacturers', [ App\Http\Controllers\Api\DropdownController::class, 'manufacturers' ] );
	Route::get( 'distributors', [ App\Http\Controllers\Api\DropdownController::class, 'distributors' ] );
	Route::get( 'terms', [ App\Http\Controllers\Api\DropdownController::class, 'terms' ] );
	Route::post( 'break-contracts-down-by-month', [ App\Http\Controllers\Api\ContractController::class, 'breakContractsDownByMonth' ] );
	Route::post( 'upcoming-renewal-revenue', [ App\Http\Controllers\Api\ContractController::class, 'upcomingRenewalRevenue' ] );
	Route::post( 'lost-contracts', [ App\Http\Controllers\Api\ContractController::class, 'lostContracts' ] );
	Route::post( 'total-contract-base-value', [ App\Http\Controllers\Api\ContractController::class, 'totalContractBaseValue' ] );

	Route::resource( 'contract', App\Http\Controllers\Api\ContractController::class);
} );




// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
