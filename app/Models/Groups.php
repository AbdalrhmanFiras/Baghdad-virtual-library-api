<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class Groups extends Model
{
    protected $guarded = ['id'];

    protected $appends = ['image_url'];

    public function category_groups()
    {
        return $this->belongsToMany(CategoryGroup::class, 'group_category_groups', 'group_id',
            'category_group_id');
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'group_users', 'group_id', 'user_id');
    }

    public function scopeCategoryGroupId($query, $id)
    {
        $query->whereHas('category_groups', function ($q) use ($id) {
            $q->where('id', $id);
        });
    }

    public function comments()
    {
        return $this->hasMany(GroupComment::class);
    }
}
