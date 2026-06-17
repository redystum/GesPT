<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWizardRequest;
use App\Models\Curso;
use App\Models\Equivalencia;
use App\Models\EquivalenciaRule;
use App\Models\PlanoEstudo;
use App\Models\PlanoUc;
use App\Models\Uc;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class WizardController extends Controller implements HasMiddleware
{
    use AuthorizesRequests;

    public static function middleware(): array
    {
        return [
            new Middleware('can:create,' . Curso::class, only: ['store']),
            new Middleware('can:create,' . PlanoEstudo::class, only: ['store']),
            new Middleware('can:create,' . Uc::class, only: ['store']),
            new Middleware('can:view,planoEstudo', only: ['show']),
            new Middleware('can:update,planoEstudo', only: ['update']),
        ];
    }

    public function store(StoreWizardRequest $request)
    {
        return DB::transaction(function () use ($request) {
            // json encode and encode to use arrow notation everywhere
            $data = json_decode(json_encode($request->validated()));

            $course = new Curso();
            if ($data->course->isNew) {
                $course = Curso::create((array)$data->course->data);
            } else {
                $course = Curso::find($data->course->data->id);
            }

            $plan = PlanoEstudo::create([
                'ano' => $data->plan->ano,
                'id_curso' => $course->id,
            ]);


            $ucs_to_plan_ucs = [];
            foreach ($data->ucs as $ucData) {
                $uc = Uc::find($ucData->id);
                if (!$uc && $ucData->isNew) {
                    $uc = Uc::create([
                        'nome' => $ucData->nome,
                        'name' => $ucData->name,
                        'sigla' => $ucData->sigla,
                        'ects' => $ucData->ects,
                        'ano' => $ucData->ano,
                        'semestre' => $ucData->semestre,
                        'id_curso' => $course->id,
                        'horas' => $ucData->horas_trabalho_totais ?? 0,
                        'horas_t' => $ucData->t ?? 0,
                        'horas_tp' => $ucData->tp ?? 0,
                        'horas_pl' => $ucData->pl ?? 0,
                        'horas_tc' => $ucData->tc ?? 0,
                        'horas_s' => $ucData->s ?? 0,
                        'horas_e' => $ucData->e ?? 0,
                        'horas_ot' => $ucData->ot ?? 0,
                        'horas_o' => $ucData->o ?? 0,
                    ]);
                }
                $plan_uc = PlanoUc::create([
                    'id_plano' => $plan->id,
                    'id_uc' => $uc->id,
                ]);
                $ucs_to_plan_ucs[$ucData->id] = $plan_uc->id;
            }

            if ($data->equivalences->prevPlanId) {
                foreach ($data->equivalences->equivalences as $equivalence) {
                    $uc_id = $ucs_to_plan_ucs[$equivalence->newUc] ?? null;
                    if (!$uc_id) {
                        continue;
                    }

                    $eq_rule = EquivalenciaRule::create([
                        'id_plano' => $plan->id,
                    ]);

                    Equivalencia::create([
                        'id_rule' => $eq_rule->id,
                        'id_plano_uc' => $uc_id,
                        'lado' => Equivalencia::LADO_FINAL,
                        'observacao' => $equivalence->observacoes ?? null,
                    ]);

                    foreach ($equivalence->oldUcs as $oldUc) {
                        $old_plan_uc = PlanoUc::where('id_plano', $data->equivalences->prevPlanId)
                            ->where('id_uc', $oldUc)
                            ->first();

                        if ($old_plan_uc) {
                            Equivalencia::create([
                                'id_rule' => $eq_rule->id,
                                'id_plano_uc' => $old_plan_uc->id,
                                'lado' => Equivalencia::LADO_ORIGINAL,
                            ]);
                        }
                    }
                }
            }

            return response()->json([
                'message' => 'Wizard data received and saved successfully!',
            ], 201);
        });
    }

    public function show(PlanoEstudo $planoEstudo)
    {
        $planoEstudo->load([
            'curso',
            'planoUcs.uc',
            'equivalenciaRules.equivalencias.plano_uc',
        ]);

        $course = $planoEstudo->curso;

        // Map UCs to wizard expected structure
        $ucs = $planoEstudo->planoUcs->map(function ($planoUc) {
            $uc = $planoUc->uc;
            return [
                'id' => $uc->id,
                'nome' => $uc->nome,
                'name' => $uc->name,
                'sigla' => $uc->sigla,
                'ects' => $uc->ects,
                'semestre' => $uc->semestre,
                'ano' => $uc->ano,
                'horas_trabalho_totais' => $uc->horas,
                't' => $uc->horas_t,
                'tp' => $uc->horas_tp,
                'pl' => $uc->horas_pl,
                'tc' => $uc->horas_tc,
                's' => $uc->horas_s,
                'e' => $uc->horas_e,
                'ot' => $uc->horas_ot,
                'o' => $uc->horas_o,
                'isNew' => false,
            ];
        });

        // Determine prevPlanId and build equivalences list
        $prevPlanId = '';
        $equivalences = [];

        foreach ($planoEstudo->equivalenciaRules as $rule) {
            $finalEquiv = $rule->equivalencias->where('lado', Equivalencia::LADO_FINAL)->first();
            if (!$finalEquiv || !$finalEquiv->plano_uc) {
                continue;
            }

            $newUcId = $finalEquiv->plano_uc->id_uc;
            $observacoes = $finalEquiv->observacao;
            $oldUcs = [];

            $originalEquivs = $rule->equivalencias->where('lado', Equivalencia::LADO_ORIGINAL);
            foreach ($originalEquivs as $orig) {
                if ($orig->plano_uc) {
                    $oldUcs[] = $orig->plano_uc->id_uc;
                    if (!$prevPlanId) {
                        $prevPlanId = $orig->plano_uc->id_plano;
                    }
                }
            }

            $equivalences[] = [
                'newUc' => $newUcId,
                'observacoes' => $observacoes,
                'oldUcs' => $oldUcs,
            ];
        }

        return response()->json([
            'course' => [
                'isNew' => false,
                'data' => [
                    'id' => $course->id,
                    'nome' => $course->nome,
                    'name' => $course->name,
                    'sigla' => $course->sigla,
                    'codigo' => $course->codigo,
                ],
            ],
            'plan' => [
                'id' => $planoEstudo->id,
                'ano' => $planoEstudo->ano,
            ],
            'ucs' => $ucs,
            'equivalences' => [
                'prevPlanId' => $prevPlanId ?: '',
                'equivalences' => $equivalences,
            ],
        ]);
    }

    public function update(StoreWizardRequest $request, PlanoEstudo $planoEstudo)
    {
        return DB::transaction(function () use ($request, $planoEstudo) {
            $data = json_decode(json_encode($request->validated()));

            // Update plan details
            $planoEstudo->update([
                'ano' => $data->plan->ano,
            ]);

            $existingPlanoUcs = $planoEstudo->planoUcs;
            $newUcIds = [];
            $ucs_to_plan_ucs = [];

            foreach ($data->ucs as $ucData) {
                $uc = null;
                if ($ucData->isNew) {
                    $uc = Uc::create([
                        'nome' => $ucData->nome,
                        'name' => $ucData->name,
                        'sigla' => $ucData->sigla,
                        'ects' => $ucData->ects,
                        'ano' => $ucData->ano,
                        'semestre' => $ucData->semestre,
                        'id_curso' => $planoEstudo->id_curso,
                        'horas' => $ucData->horas_trabalho_totais ?? 0,
                        'horas_t' => $ucData->t ?? 0,
                        'horas_tp' => $ucData->tp ?? 0,
                        'horas_pl' => $ucData->pl ?? 0,
                        'horas_tc' => $ucData->tc ?? 0,
                        'horas_s' => $ucData->s ?? 0,
                        'horas_e' => $ucData->e ?? 0,
                        'horas_ot' => $ucData->ot ?? 0,
                        'horas_o' => $ucData->o ?? 0,
                    ]);
                } else {
                    $uc = Uc::find($ucData->id);
                }

                if ($uc) {
                    $newUcIds[] = $uc->id;
                    $planoUc = $existingPlanoUcs->where('id_uc', $uc->id)->first();
                    if (!$planoUc) {
                        $planoUc = PlanoUc::create([
                            'id_plano' => $planoEstudo->id,
                            'id_uc' => $uc->id,
                        ]);
                    }
                    $ucs_to_plan_ucs[$ucData->id] = $planoUc->id;
                }
            }

            // Remove PlanoUcs not in the new list
            foreach ($existingPlanoUcs as $planoUc) {
                if (!in_array($planoUc->id_uc, $newUcIds)) {
                    // Delete equivalences associated with this plano_uc
                    Equivalencia::where('id_plano_uc', $planoUc->id)->delete();
                    $planoUc->delete();
                }
            }

            // Delete old equivalences and rules for this plan
            $existingRules = EquivalenciaRule::where('id_plano', $planoEstudo->id)->get();
            foreach ($existingRules as $rule) {
                Equivalencia::where('id_rule', $rule->id)->delete();
                $rule->delete();
            }

            // Re-create equivalences
            if (isset($data->equivalences->prevPlanId) && $data->equivalences->prevPlanId) {
                foreach ($data->equivalences->equivalences as $equivalence) {
                    $uc_id = $ucs_to_plan_ucs[$equivalence->newUc] ?? null;
                    if (!$uc_id) {
                        continue;
                    }

                    $eq_rule = EquivalenciaRule::create([
                        'id_plano' => $planoEstudo->id,
                    ]);

                    Equivalencia::create([
                        'id_rule' => $eq_rule->id,
                        'id_plano_uc' => $uc_id,
                        'lado' => Equivalencia::LADO_FINAL,
                        'observacao' => $equivalence->observacoes ?? null,
                    ]);

                    foreach ($equivalence->oldUcs as $oldUc) {
                        $old_plan_uc = PlanoUc::where('id_plano', $data->equivalences->prevPlanId)
                            ->where('id_uc', $oldUc)
                            ->first();

                        if ($old_plan_uc) {
                            Equivalencia::create([
                                    'id_rule' => $eq_rule->id,
                                    'id_plano_uc' => $old_plan_uc->id,
                                    'lado' => Equivalencia::LADO_ORIGINAL,
                                ]);
                        }
                    }
                }
            }

            return response()->json([
                'message' => 'Wizard data received and updated successfully!',
            ], 200);
        });
    }
}
