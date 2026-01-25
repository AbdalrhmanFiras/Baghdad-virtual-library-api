<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'google_id',
        'auth_provider',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function group()
    {
        return $this->hasMany(Groups::class);
    }

    public function groups()
    {
        return $this->belongsToMany(Groups::class, 'group_users', 'user_id', 'group_id');
    }

    public function books()
    {
        return $this->belongsToMany(Book::class, 'user_books')
            ->withPivot('status', 'fav', 'to_read', 'total_pages', 'pages_read')// use pivot model
            ->withTimestamps();
    }

    public function user_tags()
    {
        return $this->belongsToMany(UserTags::class, 'tag_users', 'user_id', 'user_tag_id');
    }
}
