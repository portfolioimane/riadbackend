<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GalleryImage;

class GalleryImageSeeder extends Seeder
{
    public function run()
    {
        GalleryImage::create([
            'image_path' => 'gallery/sample1.jpg',
        ]);
        GalleryImage::create([
            'image_path' => 'gallery/sample2.jpg',
        ]);
        // Add more if you want
    }
}
