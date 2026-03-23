<?php

namespace App\Http\Resources;

use App\Models\Opcoes;
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
            'opcional' => Opcoes::where('id_uc_opcional', $this->id)->exists(),
            'uc' => $this->whenLoaded('uc', function () {
                return $this->uc ? new UcResource($this->uc) : null;
            }),
        ];
    }
}
