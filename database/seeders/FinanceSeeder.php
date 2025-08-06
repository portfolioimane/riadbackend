<?php
namespace Database\Seeders;

use App\Models\Finance;
use Illuminate\Database\Seeder;

class FinanceSeeder extends Seeder
{
    public function run(): void
    {
        Finance::insert([
            [
                'type' => 'revenue',
                'title' => 'Room Booking - Andalus Suite',
                'amount' => 1200,
                'date' => now()->subDays(3)->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'revenue',
                'title' => 'Hammam & Massage Service',
                'amount' => 500,
                'date' => now()->subDays(2)->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'expense',
                'title' => 'Traditional Moroccan Breakfast Supplies',
                'amount' => 300,
                'date' => now()->subDay()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'expense',
                'title' => 'Electricity Bill',
                'amount' => 450,
                'date' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}

