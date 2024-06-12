<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RenewalCreateRequest extends FormRequest {
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
			'contract_id' => 'required|exists:contracts,id',
			'status' => 'required|in:Open,Close won,Close lost',
			'year' => 'nullable|integer',
			'quote_number' => 'required|string',
			'quote_file' => 'nullable|file|mimes:pdf',
			'purchase_order_number' => 'nullable|integer',
			'po_file' => 'nullable|file|mimes:pdf',
			'invoice_number' => 'nullable|integer',
			'invoice_file' => 'nullable|file|mimes:pdf',
		];
	}
}
