<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlanoEstudoRequest;
use App\Http\Requests\UpdatePlanoEstudoRequest;
use App\Models\PlanoEstudo;
use Illuminate\Http\Request;
use App\Http\Resources\PlanoEstudoResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Exports\PlanoEstudoTemplateExport;
use App\Imports\PlanoEstudoTemplateImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Curso;
use App\Services\PlanoEstudoTemplateValidator;

class PlanoEstudoController extends Controller implements HasMiddleware
{
    use AuthorizesRequests;

    public static function middleware(): array
    {
        return [
            new Middleware('can:viewAny,' . PlanoEstudo::class, only: ['index']),
            new Middleware('can:view,planoEstudo', only: ['show']),
            new Middleware('can:create,' . PlanoEstudo::class, only: ['store']),
            new Middleware('can:update,planoEstudo', only: ['update']),
            new Middleware('can:delete,planoEstudo', only: ['destroy']),
        ];
    }

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

    public function exportTemplate(Curso $curso)
    {
        return Excel::download(
            new PlanoEstudoTemplateExport($curso),
            'plano_estudo_template_' . $curso->codigo . '.xlsx'
        );
    }

    public function importTemplate(Request $request, Curso $curso)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls'],
        ]);

        $import = new PlanoEstudoTemplateImport($curso);
        Excel::import($import, $request->file('file'));

        $validator = PlanoEstudoTemplateValidator::make($import->data, $curso);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'O ficheiro Excel contém dados inválidos.',
                'errors' => $validator->errors(),
            ], 422);
        }

        return response()->json([
            'message' => 'Plano de estudo importado com sucesso!',
            'data' => $import->data,
        ], 200);
    }
}
