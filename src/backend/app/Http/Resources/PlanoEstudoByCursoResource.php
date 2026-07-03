<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanoEstudoByCursoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $ectsSum = 0;
        if ($this->relationLoaded('planoUcs')) {
            foreach ($this->planoUcs as $planoUc) {
                if ($planoUc->relationLoaded('uc') && $planoUc->uc) {
                    $ectsSum += $planoUc->uc->ects;
                }
            }
        }

        return [
            'id' => $this->id,
            'ano' => $this->ano,
            'ucs' => count($this->whenLoaded('planoUcs')),
            'ects' => $ectsSum
        ];
    }
}
