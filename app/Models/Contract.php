<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model {
	use HasFactory;

	protected $fillable = [ 
		'customer_id',
		'type_id',
		'manufacturer_id',
		'distributor_id',
		'term_id',
		'start_date',
		'end_date',
		'status',
		'location',
		'serial_number',
		'mfr_contract_number',
		'name',
		'contract_price',
		'contract_cost',
		'contract_progress',
		'contract_owner'
	];

	protected $dates = [ 
		'start_date' => 'datetime',
		'end_date' => 'datetime'
	];

	public function customer() {
		return $this->belongsTo( User::class, 'customer_id' );
	}

	public function type() {
		return $this->belongsTo( Type::class, 'type_id' );
	}

	public function manufacturer() {
		return $this->belongsTo( Manufacturer::class, 'manufacturer_id' );
	}

	public function distributor() {
		return $this->belongsTo( Distributor::class, 'distributor_id' );
	}

	public function term() {
		return $this->belongsTo( Term::class, 'term_id' );
	}

	public function renewals() {
		return $this->hasMany( Renewal::class);
	}

	public function histories() {
		return $this->hasMany( ContractHistory::class);
	}
}
