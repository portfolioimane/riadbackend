<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
        'duration',
        'category',
        'featured',
    ];

        public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}

