<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="Settlement",
 *     @OA\Property(property="key", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="zone_type", type="string"),
 *     @OA\Property(property="settlement_type",
 *         type="object",
 *         @OA\Property(property="name", type="string")
 *     )
 * )
 */
class SettlementResource extends JsonResource
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
            'key' => $this->key,
            'name' => $this->name,
            'zone_type' => $this->zone_type,
            'settlement_type' => [
                'name' => $this->settlement_type['name'] ?? null,
            ],
        ];
    }
}
