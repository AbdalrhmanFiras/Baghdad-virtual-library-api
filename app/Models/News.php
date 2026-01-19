<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class News extends Model
{
    protected $guarded = ['id'];

    protected $with = ['image'];

    protected $appends = ['image_url'];

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function getImageUrlAttribute($key)
    {
        return $this->image ? Storage::disk('s3')->url($this->image->url) : null;
    }
}
