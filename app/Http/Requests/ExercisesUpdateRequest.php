<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExercisesUpdateRequest extends FormRequest
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
            'title' => 'required|min:3|max:200|',
            'description' => 'max:1000'
        ];
    }
    public function attributes()
    {
        //KOLON İSİMLERİ GÜNCELLEME
        return [
            /* 
            'title' => 'BAŞLIK',
            'description' => 'AÇIKLAMA'
            */
        ];
    }
}
