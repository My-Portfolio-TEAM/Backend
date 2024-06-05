<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;


class UsersBackgroundPhoto extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function backgroundPhoto(): Attribute {
        return Attribute::make(
            get: fn($image) => url('/storage/backgroundPhotos/'. $image),
        );
    }
}
