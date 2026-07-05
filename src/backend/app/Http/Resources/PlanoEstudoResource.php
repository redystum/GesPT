<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanoEstudoResource extends JsonResource
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
            'ano' => $this->ano,
            'id_curso' => $this->when(
                ! $this->relationLoaded('curso'),
                $this->id_curso
            ),
            'curso' => $this->whenLoaded('curso', function () {
                return $this->curso ? new CursoResource($this->curso) : null;
            }),
            'ects' => $this->when($this->relationLoaded('planoUcs'), function () {
                $ectsSum = 0;
                foreach ($this->planoUcs as $planoUc) {
                    if ($planoUc->relationLoaded('uc') && $planoUc->uc) {
                        $ectsSum += $planoUc->uc->ects;
                    }
                }
                return $ectsSum;
            }),
            'ucs' => $this->whenLoaded('planoUcs', function () {
                return PlanoUcResource::collection(
                    $this->planoUcs
                        ->filter(fn ($planoUc) => $planoUc->uc !== null)
                        ->values()
                );
            })
        ];
    }
}
