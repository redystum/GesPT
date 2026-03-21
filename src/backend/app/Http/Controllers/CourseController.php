<?php

namespace App\Http\Controllers;

use App\Http\Resources\CursoResource;
use App\Models\Curso;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $curso = Curso::all();

        return response()->json([
            'data' => CursoResource::collection($curso),
        ], 200);
    }

    public function show(Curso $curso)
    {
        return response()->json([
            'data' => CursoResource::make($curso),
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'sigla' => ['required', 'string', 'max:20', 'unique:cursos,sigla'],
            'codigo' => ['required', 'string', 'max:10', 'unique:cursos,codigo'],
        ]);

        $course = Curso::create($validated);

        return response()->json([
            'message' => 'Curso criado com sucesso!',
            'data' => CursoResource::make($course),
        ], 201);
    }

    public function update(Request $request, Curso $curso)
    {
        $validated = $request->validate([
            'nome' => ['sometimes', 'string', 'max:255'],
            'sigla' => ['sometimes', 'string', 'max:20', 'unique:cursos,sigla,' . $curso->id],
            'codigo' => ['sometimes', 'string', 'max:10', 'unique:cursos,codigo,' . $curso->id],
        ]);

        $curso->update($validated);

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
