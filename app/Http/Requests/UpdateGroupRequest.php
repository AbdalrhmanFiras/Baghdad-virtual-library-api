<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGroupRequest extends FormRequest
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
            'title' => 'sometimes|string',
            'des' => 'sometimes|string',
            'image' => 'sometimes|image|mimes:jpg,png,jpeg',
            'rating' => 'sometimes|numeric|min:1|max:5',
            'status' => 'sometimes|in:public,private',
            'availbility' => 'sometimes|in:active,unactive',
        ];
    }
}
