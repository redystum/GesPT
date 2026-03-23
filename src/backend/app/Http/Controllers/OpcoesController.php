<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreOpcoesRequest;
use App\Http\Resources\OpcoesResource;
use App\Models\Opcoes;

class OpcoesController extends Controller
{
    public function store(StoreOpcoesRequest $request){
        $opcoes = Opcoes::create($request->validated());

        return response()->json([
            'message' => 'Opção criada com sucesso!',
            'data' => OpcoesResource::make($opcoes),
        ], 201);
    }

    public function update(StoreOpcoesRequest $request, Opcoes $opcoes){
        $opcoes->update($request->validated());

        return response()->json([
            'message' => 'Opção atualizada com sucesso!',
            'data' => OpcoesResource::make($opcoes),
        ], 200);
    }

    public function destroy(Opcoes $opcoes){
        $opcoes->delete();

        return response()->json([
            'message' => 'Opção eliminada com sucesso!',
        ], 200);
    }
}
