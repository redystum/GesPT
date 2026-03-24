<?php

namespace App\Http\Controllers;

use App\Models\Equivalencia;
use App\Models\EquivalenciaRule;
use App\Models\Estudante;
use App\Models\OpcoesEstudante;
use App\Models\PlanoEstudo;
use App\Models\PlanoUc;
use Illuminate\Http\Request;

class HomeController extends Controller
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

        $planoFinal->planoUcs->map(function ($planoUc) use ($rules_in_plano, $opFinal) {

            $rules = [];
            foreach ($rules_in_plano as $rule) {
                $have_uc = $rule->equivalencias->map(function ($equivalencia) use ($planoUc) {
                    return $equivalencia->lado == Equivalencia::LADO_FINAL && $equivalencia->id_plano_uc == $planoUc->id;
                })->filter()->values()->toArray();

                if (!empty($have_uc)) {

                    // ! This is to verify if all the origins is the actual initial plan, but this is not necessary.... waste of time...
                    /*
                    $original_is_initial = $rule->equivalencias->map(function ($equivalencia) use ($planoInicial, $planoUc) {
                        return $equivalencia->lado == Equivalencia::LADO_ORIGINAL &&
                            $equivalencia->load('plano_uc') &&
                            $equivalencia->plano_uc?->id_plano == $planoInicial->id &&
                            $equivalencia->makeHidden('plano_uc'); // unload plano_uc
                    })->filter()->values()->toArray();

                    // only returns the trues so the amount of them needs to match the origin so we know that this can be applied
                    if (!empty($original_is_initial) &&
                        count($original_is_initial) == count($rule->equivalencias->where('lado', Equivalencia::LADO_ORIGINAL))
                    ) {
                        $rules[] = $rule->equivalencias->toArray();
                    }
                    */

                    $rules[] = $rule->equivalencias->load('plano_uc.uc')->toArray();
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

        // region compare plans

        // region direct equivalences
        $direct_equivalences = [];
        $uc_w_eq = [];
        $planoFinal->planoUcs->each(function ($planoUc) use (&$direct_equivalences, $planoInicial, &$uc_w_eq) {

            /** @var PlanoUc $eq */
            $eq = $planoInicial->planoUcs->where('id_uc', $planoUc->id_uc)->first();

            if ($eq) {
                $direct_equivalences[] = [
                    'uc' => $planoUc->uc->toArray(),
                    'inicial' => $eq->makeHidden('uc')->toArray(),
                    'final' => $planoUc->makeHidden('uc')->toArray(),
                ];
                $uc_w_eq[] = $planoUc->id_uc;
            } else {
                $direct_equivalences[] = [
                    'uc' => $planoUc->uc->toArray(),
                    'inicial' => null,
                    'final' => $planoUc->makeHidden('uc')->toArray(),
                ];
            }

        });

        $planoInicial->planoUcs->whereNotIn('id_uc', $uc_w_eq)->each(function ($planoUc) use (&$direct_equivalences) {
            $direct_equivalences[] = [
                'uc' => $planoUc->uc->toArray(),
                'inicial' => $planoUc->makeHidden('uc')->toArray(),
                'final' => null,
            ];
        });
        $response['direct_equivalences'] = $direct_equivalences;
        // endregion

        // region all equivalences

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

        unset($response['plano_inicial']);
        unset($response['plano_final']);
        unset($response['direct_equivalences']);
        return response()->json($response);
    }
}
