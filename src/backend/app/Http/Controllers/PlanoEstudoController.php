<?php

namespace App\Http\Controllers;

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
            'data' => $planos->map(function ($planoEstudos) {
                return [
                    'id' => $planoEstudos->id,
                    'ano' => $planoEstudos->ano,
                    'curso' => $planoEstudos->curso ? [
                        'id' => $planoEstudos->curso->id,
                        'nome' => $planoEstudos->curso->nome,
                        'sigla' => $planoEstudos->curso->sigla,
                        'codigo' => $planoEstudos->curso->codigo,
                    ] : null,
                ];
            })->values(),
        ], 200);
    }

    public function show(PlanoEstudo $planoEstudo)
    {
        $planoEstudo->load([
            'curso:id,nome,sigla,codigo',
            'planoUcs' => fn ($q) => $q
                ->select('id', 'id_plano', 'id_uc')
                ->withExists(['opcoes as opcional']), // add opcional attribute to each planoUc
            'planoUcs.uc:id,nome,sigla,ects,id_curso',
        ]);

        return response()->json([
            'data' => [
                'id' => $planoEstudo->id,
                'ano' => $planoEstudo->ano,
                'curso' => $planoEstudo->curso ? [
                    'id' => $planoEstudo->curso->id,
                    'nome' => $planoEstudo->curso->nome,
                    'sigla' => $planoEstudo->curso->sigla,
                    'codigo' => $planoEstudo->curso->codigo,
                ] : null,
                'ucs' => $planoEstudo->planoUcs
                    ->filter(fn ($planoUc) => $planoUc->uc !== null) // keep only planoUcs that have a related uc
                    ->map(function ($planoUc) {
                        return [
                            'id' => $planoUc->uc->id,
                            'nome' => $planoUc->uc->nome,
                            'sigla' => $planoUc->uc->sigla,
                            'ects' => $planoUc->uc->ects,
                            'opcional' => (bool) $planoUc->opcional,
                        ];
                    })
                    ->values(),
            ],
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ano' => ['required', 'string', 'max:9'],
            'id_curso' => ['required', 'integer', 'exists:cursos,id'],
        ]);

        $planoEstudo = PlanoEstudo::create($validated);

        return response()->json([
            'message' => 'Plano de estudo criado com sucesso!',
            'data' => PlanoEstudoResource::make($planoEstudo),
        ], 201);
    }

    public function update(Request $request, PlanoEstudo $planoEstudo)
    {
        $validated = $request->validate([
            'ano' => ['required', 'string', 'max:9'],
            'id_curso' => ['required', 'integer', 'exists:cursos,id'],
        ]);

        $planoEstudo->update($validated);

        return response()->json([
            'message' => 'Plano de estudo atualizado com sucesso!',
            'data' => PlanoEstudoResource::make($planoEstudo),
        ], 201);
    }

    public function destroy(PlanoEstudo $planoEstudo)
    {
        $planoEstudo->delete();

        return response()->json([
            'message' => 'Plano de estudo deletado com sucesso!',
        ], 200);
    }
}
