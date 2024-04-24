<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ContractCreateRequest extends FormRequest {
	public function authorize() {
		return true;
	}

	public function rules() {
		$rules = [ 
			'customer_id' => 'required|exists:users,id',
			'type_id' => 'required|exists:types,id',
			'manufacturer_id' => 'required|exists:manufacturers,id',
			'term_id' => 'required|exists:terms,id',
			'start_date' => 'required|date',
			'end_date' => 'required|date|after:start_date',
			'serial_number' => 'nullable|string|max:255',
			'mfr_contract_number' => 'nullable|string|max:255',
			'name' => 'nullable|string|max:255',
			'contract_price' => 'required|numeric',
			'location' => 'nullable|string|max:255',
			'contract_owner' => 'required|string|max:255',
		];

		if ( Auth::user()->hasRole( 'admin' ) ) {
			$rules['distributor_id'] = 'required|exists:distributors,id';
			$rules['contract_cost'] = 'required|numeric';
		}

		return $rules;
	}
}
