<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
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
            'sigla' => ['required', 'string', 'max:20', 'unique:cursos,sigla'],
            'codigo' => ['required', 'string', 'max:10', 'unique:cursos,codigo'],
        ];
    }
}
