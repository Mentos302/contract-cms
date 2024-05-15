<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewUserWelcome;

class CustomerController extends Controller {
	public function index( Request $request ) {
		$request->validate( [ 
			'search' => 'nullable',
			'limit' => 'nullable',
		] );

		$search = $request->input( 'search' );
		$limit = $request->input( 'limit' ) ?? 15;
		$qry = User::role( 'customer' )->latest();

		if ( ! empty( $search ) ) {
			$qry->where( 'email', 'like', '%' . $search . '%' );
		}

		$customers = $qry->paginate( $limit );

		return view( 'admin.customer.index', compact( 'customers' ) );
	}

	public function create() {
		return view( 'admin.customer.form' );
	}

	public function store( Request $request ) {
		$request->validate( [ 
			'email' => [ 'required', 'string', 'email', 'max:255', 'unique:users' ],
			'first_name' => [ 'required', 'string', 'max:255' ],
			'last_name' => [ 'required', 'string', 'max:255' ],
		] );

		$password = Str::random( 10 );

		$customer = User::create( [ 
			'email' => $request->email,
			'password' => Hash::make( $password ),
			'first_name' => $request->first_name,
			'last_name' => $request->last_name,
			'job_title' => $request->job_title,
			'department' => $request->department,
			'company_name' => $request->company_name,
			'phone' => $request->phone,
		] );

		$customer->assignRole( 'customer' );

		Mail::to( $request->email )->send( new NewUserWelcome( $request->email, $request->first_name, $password ) );

		return redirect()->route( 'customer.index' )->with( 'success', 'Customer added Successfully. Credentials sent to email.' );
	}

	public function edit( $id ) {
		$customer = User::findOrFail( $id );
		return view( 'admin.customer.form', compact( 'customer' ) );
	}

	public function update( Request $request, $id ) {
		$customer = User::findOrFail( $id );

		$request->validate( [ 
			'email' => [ 'required', 'string', 'max:255', 'email', 'unique:users,email,' . $customer->id ],
			'password' => [ 'nullable', 'string', 'min:8', 'confirmed' ],
			'first_name' => [ 'required', 'string', 'max:255' ],
			'last_name' => [ 'required', 'string', 'max:255' ],
		] );

		$requestData = $request->except( [ '_token', 'method' ] );

		if ( $request->password ) {
			$requestData['password'] = Hash::make( $request->password );
		} else {
			unset( $requestData['password'] );
		}

		$customer->update( $requestData );

		return redirect()->route( 'customer.index' )->with( 'success', 'Customer updated Successfully.' );
	}

	public function destroy( $id ) {
		$customer = User::findOrFail( $id );
		$customer->delete();
		return redirect()->route( 'customer.index' )->with( 'delete', 'Customer deleted Successfully.' );
	}
}
