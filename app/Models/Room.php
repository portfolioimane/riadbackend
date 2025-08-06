<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'room_name',
        'room_type',
        'main_photo',
        'max_adults',
        'max_children',
        'price',
        'description',
        'featured',
    ];

    protected $casts = [
        'featured' => 'boolean',
    ];
          public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
       public function photoGallery()
    {
        return $this->hasMany(PhotoGallery::class);
    }
}
