<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $with = ['image'];
    protected $guarded = ['id'];
    protected $appends = ['image_url'];

    public function user(){
        return $this->belongsTo(User::class);
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
}

