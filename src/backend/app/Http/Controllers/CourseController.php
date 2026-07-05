<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Http\Resources\CursoResource;
use App\Http\Resources\PlanoEstudoByCursoResource;
use App\Http\Resources\PlanoEstudoResource;
use App\Http\Resources\UcResource;
use App\Models\Curso;
use App\Models\PlanoEstudo;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CourseController extends Controller implements HasMiddleware
{
    use AuthorizesRequests;
    public static function middleware(): array
    {
        return [
            new Middleware('can:viewAny,' . Curso::class, only: ['index']),
            new Middleware('can:view,curso', only: ['show']),
            new Middleware('can:create,' . Curso::class, only: ['store']),
            new Middleware('can:update,curso', only: ['update']),
            new Middleware('can:delete,curso', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $request->validate([
            'with_planos_estudo' => 'bool',
            'search' => 'nullable|string',
        ]);


        $cursos = Curso::query()
            ->orderByDesc('id')
            ->when($request->with_planos_estudo, function ($query) {
                $query->with('planoEstudos.planoUcs.uc');
            })
            ->when($request->search, function ($query, $search) {
                $query->where('nome', 'like', "%{$search}%")
                    ->orwhere('name', 'like', "%{$search}%")
                    ->orWhere('sigla', 'like', "%{$search}%")
                    ->orWhere('codigo', 'like', "%{$search}%");
            })
            ->get();

        return response()->json([
            'data' => CursoResource::collection($cursos),
        ], 200);
    }

    public function show(Curso $curso)
    {
        return response()->json([
            'data' => CursoResource::make($curso),
        ], 200);
    }

    public function store(StoreCourseRequest $request)
    {
        $curso = Curso::create($request->validated());

        return response()->json([
            'message' => 'Curso criado com sucesso!',
            'data' => CursoResource::make($curso),
        ], 201);
    }

    public function update(UpdateCourseRequest $request, Curso $curso)
    {
        $curso->update($request->validated());

        return response()->json([
            'message' => 'Curso atualizado com sucesso!',
            'data' => CursoResource::make($curso),
        ], 200);
    }

    public function destroy(Curso $curso)
    {
        $curso->delete();

        return response()->json([
            'message' => 'Curso deletado com sucesso!',
        ], 200);
    }

    public function getLatestPlan(Curso $curso)
    {
        $plano = PlanoEstudo::where('id_curso', $curso->id)
            ->with(['planoUcs.uc'])
            ->orderBy('ano', 'desc')
            ->first();

        if  (!$plano) {
            return response()->json([
                'message' => 'Nenhum plano de estudo encontrado para este curso.',
            ], 404);
        }

        return response()->json([
            'data' => PlanoEstudoResource::make($plano),
        ], 200);

    }

    public function ucs(Curso $curso)
    {
        $ucs = $curso->ucs()->with('planoUcs')->get();
        return response()->json([
            'data' => UcResource::collection($ucs),
        ], 200);
    }

    public function planos(Curso $curso)
    {
        $planos = $curso->planoEstudos()
            ->with(['planoUcs', 'planoUcs.uc'])
            ->orderByDesc('ano')
            ->get();

        return response()->json([
            'data' => PlanoEstudoByCursoResource::collection($planos),
        ], 200);
    }
}
