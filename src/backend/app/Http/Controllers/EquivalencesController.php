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
    private function getComparisonData(Request $request)
    {
        $request->validate([
            'plano_inicial' => 'required|integer|exists:plano_estudos,id',
            'plano_final' => 'nullable|integer|exists:plano_estudos,id',
            'estudante_id' => 'nullable|integer',
        ]);

        $planoInicial = PlanoEstudo::where('id', $request->plano_inicial)
            ->with(['planoUcs', 'planoUcs.uc', 'planoUcs.opcoes.ucEscolhida', 'curso'])
            ->first();
        if ($planoInicial) {
            $planoInicial->ects_sum = $planoInicial->planoUcs->sum(function ($pu) {
                return $pu->uc ? $pu->uc->ects : 0;
            });
        }

        $planoFinal = PlanoEstudo::where('id', $request->plano_final)
            ->with(['planoUcs', 'planoUcs.uc', 'planoUcs.opcoes.ucEscolhida', 'curso'])
            ->first();
        if ($planoFinal) {
            $planoFinal->ects_sum = $planoFinal->planoUcs->sum(function ($pu) {
                return $pu->uc ? $pu->uc->ects : 0;
            });
        }

        $estudanteId = $request->estudante_id ?? 1;

        $opInicial = OpcoesEstudante::where('id_estudante', $estudanteId)
            ->join('opcoes', 'opcoes_estudante.id_opcao', '=', 'opcoes.id')
            ->join('plano_ucs', 'opcoes.id_uc_opcional', '=', 'plano_ucs.id')
            ->where('plano_ucs.id_plano', $planoInicial->id)
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

        if (!$planoFinal) {
            $eqs = [];
            foreach ($planoInicial->planoUcs as $planoUc) {
                $arr = $planoUc->toArray();
                $arr['observacao'] = null;
                $eqs[] = [
                    'inicial' => [$arr],
                    'final' => null,
                ];
            }

            return [
                'planoInicial' => $planoInicial,
                'planoFinal' => null,
                'curso' => $planoInicial->curso,
                'data' => $eqs
            ];
        }

        $opFinal = OpcoesEstudante::where('id_estudante', $estudanteId)
            ->join('opcoes', 'opcoes_estudante.id_opcao', '=', 'opcoes.id')
            ->join('plano_ucs', 'opcoes.id_uc_opcional', '=', 'plano_ucs.id')
            ->where('plano_ucs.id_plano', $planoFinal->id)
            ->with(['opcao', 'opcao.ucEscolhida'])
            ->get();

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
            ->with(['equivalencias.plano_uc.uc', 'equivalencias.plano_uc.opcoes.ucEscolhida'])
            ->get();

        $equivalences = [];
        $uc_w_eq = [];
        
        $planoFinal->planoUcs->each(function ($planoUc) use ($rules_in_plano, &$equivalences, $planoInicial, &$uc_w_eq) {
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

        $eqs = [];
        foreach ($equivalences as $e) {
            $eqs[] = [
                'inicial' => $e['inicial'] && count($e['inicial']) > 0 ? $e['inicial'] : null,
                'final' => $e['final'] && count($e['final']) > 0 ? $e['final'] : null,
            ];
        }

        return [
            'planoInicial' => $planoInicial,
            'planoFinal' => $planoFinal,
            'curso' => $planoInicial->curso,
            'data' => $eqs
        ];
    }

    public function compare(Request $request)
    {
        $comp = $this->getComparisonData($request);
        
        if ($request->dd) {
            dd($comp['data']);
        }

        return response()->json([
            'plans' => [
                'initial' => $comp['planoInicial'],
                'final' => $comp['planoFinal'],
            ],
            'data' => $comp['data']
        ]);
    }

    public function export(Request $request)
    {
        $request->validate([
            'format' => 'required|string|in:pdf,xlsx',
        ]);

        $comp = $this->getComparisonData($request);

        $isEnglish = str_contains(strtolower($request->header('Accept-Language', 'pt')), 'en');

        if ($request->format === 'pdf') {
            $options = new \Dompdf\Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);

            $dompdf = new \Dompdf\Dompdf($options);
            $html = view('exports.compare_pdf', [
                'planoInicial' => $comp['planoInicial'],
                'planoFinal' => $comp['planoFinal'],
                'curso' => $comp['curso'],
                'data' => $comp['data'],
                'isEnglish' => $isEnglish
            ])->render();

            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();

            $filename = 'comparacao_' . $comp['curso']->sigla . '_' . str_replace('/', '_', $comp['planoInicial']->ano);
            if ($comp['planoFinal']) {
                $filename .= '_to_' . str_replace('/', '_', $comp['planoFinal']->ano);
            }
            $filename .= '.pdf';

            return response($dompdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        }

        // Excel format
        $export = new \App\Exports\ComparisonExport(
            $comp['data'],
            $comp['curso'],
            $comp['planoInicial'],
            $comp['planoFinal'],
            $isEnglish
        );

        $filename = 'comparacao_' . $comp['curso']->sigla . '_' . str_replace('/', '_', $comp['planoInicial']->ano);
        if ($comp['planoFinal']) {
            $filename .= '_to_' . str_replace('/', '_', $comp['planoFinal']->ano);
        }
        $filename .= '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download($export, $filename);
    }
}
