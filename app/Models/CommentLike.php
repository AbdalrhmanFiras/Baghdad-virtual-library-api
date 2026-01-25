<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentLike extends Model
{
    protected $guarded = ['id'];

    public function comment()
    {
        return $this->belongsTo(GroupComment::class, 'group_comment_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
