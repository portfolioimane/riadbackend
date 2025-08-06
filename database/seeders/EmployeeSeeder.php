<?php
// database/seeders/EmployeeSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
        Employee::insert([
            [
                'name' => 'Fatima El Amrani',
                'email' => 'fatima.elamrani@riadexample.com',
                'role' => 'Receptionist',
                'phone' => '+212 600-123456',
                'avatar' => 'https://images.unsplash.com/photo-1607746882042-944635dfe10e?w=150&h=150&fit=crop&crop=face',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ahmed Bensouda',
                'email' => 'ahmed.bensouda@riadexample.com',
                'role' => 'Chef',
                'phone' => '+212 600-654321',
                'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=150&h=150&fit=crop&crop=face',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Khadija Ait Taleb',
                'email' => 'khadija.aittaleb@riadexample.com',
                'role' => 'Housekeeper',
                'phone' => '+212 600-789012',
                'avatar' => 'https://images.unsplash.com/photo-1529626455594-4ff0802cfb7e?w=150&h=150&fit=crop&crop=face',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
