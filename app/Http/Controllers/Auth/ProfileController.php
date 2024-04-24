<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class ProfileController extends Controller {
	public function profile() {
		$user = User::findOrfail( Auth::user()->id );
		return view( 'pages-profile', compact( 'user' ) );
	}

	public function updateProfile( Request $request, $id ) {
		$request->validate( [ 
			'email' => [ 'required', 'string', 'email' ],
			'first_name' => [ 'required', 'string', 'max:255' ],
			'last_name' => [ 'required', 'string', 'max:255' ],
			'job_title' => [ 'required', 'string', 'max:255' ],
			'department' => [ 'required', 'string', 'max:255' ],
			'company_name' => [ 'required', 'string', 'max:255' ],
			'phone' => [ 'required', 'string', 'max:255' ],
		] );

		$user = User::findOrFail( $id );
		$user->email = $request->email;
		$user->first_name = $request->first_name;
		$user->last_name = $request->last_name;
		$user->job_title = $request->job_title;
		$user->department = $request->department;
		$user->company_name = $request->company_name;
		$user->phone = $request->phone;

		$user->save();

		Session::flash( 'success', 'User details updated successfully!' );
		return redirect()->back();
	}

	public function updatePassword( Request $request, $id ) {
		$request->validate( [ 
			'current_password' => [ 'required', 'string' ],
			'password' => [ 'required', 'string', 'min:6', 'confirmed' ],
		] );

		if ( ! ( Hash::check( $request->current_password, Auth::user()->password ) ) ) {
			return response()->json( [ 
				'isSuccess' => false,
				'Message' => 'Your current password does not match with the password you provided. Please try again.'
			], 200 ); // Status code
		}

		$user = User::find( $id );
		$user->password = Hash::make( $request->password );
		$user->save();

		Session::flash( 'success', 'Password updated successfully!' );
		return redirect()->back();
	}

	public function updateAvatar( Request $request, $id ) {
		$request->validate( [ 
			'avatar' => [ 'required', 'image', 'mimes:jpg,jpeg,png', 'max:1024' ],
		] );

		$user = User::findOrFail( $id );

		if ( $request->hasFile( 'avatar' ) ) {
			$avatar = $request->file( 'avatar' );
			$avatarName = time() . '.' . $avatar->getClientOriginalExtension();
			$avatarPath = 'user/';
			$avatar->move( storage_path( 'app/public/' . $avatarPath ), $avatarName );
			$user->avatar = 'storage/' . $avatarPath . $avatarName; // Check if this is the correct path
			$user->save();
		}

		Session::flash( 'success', 'Avatar updated successfully!' );
		return redirect()->back();
	}


	public function updateCompanyLogo( Request $request, $id ) {
		$request->validate( [ 
			'company_logo' => [ 'required', 'image', 'mimes:jpg,jpeg,png', 'max:1024' ],
		] );

		$user = User::findOrFail( $id );

		if ( $request->hasFile( 'company_logo' ) ) {
			$companyLogo = $request->file( 'company_logo' );
			$companyLogoName = time() . '.' . $companyLogo->getClientOriginalExtension();
			$companyLogoPath = 'company_logos/';
			$companyLogo->move( storage_path( 'app/public/' . $companyLogoPath ), $companyLogoName );
			$user->company_logo = 'storage/' . $companyLogoPath . $companyLogoName;
			$user->save();
		}

		Session::flash( 'success', 'Company logo updated successfully!' );
		return redirect()->back();
	}
}
