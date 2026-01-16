<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
        'fullname'  => 'sometimes|string|max:225',
        'bio'   => 'sometimes|string',
        'age' => 'sometimes|integer', 
        'phone' => 'sometimes|string|min:5', 
         'gender' => 'sometimes|in:male,female',
        'language' => 'sometimes|string',
        'image' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048'
        ];
    }
}
