<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         return [
            'id'         => $this->id,
            'fullname'       => $this->fullname,
            'bio'        => $this->bio,
            'email'     => $this->user->email ,
            'phone'          => $this->phone,
            'age'          => $this->age,
            'gender'        => $this->gender,
            'language'        => $this->language,
            'user_id'    => $this->user_id,
            'image_url'  => $this->when($this->image_url ,function(){
                return $this->image_url;
            }), ];
    }
}
