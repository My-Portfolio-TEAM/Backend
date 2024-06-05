<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function title(): Attribute {
        return Attribute::make(
            set: fn($title) => strtolower($title),
            get: fn($title) => ucwords($title),
        );
    }

    public function image(): Attribute {
        return Attribute::make(
            get: fn($image) => url('/storage/portfolios/'. $image),
        );
    }

    public function biodata()
    {
        return $this->user->biodata();
    }
}
