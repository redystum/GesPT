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
            'course.data.id' => 'required|integer',
            'course.data.nome' => 'required_if:course.isNew,true|string',
            'course.data.name' => 'required_if:course.isNew,true|string',
            'course.data.sigla' => 'required_if:course.isNew,true|string',
            'course.data.codigo' => 'required_if:course.isNew,true|string',

            'plano.ano' => [
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
                }
            ],

            'ucs' => 'required|array|min:1',
            'ucs.*.isNew' => 'boolean',
            'ucs.*.id' => 'required_unless:ucs.*.isNew,true|integer',
            'ucs.*.nome' => 'required_if:ucs.*.isNew,true|string',
            'ucs.*.name' => 'required_if:ucs.*.isNew,true|string',
            'ucs.*.sigla' => 'required_if:ucs.*.isNew,true|string',
            'ucs.*.ects' => 'required_if:ucs.*.isNew,true|integer|min:1',
            'ucs.*.horas' => 'required_if:ucs.*.isNew,true|integer|min:1',
            'ucs.*.semestre' => 'required_if:ucs.*.isNew,true|integer',

            'equivalences.prevPlanId' => 'required|integer|exists:plano_estudos,id',
            'equivalences.equivalences' => 'required|array',
            'equivalences.equivalences.*.newUc' => 'integer',
            'equivalences.equivalences.*.oldUcs' => 'array|min:1',
            'equivalences.equivalences.*.oldUcs.*' => 'integer',

        ];
    }
}
