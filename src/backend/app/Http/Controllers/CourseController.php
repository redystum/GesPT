<?php

namespace App\Http\Controllers;

use App\Http\Resources\CursoResource;
use App\Models\Curso;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $cursos = Curso::all();

        return response()->json([
            'data' => CursoResource::collection($cursos),
        ], 200);
    }

    public function show(Curso $course)
    {
        return response()->json([
            'data' => CursoResource::make($course),
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

    public function update(Request $request, Curso $course)
    {
        $validated = $request->validate([
            'nome' => ['sometimes', 'string', 'max:255'],
            'sigla' => ['sometimes', 'string', 'max:20', 'unique:cursos,sigla,' . $course->id],
            'codigo' => ['sometimes', 'string', 'max:10', 'unique:cursos,codigo,' . $course->id],
        ]);

        $course->update($validated);

        return response()->json([
            'message' => 'Curso atualizado com sucesso!',
            'data' => CursoResource::make($course),
        ], 200);
    }

    public function destroy(Curso $course)
    {
        $course->delete();

        return response()->json([
            'message' => 'Curso deletado com sucesso!',
        ], 200);
    }
}
