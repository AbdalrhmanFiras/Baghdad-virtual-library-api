<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryGroup extends Model
{
    protected $guarded = ['id'];

    public function groups()
    {
        return $this->belongsToMany(
            Groups::class,
            'group_category_groups',
            'category_group_id',
            'group_id'
        );
    }
}
