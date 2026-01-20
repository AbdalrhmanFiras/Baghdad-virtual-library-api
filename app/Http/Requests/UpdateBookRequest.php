<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
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
            'author_id' => 'sometimes|integer',
            'title' => 'sometimes|string',
            'dec' => 'sometimes|string',
            'categories' => 'sometimes|array|min:1',
            'categories.*' => 'sometimes|exists:categories,id',
            'publish_year' => 'sometimes|integer|digits:4|max:'.now()->year,
            'image' => 'sometimes|image|mimes:jpg,png,jpeg',
            'rating' => 'sometimes|numeric|min:1|max:5',
            'status' => 'sometimes|in:draft,published',
            'language' => 'sometimes|string',
            'pdf_read' => 'sometimes|file|mimes:pdf|max:10240',
            'pdf_download' => 'sometimes|file|mimes:pdf|max:10240',
            'audio' => 'sometimes|file|mimes:mp3,wav,ogg|max:51200',
        ];
    }
}
