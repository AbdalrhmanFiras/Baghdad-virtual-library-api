<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'dec' => $this->dec,
            'language' => $this->language,
            'publish_date' => $this->publish_date,
            'rating' => $this->rating,
            'pdf_read' => $this->pdf_read,
            'pdf_download' => $this->pdf_download,
            'audio' => $this->pdf_download,
            'status' => $this->status,
        ];
    }
}
