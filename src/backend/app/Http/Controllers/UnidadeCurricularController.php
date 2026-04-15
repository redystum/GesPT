<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUcRequest;
use App\Http\Requests\UpdateUcRequest;
use App\Http\Resources\UcResource;
use Illuminate\Http\Request;
use App\Models\Uc;

class UnidadeCurricularController extends Controller
{
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
        $uc->load('curso:id,nome,sigla,codigo');

        return response()->json([
            'data' => UcResource::make($uc),
        ], 200);
    }

    public function store(StoreUcRequest $request)
    {
        $uc = Uc::create($request->validated());

        return response()->json([
            'message' => 'Unidade curricular criada com sucesso!',
            'data' => UcResource::make($uc),
        ], 201);
    }

    public function update(UpdateUcRequest $request, Uc $uc)
    {
        $uc->update($request->validated());

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
