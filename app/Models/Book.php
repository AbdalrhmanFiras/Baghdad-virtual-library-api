<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
    'is_readable' => 'boolean',
    'is_downloadable' => 'boolean',
    'has_audio' => 'boolean',
];
   public function categories() {
        return $this->belongsToMany(Category::class);
    }
}
