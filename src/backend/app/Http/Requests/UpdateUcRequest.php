<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUcRequest extends FormRequest
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
        $uc = $this->route('uc'); // Get the Uc model instance from the route

        return [
            'nome' => ['sometimes', 'string', 'max:255'],
            'sigla' => [
                'sometimes',
                'string',
                'max:20',
                Rule::unique('ucs', 'sigla')->ignore($uc),
            ],
            'ects' => ['sometimes', 'integer', 'min:1'],
            'horas' => ['sometimes', 'integer', 'min:1'],
            'id_curso' => ['sometimes', 'integer', 'exists:cursos,id'],
        ];
    }
}
