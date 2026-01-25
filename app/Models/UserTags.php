<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTags extends Model
{
    protected $guarded = ['id'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'tag_users', 'user_tag_id', 'user_id');
    }
}
