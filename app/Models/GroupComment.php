<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupComment extends Model
{
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function group()
    {
        return $this->belongsTo(Groups::class);
    }

    public function likes()
    {
        return $this->hasMany(CommentLike::class);
    }

    public function likesCount()
    {
        return $this->likes()->count();
    }
}
