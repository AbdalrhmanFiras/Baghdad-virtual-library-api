<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProfileRequest extends FormRequest
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
        'fullname' => 'required|string|max:225',
        'bio'  => 'nullable|string',
        'age' => 'nullable|integer', 
        'phone' => 'nullable|string|min:7', 
        'gender' => 'required|in:male,female',
        'language' => 'nullable|string',
        'image'=> 'required|image|mimes:jpg,jpeg,png|max:2048'
        ];

    }
}
