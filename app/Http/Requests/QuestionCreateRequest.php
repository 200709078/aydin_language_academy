<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuestionCreateRequest extends FormRequest
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
            'question' => 'required|min:3',
            'image' => 'image|nullable|max:1024|mimes:jpg,jpeg,png',
            'select1' => 'required|min:1',
            'select2' => 'required|min:1',
            'select3' => 'required|min:1',
            'select4' => 'required|min:1',
            'select5' => 'required|min:1',
            'correct_answer' => 'required'
        ];
    }
}
