<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGroupRequest extends FormRequest
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
            'title' => 'required|string',
            'des' => 'required|string',
            'categories' => 'required|array|min:1',
            'categories.*' => 'required|exists:category_groups,id',
            'image' => 'required|image|mimes:jpg,png,jpeg',
            'rating' => 'required|numeric|min:1|max:5',
            'status' => 'required|in:public,private',
            'availbility' => 'required|in:active,unactive',
        ];
    }
}
