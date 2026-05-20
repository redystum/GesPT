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

class WizardController extends Controller
{
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
                    $horas = [
                        $ucData->horas_trabalho_totais ?? 0,
                        $ucData->t ?? 0,
                        $ucData->tp ?? 0,
                        $ucData->pl ?? 0,
                        $ucData->tc ?? 0,
                        $ucData->s ?? 0,
                        $ucData->e ?? 0,
                        $ucData->ot ?? 0,
                        $ucData->o ?? 0,
                    ];
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
}
