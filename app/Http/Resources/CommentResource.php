<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

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
            'profile_image' => $this->user->profile->image
                ? Storage::disk('s3-private')->temporaryUrl($this->user->profile->image->url,Carbon::now()->addHours(24))
                : null,
            'profile_name' => optional($this->user->profile)->fullname,
            'user_id' => $this->user_id,
            'book_id' => $this->book_id,
        ];
    }
}
