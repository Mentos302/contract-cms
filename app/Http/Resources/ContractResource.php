<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContractResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type->name,
            'manufacturer' => $this->manufacturer->name,
            'distributor' => $this->distributor->name,
            'term' => $this->term->name,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'number' => $this->number,
            'location' => $this->location,
            'contract_price' => $this->contract_price,
            'created_at' => $this->created_at,
        ];
    }
}
