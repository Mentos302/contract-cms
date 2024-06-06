<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractHistory extends Model {
	use HasFactory;

	protected $fillable = [ 
		'contract_id',
		'year',
		'quote_number',
		'quote_file',
		'purchase_order_number',
		'po_file',
		'invoice_number'
	];

	public function contract() {
		return $this->belongsTo( Contract::class);
	}
}
