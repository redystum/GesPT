<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUcRequest;
use App\Http\Requests\UpdateUcRequest;
use App\Http\Resources\UcResource;
use App\Models\PlanoUc;
use Illuminate\Http\Request;
use App\Models\Uc;

class UnidadeCurricularController extends Controller
{
    public function index(Request $request)
    {
        $query = Uc::query()->with('curso:id,nome,sigla,codigo');

        if ($request->has('curso_id')) {
            $query->where('id_curso', $request->curso_id);
        }

        $ucs = $query->orderByDesc('id')->get();

        return response()->json([
            'data' => UcResource::collection($ucs),
        ], 200);
    }

    public function show(Uc $uc)
    {
        $uc->load('curso:id,nome,sigla,codigo');

        return response()->json([
            'data' => UcResource::make($uc),
        ], 200);
    }

    public function store(StoreUcRequest $request)
    {
        $uc = Uc::create([
            'nome' => $request->nome,
            'name' => $request->name,
            'sigla' => $request->sigla,
            'ects' => $request->ects,
            'ano' => $request->ano,
            'semestre' => $request->semestre,
            'id_curso' => $request->id_curso,
            'horas' => $request->horas ?? 0,
            'horas_t' => $request->t ?? 0,
            'horas_tp' => $request->tp ?? 0,
            'horas_pl' => $request->pl ?? 0,
            'horas_tc' => $request->tc ?? 0,
            'horas_s' => $request->s ?? 0,
            'horas_e' => $request->e ?? 0,
            'horas_ot' => $request->ot ?? 0,
            'horas_o' => $request->o ?? 0,
        ]);

        return response()->json([
            'message' => 'Unidade curricular criada com sucesso!',
            'data' => UcResource::make($uc),
        ], 201);
    }

    public function update(UpdateUcRequest $request, Uc $uc)
    {
        $uc->update($request->validated());

        return response()->json([
            'message' => 'Unidade curricular atualizada com sucesso!',
            'data' => UcResource::make($uc),
        ], 200);
    }

    public function destroy(Uc $uc)
    {
        $uc->delete();

        return response()->json([
            'message' => 'Unidade curricular deletada com sucesso!',
        ], 200);
    }
}
