<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
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
            'rating' => (float) $this->rating,
            'status' => $this->status,
            'availbility' => $this->availbility,
            'image_url' => $this->when($this->image_url, fn () => $this->image_url),
            'categories' => CategoryResource::collection($this->whenLoaded('category_groups')),
            'owner' => optional($this->user->profile)->fullname,

        ];
    }
}
