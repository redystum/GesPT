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
            'sigla' => ['required', 'string', 'max:20', 'unique:ucs,sigla'],
            'ects' => ['required', 'integer', 'min:1'],
            'horas' => ['required', 'integer', 'min:1'],
            'id_curso' => ['required', 'integer', 'exists:cursos,id'],
        ];
    }
}
