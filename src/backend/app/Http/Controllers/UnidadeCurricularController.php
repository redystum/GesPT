<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Uc;

class UnidadeCurricularController extends Controller
{
    public function index()
    {
        $ucs = Uc::query()
            ->with('curso:id,nome,sigla,codigo')
            ->select('id', 'nome', 'sigla', 'ects', 'horas', 'id_curso')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'data' => $ucs->map(function ($uc) {
                return [
                    'id' => $uc->id,
                    'nome' => $uc->nome,
                    'sigla' => $uc->sigla,
                    'ects' => $uc->ects,
                    'horas' => $uc->horas,
                    'curso' => $uc->curso ? [
                        'id' => $uc->curso->id,
                        'nome' => $uc->curso->nome,
                        'sigla' => $uc->curso->sigla,
                        'codigo' => $uc->curso->codigo,
                    ] : null,
                ];
            })->values(),
        ], 200);
    }

    public function show(Uc $uc)
    {
        $uc->load('curso:id,nome,sigla,codigo');

        return response()->json([
            'data' => [
                'id' => $uc->id,
                'nome' => $uc->nome,
                'sigla' => $uc->sigla,
                'ects' => $uc->ects,
                'horas' => $uc->horas,
                'curso' => $uc->curso ? [
                    'id' => $uc->curso->id,
                    'nome' => $uc->curso->nome,
                    'sigla' => $uc->curso->sigla,
                    'codigo' => $uc->curso->codigo,
                ] : null,
            ],
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'sigla' => ['required', 'string', 'max:20', 'unique:ucs,sigla'],
            'ects' => ['required', 'integer', 'min:1'],
            'horas' => ['required', 'integer', 'min:1'],
            'id_curso' => ['required', 'integer', 'exists:cursos,id'],
        ]);

        $uc = Uc::create($validated);

        return response()->json([
            'message' => 'Unidade curricular criada com sucesso!',
            'data' => [
                'id' => $uc->id,
                'nome' => $uc->nome,
                'sigla' => $uc->sigla,
                'ects' => $uc->ects,
                'horas' => $uc->horas,
                'id_curso' => $uc->id_curso,
            ],
        ], 201);
    }

    public function update(Request $request, Uc $uc)
    {
        $validated = $request->validate([
            'nome' => ['sometimes', 'string', 'max:255'],
            'sigla' => ['sometimes', 'string', 'max:20', 'unique:ucs,sigla,' . $uc->id],
            'ects' => ['sometimes', 'integer', 'min:1'],
            'horas' => ['sometimes', 'integer', 'min:1'],
            'id_curso' => ['sometimes', 'integer', 'exists:cursos,id'],
        ]);

        $uc->update($validated);

        return response()->json([
            'message' => 'Unidade curricular atualizada com sucesso!',
            'data' => [
                'id' => $uc->id,
                'nome' => $uc->nome,
                'sigla' => $uc->sigla,
                'ects' => $uc->ects,
                'horas' => $uc->horas,
                'id_curso' => $uc->id_curso,
            ],
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
