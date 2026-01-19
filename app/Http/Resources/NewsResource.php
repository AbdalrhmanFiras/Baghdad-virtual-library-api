<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,
            'title' => $this->when($this->title, fn () => $this->title),
            'description' => $this->when($this->dec, fn () => $this->dec),
            'image_url' => $this->image_url,

        ];
    }
}
