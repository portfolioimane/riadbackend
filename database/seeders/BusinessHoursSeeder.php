<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BusinessHours;

class BusinessHoursSeeder extends Seeder
{
    public function run()
    {
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        foreach ($days as $day) {
            BusinessHours::create([
                'day' => $day,
                'open_time' => '08:00',    // Open early to welcome early arrivals & breakfast
                'close_time' => '01:00',   // Close after midnight for late check-ins, dinner, etc.
                'is_closed' => false,
            ]);
        }

        // Optionally, for Friday early closure (adjust if you want)
        // BusinessHours::where('day', 'Friday')->update([
        //     'close_time' => '14:00',
        // ]);
    }
}
