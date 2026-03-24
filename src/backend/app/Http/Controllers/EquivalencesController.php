<?php

namespace App\Http\Controllers;

use App\Models\Equivalencia;
use App\Models\EquivalenciaRule;
use App\Models\Estudante;
use App\Models\OpcoesEstudante;
use App\Models\PlanoEstudo;
use App\Models\PlanoUc;
use Illuminate\Http\Request;

class EquivalencesController extends Controller
{
    // http://localhost:8000/api/home?estudante_id=1&plano_inicial=1&plano_final=2
    public function index(Request $request)
    {
        $request->validate([
            'estudante_id' => 'required|integer|exists:estudantes,id',
            'plano_inicial' => 'required|integer|exists:plano_estudos,id',
            'plano_final' => 'required|integer|exists:plano_estudos,id',
            'dd' => 'nullable|bool'
        ]);

        $planoInicial = PlanoEstudo::where('id', $request->plano_inicial)
            ->with(['planoUcs', 'planoUcs.uc'])
            ->first();
        $planoFinal = PlanoEstudo::where('id', $request->plano_final)
            ->with(['planoUcs', 'planoUcs.uc'])
            ->first();

        $opInicial = OpcoesEstudante::where('id_estudante', $request->estudante_id)
            ->join('opcoes', 'opcoes_estudante.id_opcao', '=', 'opcoes.id')
            ->join('plano_ucs', 'opcoes.id_uc_opcional', '=', 'plano_ucs.id')
            ->where('plano_ucs.id_plano', $planoInicial->id)
            ->with(['opcao', 'opcao.ucEscolhida'])
            ->get();

        $opFinal = OpcoesEstudante::where('id_estudante', $request->estudante_id)
            ->join('opcoes', 'opcoes_estudante.id_opcao', '=', 'opcoes.id')
            ->join('plano_ucs', 'opcoes.id_uc_opcional', '=', 'plano_ucs.id')
            ->where('plano_ucs.id_plano', $planoFinal->id)
            ->with(['opcao', 'opcao.ucEscolhida'])
            ->get();

        $planoInicial->planoUcs->each(function ($planoUc) use ($opInicial) {
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

        $planoFinal->planoUcs->each(function ($planoUc) use ($opFinal) {
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

        $rules_in_plano = EquivalenciaRule::where('id_plano', $planoFinal->id)
            ->with('equivalencias')
            ->get();

        $response = [];

        $equivalences = [];
        $uc_w_eq = [];
        $planoFinal->planoUcs->each(function ($planoUc) use ($rules_in_plano, &$equivalences, $planoInicial, &$uc_w_eq) {

            /** @var PlanoUc $d_eq */
            $d_eq = $planoInicial->planoUcs->where('id_uc', $planoUc->id_uc)->first();
            $eq = $rules_in_plano->map(function ($rule) use ($planoUc) {
                $have_uc = $rule->equivalencias->map(function ($equivalencia) use ($planoUc) {
                    return $equivalencia->lado == Equivalencia::LADO_FINAL && $equivalencia->id_plano_uc == $planoUc->id;
                })->filter()->values()->toArray();

                if (!empty($have_uc)) {
                    return $rule->equivalencias->load('plano_uc.uc')->toArray();
                }
            })->filter()->values();

            if ($eq->isEmpty()) {
                if ($d_eq) {
                    $d_eq_w_uc = [...$d_eq->toArray(), 'uc' => $planoUc->uc->toArray()];
                    $equivalences[] = [
                        'uc' => $planoUc->uc->toArray(),
                        'inicial' => [$d_eq_w_uc],
                        'final' => $planoUc->makeHidden('uc')->toArray(),
                    ];
                    $uc_w_eq[] = $planoUc->id_uc;
                } else {
                    $equivalences[] = [
                        'uc' => $planoUc->uc->toArray(),
                        'inicial' => null,
                        'final' => $planoUc->makeHidden('uc')->toArray(),
                    ];
                    $uc_w_eq[] = $planoUc->id_uc;
                }
            }

            if (in_array($planoUc->uc->id, $uc_w_eq)) {
                return;
            }

            $inicial = [];
            if ($d_eq) {
                $d_eq_w_uc = [...$d_eq->toArray(), 'uc' => $planoUc->uc->toArray()];
                $inicial[] = $d_eq_w_uc;
            }

            $eq->each(function ($equivalencia) use (&$inicial, $planoInicial, &$uc_w_eq) {
                $origins = collect($equivalencia)->where('lado', Equivalencia::LADO_ORIGINAL);
                $origins->each(function ($origin) use (&$inicial, $planoInicial, &$uc_w_eq) {
                    $plano_uc = $origin['plano_uc'];
                    $uc_w_eq[] = $plano_uc['id_uc'] ?? null;
                    if ($plano_uc && $plano_uc['id_plano'] == $planoInicial->id) {
                        $_eq = collect($plano_uc)->except('plano')->toArray();
                        $d_eq_w_uc = [...$_eq, 'uc' => $plano_uc['uc']];
                        $inicial[] = $d_eq_w_uc;
                    }
                });
            });

            $equivalences[] = [
                'uc' => $planoUc->uc->toArray(),
                'inicial' => $inicial,
                'final' => $planoUc->makeHidden('uc')->toArray(),
            ];

            if (!$eq->isEmpty() || $d_eq) {
                $uc_w_eq[] = $planoUc->id_uc;
            }
        });

        $planoInicial->planoUcs->whereNotIn('id_uc', $uc_w_eq)->each(function ($planoUc) use (&$equivalences) {
            $equivalences[] = [
                'uc' => $planoUc->uc->toArray(),
                'inicial' => [$planoUc->makeHidden('uc')->toArray()],
                'final' => null,
            ];
        });
        $response['equivalences'] = $equivalences;

        // endregion

        // endregion
        if ($request->dd)
            dd($response);

        return response()->json($response);
    }
}
