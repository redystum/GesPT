<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWizardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        return [
            'course.isNew' => 'boolean',
            'course.data.id' => 'required_unless:course.isNew,true|integer',
            'course.data.nome' => 'required_if:course.isNew,true|string',
            'course.data.name' => 'required_if:course.isNew,true|string',
            'course.data.sigla' => 'required_if:course.isNew,true|string',
            'course.data.codigo' => 'required_if:course.isNew,true|string',

            'plan.ano' => [
                'required',
                'string',
                'max:9',
                'regex:/^20\d{2}\/(20)?\d{2}$/',
                function ($attribute, $value, $fail) {
                    $parts = explode('/', $value);
                    $year1 = (int) $parts[0];
                    $year2 = (int) $parts[1];
                    if (strlen($parts[1]) === 2) {
                        $year2 = 2000 + $year2;
                    }
                    if ($year2 !== $year1 + 1) {
                        $fail('The second year must be exactly one year after the first (e.g. 2025/2026).');
                    }
                },
                'unique:plano_estudos,ano,' . (($plano = $this->route('planoEstudo')) ? (is_object($plano) ? $plano->id : $plano) : 'NULL') . ',id,id_curso,' . ($this->course['isNew'] ? 'NULL' : $this->course['data']['id'])
            ],

            'ucs' => 'required|array|min:1',
            'ucs.*.isNew' => 'boolean',
            'ucs.*.id' => 'required_unless:ucs.*.isNew,true|integer',
            'ucs.*.nome' => 'required_if:ucs.*.isNew,true|string',
            'ucs.*.name' => 'required_if:ucs.*.isNew,true|string',
            'ucs.*.sigla' => 'required_if:ucs.*.isNew,true|string',
            'ucs.*.ects' => 'required_if:ucs.*.isNew,true|integer|min:1',
            'ucs.*.ano' => 'required_if:ucs.*.isNew,true|integer|min:1',
            'ucs.*.semestre' => 'required_if:ucs.*.isNew,true|integer|min:1',
            'ucs.*.horas_trabalho_totais' => 'nullable|integer|min:0',
            'ucs.*.t' => 'nullable|integer|min:0',
            'ucs.*.tp' => 'nullable|integer|min:0',
            'ucs.*.pl' => 'nullable|integer|min:0',
            'ucs.*.tc' => 'nullable|integer|min:0',
            'ucs.*.s' => 'nullable|integer|min:0',
            'ucs.*.e' => 'nullable|integer|min:0',
            'ucs.*.ot' => 'nullable|integer|min:0',
            'ucs.*.o' => 'nullable|integer|min:0',

            'equivalences.prevPlanId' => 'nullable|integer|exists:plano_estudos,id',
            'equivalences.equivalences' => 'array',
            'equivalences.equivalences.*.newUc' => 'integer',
            'equivalences.equivalences.*.observacoes' => 'nullable|string',
            'equivalences.equivalences.*.oldUcs' => 'array|min:1',
            'equivalences.equivalences.*.oldUcs.*' => 'integer',

        ];
    }

    public function attributes(): array
    {
        return [
            'course.isNew' => 'tipo de curso',
            'course.data.id' => 'curso',
            'course.data.nome' => 'nome do curso (PT)',
            'course.data.name' => 'nome do curso (EN)',
            'course.data.sigla' => 'sigla do curso',
            'course.data.codigo' => 'codigo do curso',

            'plan.ano' => 'ano letivo',

            'ucs' => 'unidades curriculares',
            'ucs.*.isNew' => 'tipo de unidade curricular',
            'ucs.*.id' => 'unidade curricular',
            'ucs.*.nome' => 'nome da unidade curricular (PT)',
            'ucs.*.name' => 'nome da unidade curricular (EN)',
            'ucs.*.sigla' => 'sigla da unidade curricular',
            'ucs.*.ects' => 'ECTS da unidade curricular',
            'ucs.*.horas' => 'horas da unidade curricular',
            'ucs.*.semestre' => 'semestre da unidade curricular',

            'equivalences.prevPlanId' => 'plano anterior',
            'equivalences.equivalences' => 'equivalencias',
            'equivalences.equivalences.*.newUc' => 'nova unidade curricular',
            'equivalences.equivalences.*.oldUcs' => 'unidades curriculares antigas',
            'equivalences.equivalences.*.oldUcs.*' => 'unidade curricular antiga',
        ];
    }
}
