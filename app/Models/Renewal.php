<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Renewal extends Model {
	use HasFactory;

	protected $fillable = [ 
		'contract_id',
		'status',
		'year',
		'expiring_date',
		'quote_number',
		'quote_file',
		'purchase_order_number',
		'po_file',
		'invoice_number',
		'invoice_file'
	];

	public function contract() {
		return $this->belongsTo( Contract::class);
	}
}

