<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function comments() {
        return $this->hasMany(Comment::class)->latest();
    }

    public function postUpVotes() {
        return $this->hasMany(PostUpVote::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function image(): Attribute {
        return Attribute::make(
            get: fn($image) => $image != null ?  url('/storage/posts/'. $image) : null,
        );
    }
}
