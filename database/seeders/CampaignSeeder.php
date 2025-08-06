<?php
// database/seeders/CampaignSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Campaign;

class CampaignSeeder extends Seeder {
    public function run() {
        Campaign::insert([
            [
                'name' => 'Spring Stay Special - 20% Off',
                'description' => 'Enjoy 20% off on all room bookings throughout April. Experience the charm of our riad this spring!',
                'start_date' => '2025-04-01',
                'end_date' => '2025-04-30',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Refer a Friend – Complimentary Breakfast',
                'description' => 'Refer a friend to stay with us and receive a complimentary traditional Moroccan breakfast on your next visit.',
                'start_date' => '2025-03-15',
                'end_date' => '2025-05-15',
                'active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
