<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Book extends Model
{
    protected $with = ['categories', 'image'];

    protected $guarded = ['id'];

    protected $appends = ['image_url'];

    protected $casts = [
        'is_readable' => 'boolean',
        'is_downloadable' => 'boolean',
        'has_audio' => 'boolean',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_book');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function getImageUrlAttribute()
    {
        return $this->image
            ? Storage::disk('s3-private')->temporaryUrl($this->image->url, Carbon::now()->addHours(24))
            : null;
    }

    public function scopeGetBook($query, $id)
    {
        return $query->where('id', $id);
    }
}
