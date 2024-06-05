<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class UsersPhotoProfile extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function photoProfile(): Attribute {
        return Attribute::make(
            get: fn($image) => url('/storage/photoProfiles/'. $image),
        );
    }

    public function backgroundPhoto(): Attribute {
        return Attribute::make(
            get: fn($image) => url('/storage/backgroundPhotos/'. $image),
        );
    }
}
