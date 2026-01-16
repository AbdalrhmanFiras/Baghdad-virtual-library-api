<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
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
            'author_id' => 'required|integer|exists:authors.id',
            'title' => 'required|string',
            'dec' => 'required|string',
            'pdf_read' => 'nullable|file|mimes:pdf|max:10240',
            'pdf_download' => 'nullable|file|mimes:pdf|max:10240',
            'audio' => 'required|file|mimes:mp3,wav,ogg|max:51200',
            'language' => 'required|string',
            'publish_date' => 'required|date',
            'rating' => 'required|numeric|between:1,5',
            'status' => 'required|in:draft,published',
        ];
    }
}
