<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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
            'context' => $this->context,
            'profile_image' => optional($this->user->profile)->image,
            'profile_name' => optional($this->user->profile)->name,
            'user_id' => $this->user_id,
            'book_id' => $this->book_id,
        ];
    }
}
