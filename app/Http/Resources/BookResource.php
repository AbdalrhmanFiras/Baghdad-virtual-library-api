<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Resources\CommentResource;
use Illuminate\Support\Facades\Storage;
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
            'description' => $this->dec,
            'language' => $this->language,
            'publish_year' => (int) $this->publish_year,
            'rating' => (float) $this->rating,
            'status' => $this->status_case,
            'author_name' => $this->author_name

            'fav' => (bool) ($this->pivot?->fav),
            'book_status' => $this->pivot?->status ?? 'none',
            'to_read' => (bool) ($this->pivot?->to_read),
            'pages_read' => (int) ($this->pivot?->pages_read ?? 0),

            'pdf_read' => Storage::disk('s3-private')->temporaryUrl($this->pdf_read, Carbon::now()->addMinute(5)),

            'pdf_download' => Storage::disk('s3-private')->temporaryUrl($this->pdf_download, Carbon::now()->addMinute(5)),

            'audio' => $this->when($this->audio, fn () => $this->audio),

            'is_readable' => $this->when($this->is_readable, fn () => $this->is_readable),
            'is_downloadable' => $this->when($this->is_downloadable, fn () => $this->is_downloadable),
            'has_audio' => $this->when($this->has_audio, fn () => $this->has_audio),

            'image_url' => $this->when($this->image_url, fn () => $this->image_url),

            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
            
        ];
    }
}
