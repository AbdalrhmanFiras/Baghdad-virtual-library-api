<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
            'publish_year' => Carbon::parse($this->publish_date)->year,    
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'rating' => $this->rating,
            'pdf_read' => $this->pdf_read,
            'pdf_download' => $this->when($this->pdf_download,function(){ return $this->pdf_download;}),
            'audio' => $this->when($this->audio,function(){ return $this->audio;}),
            'status' => $this->status,
            'image_url'  => $this->when($this->image_url ,function(){
                return $this->image_url;
            }), ];
    
    }
}
