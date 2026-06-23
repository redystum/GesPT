<?php

namespace App\Services;

use App\Models\Curso;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as LaravelValidator;

class PlanoEstudoTemplateValidator
{
    public static function make(array $data, Curso $curso): LaravelValidator
    {
        return Validator::make($data, [
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
    }
}
