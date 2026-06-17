<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUcRequest extends FormRequest
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
            'nome' => ['required', 'string', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
            'sigla' => ['required', 'string', 'max:20', 'unique:ucs,sigla'],
            'ects' => ['required', 'integer', 'min:1'],
            'horas' => ['required', 'integer', 'min:1'],
            'id_curso' => ['required', 'integer', 'exists:cursos,id'],
            'semestre' => ['nullable', 'integer', 'min:1', 'max:2'],
            'ano' => ['nullable', 'integer', 'min:1', 'max:6'],
            'horas_t' => ['nullable', 'integer', 'min:0'],
            'horas_tp' => ['nullable', 'integer', 'min:0'],
            'horas_pl' => ['nullable', 'integer', 'min:0'],
            'horas_tc' => ['nullable', 'integer', 'min:0'],
            'horas_s' => ['nullable', 'integer', 'min:0'],
            'horas_e' => ['nullable', 'integer', 'min:0'],
            'horas_ot' => ['nullable', 'integer', 'min:0'],
            'horas_o' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
