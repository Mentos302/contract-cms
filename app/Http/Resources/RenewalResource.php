<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RenewalResource extends JsonResource {
	/**
	 * Transform the resource into an array.
	 *
	 * @param  Request  $request
	 * @return array<string, mixed>
	 */
	public function toArray( $request ): array {
		return [ 
			'id' => $this->id,
			'contract' => new ContractResource( $this->contract ),
			'status' => $this->status,
			'created_at' => $this->created_at,
		];
	}
}
