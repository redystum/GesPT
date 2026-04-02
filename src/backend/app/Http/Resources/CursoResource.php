<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CursoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'nome' => $this->nome,
            'name' => $this->name,
            'sigla' => $this->sigla,
            'codigo' => $this->codigo,
        ];


        if ($this->relationLoaded('planoEstudos') && $this->planoEstudos->count() > 0) {
            $data['planos_estudo'] = PlanoEstudoResource::collection($this->planoEstudos);
        }

        return $data;
    }
}
