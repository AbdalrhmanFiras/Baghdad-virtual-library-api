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

    public function hasFlag(string $flag)// for checking
    {
        return $this->flags()->where('flag', $flag)->exists();
    }

    public function flags()
    {
        return $this->hasMany(BookFlags::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_books')
            ->withPivot('status', 'fav', 'to_read', 'pages_read', 'total_pages')
            ->withTimestamps();
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

    public function scopeAuthorName($query, $name)
    {
        return $query->whereHas('author', function ($q) use ($name) {
            $q->where('author_name', 'like', "%{$name}%");
        });
    }

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    protected static function booted()
    {
        // static::updating(function ($book) {
        //     // تحقق إذا كان حقل pdf_read قد تغيّر
        //     if ($book->isDirty('pdf_read')) {
        //         // حذف الملف القديم من S3
        //         Storage::disk('s3-private')->delete($book->getOriginal('pdf_read'));
        //     }

        //     // نفس الشيء لأي حقل آخر تريد حذفه عند التغيير
        //     if ($book->isDirty('pdf_download')) {
        //         Storage::disk('s3-private')->delete($book->getOriginal('pdf_download'));
        //     }

        //     if ($book->isDirty('audio')) {
        //         Storage::disk('s3-private')->delete($book->getOriginal('audio'));
        //     }

        //     if ($book->isDirty('cover_image')) {
        //         Storage::disk('s3-private')->delete($book->getOriginal('cover_image'));
        //     }
        // });
    }
}
