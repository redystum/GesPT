<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUcRequest;
use App\Http\Requests\UpdateUcRequest;
use App\Http\Resources\UcResource;
use App\Models\PlanoUc;
use Illuminate\Http\Request;
use App\Models\Uc;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UnidadeCurricularController extends Controller implements HasMiddleware
{
    use AuthorizesRequests;

    public static function middleware(): array
    {
        return [
            new Middleware('can:viewAny,' . Uc::class, only: ['index']),
            new Middleware('can:view,uc', only: ['show']),
            new Middleware('can:create,' . Uc::class, only: ['store']),
            new Middleware('can:update,uc', only: ['update']),
            new Middleware('can:delete,uc', only: ['destroy']),
        ];
    }

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
        $uc->load(['curso:id,nome,sigla,codigo', 'planoUcs.opcoes.ucEscolhida']);

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

        if ($request->has('opcoes')) {
            $opcaoIds = $request->input('opcoes') ?? [];
            $planoUcIds = \App\Models\PlanoUc::where('id_uc', $uc->id)->pluck('id');

            \Illuminate\Support\Facades\DB::transaction(function () use ($planoUcIds, $opcaoIds) {
                // Delete old options for these plano_ucs
                \App\Models\Opcoes::whereIn('id_uc_opcional', $planoUcIds)->delete();

                // Create new options
                foreach ($planoUcIds as $planoUcId) {
                    foreach ($opcaoIds as $opcaoId) {
                        \App\Models\Opcoes::create([
                            'id_uc_opcional' => $planoUcId,
                            'id_uc' => $opcaoId
                        ]);
                    }
                }
            });
        }

        $uc->load(['curso:id,nome,sigla,codigo', 'planoUcs.opcoes.ucEscolhida']);

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
