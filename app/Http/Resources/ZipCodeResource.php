<?php

namespace App\Http\Resources;

use App\Http\Resources\SettlementResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ZipCodeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'zip_code' => $this->zip_code,
            'locality' => $this->locality['name'] ?? null,
            'federal_entity' => [
                'key' => $this->federal_entity['key'] ?? null,
                'name' => $this->federal_entity['name'] ?? null,
                'code' => null,
            ],
            'settlements' => SettlementResource::collection($this->settlements),
            'municipality' => [
                'key' => $this->municipality['key'] ?? null,
                'name' => $this->municipality['name'] ?? null,
            ],
        ];
    }
}
