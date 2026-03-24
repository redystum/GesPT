<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCourseRequest extends FormRequest
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
        $curso = $this->route('curso'); // Get the Curso model instance from the route

        return [
            'nome' => ['sometimes', 'string', 'max:255'],
            'sigla' => [
                'sometimes', 
                'string', 
                'max:20', 
                Rule::unique('cursos', 'sigla')->ignore($curso),
            ],
            'codigo' => [
                'sometimes', 
                'string', 
                'max:10', 
                Rule::unique('cursos', 'codigo')->ignore($curso),
            ],
        ];
    }
}
