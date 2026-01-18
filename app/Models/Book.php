<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
   public function categories() {
        return $this->belongsToMany(Category::class,'category_book');
    }

    public function image(){
        return $this->morphOne(Image::class , 'imageable');
    }

    public function getImageUrlAttribute()
    {
    return $this->image
        ? asset('storage/' . $this->image->url)
        : null;
    }

      public function scopeGetBook($query,$id)
    {
        return $query->where('id' , $id);
    }
}
