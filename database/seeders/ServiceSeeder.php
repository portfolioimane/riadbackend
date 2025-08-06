<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        Service::create([
            'name' => 'Dental Cleaning',
            'description' => 'Professional teeth cleaning to remove plaque and tartar.',
            'price' => 300.00,
            'image' => 'dental_cleaning.jpg',
            'duration' => 45,
            'category' => 'Preventive',
            'featured' => true,
        ]);

        Service::create([
            'name' => 'Tooth Extraction',
            'description' => 'Safe removal of decayed or damaged tooth.',
            'price' => 500.00,
            'image' => 'tooth_extraction.jpg',
            'duration' => 30,
            'category' => 'Surgical',
            'featured' => false,
        ]);

        Service::create([
            'name' => 'Teeth Whitening',
            'description' => 'Brighten your smile with professional whitening treatment.',
            'price' => 800.00,
            'image' => 'teeth_whitening.jpg',
            'duration' => 60,
            'category' => 'Cosmetic',
            'featured' => true,
        ]);
    }
}
