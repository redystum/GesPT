<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlanoEstudoRequest;
use App\Http\Requests\UpdatePlanoEstudoRequest;
use App\Models\PlanoEstudo;
use Illuminate\Http\Request;
use App\Http\Resources\PlanoEstudoResource;

class PlanoEstudoController extends Controller
{
    public function index(Request $request)
    {
        $query = PlanoEstudo::query()
            ->with(['curso:id,nome,sigla,codigo'])
            ->select('id', 'ano', 'id_curso')
            ->orderByDesc('id');

        if ($request->has('curso_id')) {
            $query->where('id_curso', $request->curso_id);
        }

        $planosEstudo = $query->get();

        return response()->json([
            'data' => PlanoEstudoResource::collection($planosEstudo),
        ], 200);
    }

    public function show(PlanoEstudo $planoEstudo)
    {
        $planoEstudo->load([
            'curso:id,nome,sigla,codigo',
            'planoUcs' => fn($q) => $q
                ->select('id', 'id_plano', 'id_uc')
                ->withExists(['opcoes as opcional']), // add opcional attribute to each planoUc
            'planoUcs.uc:id,nome,sigla,ects,horas,id_curso',
        ]);

        return response()->json([
            'data' => PlanoEstudoResource::make($planoEstudo),
        ], 200);
    }

    public function store(StorePlanoEstudoRequest $request)
    {
        $planoEstudo = PlanoEstudo::create($request->validated());

        return response()->json([
            'message' => 'Plano de estudo criado com sucesso!',
            'data' => PlanoEstudoResource::make($planoEstudo),
        ], 201);
    }

    public function update(UpdatePlanoEstudoRequest $request, PlanoEstudo $planoEstudo)
    {
        $planoEstudo->update($request->validated());

        return response()->json([
            'message' => 'Plano de estudo atualizado com sucesso!',
            'data' => PlanoEstudoResource::make($planoEstudo),
        ], 200);
    }

    public function destroy(PlanoEstudo $planoEstudo)
    {
        $planoEstudo->delete();

        return response()->json([
            'message' => 'Plano de estudo deletado com sucesso!',
        ], 200);
    }

    public function getUcs(PlanoEstudo $planoEstudo, Request $request)
    {
        $planoEstudo->load(['planoUcs' => function ($query) use ($request) {
            if ($request->has('semestre')) {
                $query->whereHas('uc', function ($q) use ($request) {
                    $q->where('semestre', $request->semestre);
                });
            }
        }, 'planoUcs.uc']);

        return response()->json([
            'data' => PlanoEstudoResource::make($planoEstudo),
        ]);
    }
}
