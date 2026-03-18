<?php

namespace App\Http\Controllers;

use App\Models\Equivalencia;
use App\Models\EquivalenciaRule;
use App\Models\Estudante;
use App\Models\OpcoesEstudante;
use App\Models\PlanoEstudo;
use Hamcrest\Matcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function Psy\debug;

class HomeController extends Controller
{

    // http://localhost:8000/api/home?estudante_id=1&plano_inicial=1&plano_final=2
    public function index(Request $request)
    {
        $request->validate([
            'estudante_id' => 'required|integer|exists:estudantes,id',
            'plano_inicial' => 'required|integer|exists:plano_estudos,id',
            'plano_final' => 'required|integer|exists:plano_estudos,id',
        ]);

        $planoInicial = PlanoEstudo::where('id', $request->plano_inicial)
            ->with(['planoUcs', 'planoUcs.uc'])
            ->first();
        $planoFinal = PlanoEstudo::where('id', $request->plano_final)
            ->with(['planoUcs', 'planoUcs.uc'])
            ->first();
        $estudante = Estudante::find($request->estudante_id);

        $opInicial = OpcoesEstudante::where('id_estudante', $estudante->id)
            ->join('opcoes', 'opcoes_estudante.id_opcao', '=', 'opcoes.id')
            ->join('plano_ucs', 'opcoes.id_uc_opcional', '=', 'plano_ucs.id')
            ->where('plano_ucs.id_plano', $planoInicial->id)
            ->with(['opcao', 'opcao.ucEscolhida'])
            ->get();


        $opFinal = OpcoesEstudante::where('id_estudante', $estudante->id)
            ->join('opcoes', 'opcoes_estudante.id_opcao', '=', 'opcoes.id')
            ->join('plano_ucs', 'opcoes.id_uc_opcional', '=', 'plano_ucs.id')
            ->where('plano_ucs.id_plano', $planoFinal->id)
            ->with(['opcao', 'opcao.ucEscolhida'])
            ->get();

        $planoInicial->planoUcs->map(function ($planoUc) use ($opInicial) {
            $op = $opInicial
                ->pluck('opcao')
                ->filter()
                ->values()->where('id_uc_opcional', $planoUc->id)
                ->first()?->toArray();

            if (!empty($op)) {
                $planoUc->opcao_escolhida = $op['uc_escolhida'];
            } else {
                $planoUc->opcao_escolhida = null;
            }
        });

        $rules_in_plano = EquivalenciaRule::where('id_plano', $planoFinal->id)
            ->with('equivalencias')
            ->get();

        $planoFinal->planoUcs->map(function ($planoUc) use ($planoInicial, $rules_in_plano, $planoFinal, $opFinal) {


            $rules = [];
            foreach ($rules_in_plano as $rule) {
                $have_uc = $rule->equivalencias->map(function ($equivalencia) use ($planoUc) {
                    return $equivalencia->lado == Equivalencia::LADO_FINAL && $equivalencia->id_plano_uc == $planoUc->id;
                })->filter()->values()->toArray();

                if (!empty($have_uc)) {

                    //! This is to verify if all the origins is the actual initial plan, but this is not necessary.... waste of time...
//                    $original_is_initial = $rule->equivalencias->map(function ($equivalencia) use ($planoInicial, $planoUc) {
//                        return $equivalencia->lado == Equivalencia::LADO_ORIGINAL &&
//                            $equivalencia->load('plano_uc') &&
//                            $equivalencia->plano_uc?->id_plano == $planoInicial->id &&
//                            $equivalencia->makeHidden('plano_uc'); // unload plano_uc
//                    })->filter()->values()->toArray();
//
//                    // only returns the trues so the amount of them needs to match the origin so we know that this can be applied
//                    if (!empty($original_is_initial) &&
//                        count($original_is_initial) == count($rule->equivalencias->where('lado', Equivalencia::LADO_ORIGINAL))
//                    ) {
//                        $rules[] = $rule->equivalencias->toArray();
//                    }

                    $rules[] = $rule->equivalencias->toArray();
                }
            }

            if (!empty($rules)) {
                $planoUc->equivalencias = $rules;
            } else {
                $planoUc->equivalencias = null;
            }

            $op = $opFinal
                ->pluck('opcao')
                ->filter()
                ->values()->where('id_uc_opcional', $planoUc->id)
                ->first()?->toArray();

            if (!empty($op)) {
                $planoUc->opcao_escolhida = $op['uc_escolhida'];
            } else {
                $planoUc->opcao_escolhida = null;
            }
        });

        $response = [];
        $response['plano_inicial'] = $planoInicial->toArray();
        $response['plano_final'] = $planoFinal->toArray();


        dd($response);
    }
}
