<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreOpcoesRequest;
use App\Http\Requests\UpdateOpcoesRequest;
use App\Http\Resources\OpcoesResource;
use App\Models\Opcoes;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class OpcoesController extends Controller implements HasMiddleware
{
    use AuthorizesRequests;

    public static function middleware(): array
    {
        return [
            new Middleware('can:create,' . Opcoes::class, only: ['store']),
            new Middleware('can:update,opcoao', only: ['update']),
            new Middleware('can:delete,opcoao', only: ['destroy']),
        ];
    }

    public function store(StoreOpcoesRequest $request){
        $opcoes = Opcoes::create($request->validated());

        return response()->json([
            'message' => 'Opção criada com sucesso!',
            'data' => OpcoesResource::make($opcoes),
        ], 201);
    }

    public function update(UpdateOpcoesRequest $request, Opcoes $opcoao){
        $opcoao->update($request->validated());

        return response()->json([
            'message' => 'Opção atualizada com sucesso!',
            'data' => OpcoesResource::make($opcoao),
        ], 200);
    }

    public function destroy(Opcoes $opcoao){
        $opcoao->delete();

        return response()->json([
            'message' => 'Opção eliminada com sucesso!',
        ], 200);
    }
}
