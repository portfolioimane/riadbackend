<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    public function run()
    {
        Room::insert([
            [
                'room_name' => 'Standard Room',
                'room_type' => 'Standard',
                'main_photo' => 'rooms/standard.jpg',
                'max_adults' => 2,
                'max_children' => 1,
                'price' => 750.00,
                'description' => 'A comfortable room for 2 adults and 1 child.',
                'featured' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'room_name' => 'Deluxe Suite',
                'room_type' => 'Deluxe',
                'main_photo' => 'rooms/deluxe.jpg',
                'max_adults' => 2,
                'max_children' => 2,
                'price' => 1500.00,
                'description' => 'Spacious deluxe suite with private balcony.',
                'featured' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

