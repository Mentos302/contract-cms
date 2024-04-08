<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Renewal extends Model {
	use HasFactory;

	protected $fillable = [ 
		'contract_id',
		'status',
		'expiring_date',
	];

	protected static function boot() {
		parent::boot();

		static::creating( function ($renewal) {
			$contract = $renewal->contract;
			if ( $contract ) {
				$renewal->expiring_date = $contract->end_date;
			}
		} );
	}

	public function contract() {
		return $this->belongsTo( Contract::class);
	}
}
