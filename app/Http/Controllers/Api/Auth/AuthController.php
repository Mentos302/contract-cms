<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller {
	public function register( RegisterRequest $request ) {
		return response()->json( [ 'error' => 'Unauthorized' ], 401 );

		// $data = $request->validated();
		// $data['password'] = bcrypt( $data['password'] );
		// $user = User::create( $data );
		// $user->assignRole( 'customer' );
		// $success['token'] = $user->createToken( 'SanctumAPI' )->plainTextToken;
		// $success['name'] = $user->name;

		// return response()->json( $success, 201 );
	}

	public function login( Request $request ) {
		$validator = Validator::make( $request->all(), [ 
			'email' => [ 'required', 'email' ],
			'password' => [ 'required' ],
		] );
		if ( $validator->fails() ) {
			$rtn = [ 
				'errors' => $validator->errors()
			];
			return response()->json( $rtn, 422 );
		}

		if ( Auth::attempt( [ 'email' => $request->email, 'password' => $request->password ] ) ) {
			$user = Auth::user();
			$success['token'] = $user->createToken( 'SanctumAPI' )->plainTextToken;
			$success['name'] = $user->name;
			return response()->json( $success, 200 );
		} else {
			return response()->json( [ 'error' => 'Unauthorized' ], 401 );
		}
	}
	public function logout( Request $request ) {
		auth()->user()->tokens()->delete();
		return [ 
			'message' => 'Logged out successfully'
		];
	}
}
