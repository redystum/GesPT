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
            'data' => $planos->map(function ($planosEstudo) {
                return [
                    'id' => $planosEstudo->id,
                    'ano' => $planosEstudo->ano,
                    'curso' => $planosEstudo->curso ? [
                        'id' => $planosEstudo->curso->id,
                        'nome' => $planosEstudo->curso->nome,
                        'sigla' => $planosEstudo->curso->sigla,
                        'codigo' => $planosEstudo->curso->codigo,
                    ] : null,
                ];
            })->values(),
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
            'data' => [
                'id' => $planosEstudo->id,
                'ano' => $planosEstudo->ano,
                'curso' => $planosEstudo->curso ? [
                    'id' => $planosEstudo->curso->id,
                    'nome' => $planosEstudo->curso->nome,
                    'sigla' => $planosEstudo->curso->sigla,
                    'codigo' => $planosEstudo->curso->codigo,
                ] : null,
                'ucs' => $planosEstudo->planoUcs
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

        $planosEstudo = PlanoEstudo::create($validated);

        return response()->json([
            'message' => 'Plano de estudo criado com sucesso!',
            'data' => PlanoEstudoResource::make($planosEstudo),
        ], 201);
    }

    public function update(Request $request, PlanoEstudo $planosEstudo)
    {
        $validated = $request->validate([
            'ano' => ['required', 'string', 'max:9'],
            'id_curso' => ['required', 'integer', 'exists:cursos,id'],
        ]);

        $planosEstudo->update($validated);

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
