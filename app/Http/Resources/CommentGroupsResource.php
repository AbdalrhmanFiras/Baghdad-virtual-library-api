<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CommentGroupsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = Auth::user();

        return [
            'context' => $this->context,
            'profile_image' => $this->user->profile->image
               ? Storage::disk('s3-private')->temporaryUrl($this->user->profile->image->url, Carbon::now()->addHours(24))
               : null,
            'profile_name' => optional($this->user->profile)->fullname,
            'created_at' => $this->created_at->toDateTimeString(),
            'likes_count' => $this->likes()->count(),
            'is_liked_by_me' => $user
                        ? $this->likes()->where('user_id', $user->id)->exists()
                        : false,
        ];
    }
}
