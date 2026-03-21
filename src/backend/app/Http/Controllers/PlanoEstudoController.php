<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlanoEstudoRequest;
use App\Http\Requests\UpdatePlanoEstudoRequest;
use App\Models\PlanoEstudo;
use Illuminate\Http\Request;
use App\Http\Resources\PlanoEstudoResource;

class PlanoEstudoController extends Controller
{
    public function index()
    {
        $planos = PlanoEstudo::query()
            ->with(['curso:id,nome,sigla,codigo'])
            ->select('id', 'ano', 'id_curso')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'data' => PlanoEstudoResource::collection($planos),
        ], 200);
    }

    public function show(PlanoEstudo $planosEstudo)
    {
        $planosEstudo->load([
            'curso:id,nome,sigla,codigo',
            'planoUcs' => fn ($q) => $q
                ->select('id', 'id_plano', 'id_uc')
                ->withExists(['opcoes as opcional']), // add opcional attribute to each planoUc
            'planoUcs.uc:id,nome,sigla,ects,id_curso',
        ]);

        return response()->json([
            'data' => PlanoEstudoResource::make($planosEstudo),
        ], 200);
    }

    public function store(StorePlanoEstudoRequest $request)
    {
        $planosEstudo = PlanoEstudo::create($request->validated());

        return response()->json([
            'message' => 'Plano de estudo criado com sucesso!',
            'data' => PlanoEstudoResource::make($planosEstudo),
        ], 201);
    }

    public function update(UpdatePlanoEstudoRequest $request, PlanoEstudo $planosEstudo)
    {
        $planosEstudo->update($request->validated());

        return response()->json([
            'message' => 'Plano de estudo atualizado com sucesso!',
            'data' => PlanoEstudoResource::make($planosEstudo),
        ], 200);
    }

    public function destroy(PlanoEstudo $planosEstudo)
    {
        $planosEstudo->delete();

        return response()->json([
            'message' => 'Plano de estudo deletado com sucesso!',
        ], 200);
    }
}
