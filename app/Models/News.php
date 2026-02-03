<?php

namespace App\Models;

use Carbon\Carbon;
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

    protected static function booted()
    {
        static::deleting(function ($news) {
            if ($news->image) {
                Storage::disk('s3-private')->delete($news->image->url);
                $news->image->delete();
            }
        }); // work every 24
    }

    public function getImageUrlAttribute()
    {
        return $this->image
            ? Storage::disk('s3')->temporaryUrl(
                $this->image->url,
                Carbon::now()->addHours(24)
            )
            : null;
    }
}
