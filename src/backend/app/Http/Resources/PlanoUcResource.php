<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanoUcResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'opcional' => (bool) $this->opcional,
            'uc' => $this->whenLoaded('uc', function () {
                return $this->uc ? new UcResource($this->uc) : null;
            }),
        ];
    }
}
