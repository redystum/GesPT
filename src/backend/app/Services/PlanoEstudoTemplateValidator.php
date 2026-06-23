<?php

namespace App\Services;

use App\Models\Curso;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as LaravelValidator;

class PlanoEstudoTemplateValidator
{
    public static function make(array $data, Curso $curso): LaravelValidator
    {
        $validator = Validator::make($data, [
            'plan.ano' => [
                'required',
                'string',
                'max:9',
                'regex:/^20\d{2}\/(20)?\d{2}$/',
                function ($attribute, $value, $fail) {
                    $parts = explode('/', $value);

                    if (count($parts) !== 2) {
                        return;
                    }

                    $year1 = (int) $parts[0];
                    $year2 = (int) $parts[1];

                    if (strlen($parts[1]) === 2) {
                        $year2 = 2000 + $year2;
                    }

                    if ($year2 !== $year1 + 1) {
                        $fail('O segundo ano deve ser exatamente um ano após o primeiro.');
                    }
                },
                'unique:plano_estudos,ano,NULL,id,id_curso,' . $curso->id,
            ],

            'ucs' => ['required', 'array', 'min:1'],
            'ucs.*.nome' => ['required', 'string'],
            'ucs.*.name' => ['required', 'string'],
            'ucs.*.sigla' => ['required', 'string'],
            'ucs.*.ects' => ['required', 'integer', 'min:1'],
            'ucs.*.horas_trabalho_totais' => ['required', 'integer', 'min:0'],
            'ucs.*.semestre' => ['required', 'integer', 'min:1', 'max:2'],
            'ucs.*.ano' => ['required', 'integer', 'min:1'],
            'ucs.*.t' => ['nullable', 'integer', 'min:0'],
            'ucs.*.tp' => ['nullable', 'integer', 'min:0'],
            'ucs.*.pl' => ['nullable', 'integer', 'min:0'],
            'ucs.*.tc' => ['nullable', 'integer', 'min:0'],
            'ucs.*.s' => ['nullable', 'integer', 'min:0'],
            'ucs.*.e' => ['nullable', 'integer', 'min:0'],
            'ucs.*.ot' => ['nullable', 'integer', 'min:0'],
            'ucs.*.o' => ['nullable', 'integer', 'min:0'],
        ]);

        $validator->setAttributeNames([
            'plan.ano' => 'ano letivo do plano',
            'ucs' => 'unidades curriculares',
            'ucs.*.nome' => 'nome em português da UC',
            'ucs.*.name' => 'nome em inglês da UC',
            'ucs.*.sigla' => 'sigla da UC',
            'ucs.*.ects' => 'ECTS da UC',
            'ucs.*.horas_trabalho_totais' => 'horas totais da UC',
            'ucs.*.semestre' => 'semestre da UC',
            'ucs.*.ano' => 'ano da UC',
            'ucs.*.t' => 'horas T da UC',
            'ucs.*.tp' => 'horas TP da UC',
            'ucs.*.pl' => 'horas PL da UC',
            'ucs.*.tc' => 'horas TC da UC',
            'ucs.*.s' => 'horas S da UC',
            'ucs.*.e' => 'horas E da UC',
            'ucs.*.ot' => 'horas OT da UC',
            'ucs.*.o' => 'horas O da UC',
        ]);

        return $validator;
    }
}
