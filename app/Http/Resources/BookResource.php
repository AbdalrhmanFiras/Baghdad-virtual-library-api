<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

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
            'description' => $this->dec,
            'language' => $this->language,
            'publish_year' => (int) $this->publish_year,
            'rating' => (float) $this->rating,
            'status' => $this->status,

            'fav' => (bool) ($this->pivot?->fav),
            'status' => $this->pivot?->status,
            'to_read' => (bool) ($this->pivot?->to_read),
            'pages_read' => (int) ($this->pivot?->pages_read ?? 0),

            'pdf_read' => Storage::disk('s3-private')->temporaryUrl($this->pdf_read, Carbon::now()->addMinute(5)),
            'pdf_download' => $this->when($this->pdf_download, fn () => $this->pdf_download),
            'audio' => $this->when($this->audio, fn () => $this->audio),

            'is_readable' => $this->when($this->is_readable, fn () => $this->is_readable),
            'is_downloadable' => $this->when($this->is_downloadable, fn () => $this->pdf_download),
            'has_audio' => $this->when($this->has_audio, fn () => $this->has_audio),

            'image_url' => $this->when($this->image_url, fn () => $this->image_url),

            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
        ];
    }
}
