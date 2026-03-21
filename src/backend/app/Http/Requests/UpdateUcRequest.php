<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
        return [
            'nome' => ['sometimes', 'string', 'max:255'],
            'sigla' => ['sometimes', 'string', 'max:20', 'unique:ucs,sigla,' . $this->route('uc')->id],
            'ects' => ['sometimes', 'integer', 'min:1'],
            'horas' => ['sometimes', 'integer', 'min:1'],
            'id_curso' => ['sometimes', 'integer', 'exists:cursos,id'],
        ];
    }
}
