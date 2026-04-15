<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlanoEstudoRequest extends FormRequest
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
            'ano' => [
                'sometimes',
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
            'id_curso' => ['sometimes', 'integer', 'exists:cursos,id'],
        ];
    }
}
