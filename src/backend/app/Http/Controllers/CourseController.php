<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Http\Resources\CursoResource;
use App\Models\Curso;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'with_planos_estudo' => 'bool',
            'search' => 'nullable|string',
        ]);


        $cursos = Curso::query()
            ->orderByDesc('id')
            ->when($request->with_planos_estudo, function ($query) {
                $query->with('planoEstudos');
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
}
