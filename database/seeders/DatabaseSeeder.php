<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

     $this->call(ServiceSeeder::class);
     $this->call(FinanceSeeder::class);
    $this->call(EmployeeSeeder::class);

    $this->call(BusinessHoursSeeder::class);
        $this->call(CampaignSeeder::class);
     $this->call(ProductSeeder::class);
    $this->call(UserSeeder::class);
    $this->call(RoomSeeder::class);





    }
}
