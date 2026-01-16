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
            'author_id' => 'required|integer',
            'title' => 'required|string',
            'dec' => 'required|string',
            'publish_date' => 'required|date',
            'rating' => 'required|numeric|min:1|max:5',
            'status' => 'required|in:draft,published',
            'language' => 'required|string',
            'pdf_read' => 'required|file|mimes:pdf|max:10240',
            'pdf_download' => 'required|file|mimes:pdf|max:10240',
            'audio' => 'required|file|mimes:mp3,wav,ogg|max:51200',
        ];
    }
}
