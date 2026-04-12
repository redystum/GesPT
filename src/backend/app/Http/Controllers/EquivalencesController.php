<?php

namespace App\Http\Controllers;

use App\Models\Equivalencia;
use App\Models\EquivalenciaRule;
use App\Models\OpcoesEstudante;
use App\Models\PlanoEstudo;
use App\Models\PlanoUc;
use Illuminate\Http\Request;

class EquivalencesController extends Controller
{
    // http://localhost:8000/api/home?estudante_id=1&plano_inicial=1&plano_final=2
    public function compare(Request $request)
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

        $rules_in_plano = EquivalenciaRule::where('id_plano', $planoFinal->id)
            ->with('equivalencias.plano_uc.uc')
            ->get();

        $equivalences = [];
        $uc_w_eq = [];
        $planoFinal->planoUcs->each(function ($planoUc) use ($rules_in_plano, &$equivalences, $planoInicial, &$uc_w_eq, $opFinal) {
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


            /** @var PlanoUc $direct_eq */
            $direct_eq = $planoInicial->planoUcs->where('id_uc', $planoUc->id_uc)->first();
            $eq = $rules_in_plano->map(function ($rule) use ($planoUc) {
                $have_uc = $rule->equivalencias->map(function ($equivalencia) use ($planoUc) {
                    return $equivalencia->lado == Equivalencia::LADO_FINAL && $equivalencia->id_plano_uc == $planoUc->id;
                })->filter()->values()->toArray();

                if (!empty($have_uc)) {
                    return $rule->equivalencias->toArray();
                }
            })->filter()->values();

            if ($eq->isEmpty()) {
                if ($direct_eq) {
                    $dir_arr = $direct_eq->toArray();
                    $dir_arr['observacao'] = null;
                    $final_arr = $planoUc->toArray();
                    $final_arr['observacao'] = null;
                    $equivalences[] = [
                        'inicial' => [$dir_arr],
                        'final' => [$final_arr],
                    ];
                } else {
                    $final_arr = $planoUc->toArray();
                    $final_arr['observacao'] = null;
                    $equivalences[] = [
                        'inicial' => null,
                        'final' => [$final_arr],
                    ];
                }
                $uc_w_eq[] = $planoUc->id_uc;
            }

            if (in_array($planoUc->uc->id, $uc_w_eq)) {
                return;
            }

            $inicial = [];
            if ($direct_eq) {
                $dir_arr = $direct_eq->toArray();
                $dir_arr['observacao'] = null;
                $inicial[] = $dir_arr;
            }

            $finalUcArray = $planoUc->toArray();
            $finalUcArray['observacao'] = null;

            $eq->each(function ($equivalencia) use (&$inicial, $planoInicial, &$uc_w_eq, &$finalUcArray, $planoUc) {
                collect($equivalencia)->where('lado', Equivalencia::LADO_FINAL)->where('id_plano_uc', $planoUc->id)->each(function ($item) use (&$finalUcArray) {
                    if (!empty($item['observacao'])) {
                        $finalUcArray['observacao'] = $item['observacao'];
                    }
                });

                $origins = collect($equivalencia)->where('lado', Equivalencia::LADO_ORIGINAL);
                $origins->each(function ($origin) use (&$inicial, $planoInicial, &$uc_w_eq) {
                    $plano_uc = $origin['plano_uc'];
                    $uc_w_eq[] = $plano_uc['id_uc'] ?? null;
                    if ($plano_uc && $plano_uc['id_plano'] == $planoInicial->id) {
                        $arr = collect($plano_uc)->except('plano')->toArray();
                        $arr['observacao'] = $origin['observacao'] ?? null;
                        $inicial[] = $arr;
                    }
                });
            });

            $equivalences[] = [
                'inicial' => $inicial,
                'final' => [$finalUcArray],
            ];

            if (!$eq->isEmpty() || $direct_eq) {
                $uc_w_eq[] = $planoUc->id_uc;
            }
        });

        $planoInicial->planoUcs->whereNotIn('id_uc', $uc_w_eq)->each(function ($planoUc) use (&$equivalences) {
            $arr = $planoUc->toArray();
            $arr['observacao'] = null;
            $equivalences[] = [
                'inicial' => [$arr],
                'final' => null,
            ];
        });

        // just to normalize this because it does not take that much time and it's much simpler
        $eqs = [];
        foreach ($equivalences as $e) {
            $eqs[] = [
                'inicial' => $e['inicial'] && count($e['inicial']) > 0 ? $e['inicial'] : null,
                'final' => $e['final'] && count($e['final']) > 0 ? $e['final'] : null,
            ];
        }
        unset($equivalences);

        if ($request->dd)
            dd($eqs);

        return response()->json([
            'plans' => [
                'initial' => PlanoEstudo::find($request->plano_inicial),
                'final' => PlanoEstudo::find($request->plano_final),
            ],
            'data' => $eqs
        ]);
    }
}
