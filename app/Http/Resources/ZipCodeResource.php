<?php

namespace App\Http\Resources;

use App\Http\Resources\SettlementResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ZipCode",
 *     @OA\Property(property="zip_code", type="string"),
 *     @OA\Property(property="locality", type="string"),
 *     @OA\Property(property="federal_entity",
 *         type="object",
 *         @OA\Property(property="key", type="integer", format="int32"),
 *         @OA\Property(property="name", type="string"),
 *         @OA\Property(property="code", type="string", nullable=true)
 *     ),
 *     @OA\Property(property="settlements", type="array", @OA\Items(ref="#/components/schemas/Settlement")),
 *     @OA\Property(property="municipality",
 *         type="object",
 *         @OA\Property(property="key", type="integer", format="int32"),
 *         @OA\Property(property="name", type="string")
 *     )
 * )
 */
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
