<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    public function commentsUpVotes() {
        return $this->hasMany(CommentUpVote::class);
    }

    public function replyComments() {
        return $this->hasMany(ReplyComment::class);
    }

    public function post() {
        return $this->belongsTo(Post::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

}
