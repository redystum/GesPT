<?php

namespace App\Http\Resources;

use App\Models\Curso;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UcResource extends JsonResource
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
            'nome' => $this->nome,
            'name' => $this->name,
            'sigla' => $this->sigla,
            'ects' => $this->ects,
            'horas' => $this->horas,
            'semestre' => $this->semestre,
            'id_curso' => $this->id_curso,
            'curso' => CursoResource::make($this->whenLoaded('curso')),
        ];
    }
}
