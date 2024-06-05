<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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

    /**
     * getJWTCustomClaims
     *
     * @return void
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function name(): Attribute {
        return Attribute::make(
            set: fn($name) => strtolower($name),
            get: fn($name) => ucwords($name),
        );
    }

    public function photoProfile() {
        return $this->hasOne(UsersPhotoProfile::class);
    }
    public function backgroundPhoto() {
        return $this->hasOne(UsersBackgroundPhoto::class);
    }

    public function biodata() {
        return $this->hasOne(UsersBiodata::class);
    }

    public function portfolios() {
        return $this->hasMany(Portfolio::class);
    }

    public function posts() {
        return $this->hasMany(Post::class);
    }

    public function postUpVotes() {
        return $this->hasMany(Post::class);
    }

    public function comments() {
        return $this->hasMany(Comment::class);
    }

    public function commentUpVotes() {
        return $this->hasMany(CommentUpVote::class);
    }

    public function replyComment() {
        return $this->hasMany(ReplyComment::class);
    }
}
