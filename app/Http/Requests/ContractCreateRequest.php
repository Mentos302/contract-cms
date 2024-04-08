<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContractCreateRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 */
	public function authorize(): bool {
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
	 */
	public function rules(): array {
		return [ 
			'type_id' => 'required|exists:types,id',
			'manufacturer_id' => 'required|exists:manufacturers,id',
			'distributor_id' => 'nullable',
			'term_id' => 'required|exists:terms,id',
			'start_date' => 'required',
			'end_date' => 'required',
			'location' => 'required',
			'contract_price' => 'nullable',
			'contract_cost' => 'nullable',
			'status' => 'nullable',
			'contract_progress' => 'nullable',
		];
	}
}
