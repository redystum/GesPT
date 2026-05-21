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
            'ano' => $this->ano,
            'horas' => $this->horas,
            'horas_t' => $this->horas_t,
            'horas_tp' => $this->horas_tp,
            'horas_pl' => $this->horas_pl,
            'horas_tc' => $this->horas_tc,
            'horas_s' => $this->horas_s,
            'horas_e' => $this->horas_e,
            'horas_ot' => $this->horas_ot,
            'horas_o' => $this->horas_o,
            'semestre' => $this->semestre,
            'id_curso' => $this->id_curso,
            'curso' => CursoResource::make($this->whenLoaded('curso')),
        ];
    }
}
