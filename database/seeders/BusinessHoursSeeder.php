<?php

namespace Database\Seeders;

use App\Models\BusinessHour;
use Illuminate\Database\Seeder;

class BusinessHoursSeeder extends Seeder
{
    public function run(): void
    {
        // 0 = Sunday .. 6 = Saturday. Typical bagel shop hours — editable in admin.
        $hours = [
            0 => ['06:00', '15:00'],
            1 => ['05:30', '15:00'],
            2 => ['05:30', '15:00'],
            3 => ['05:30', '15:00'],
            4 => ['05:30', '15:00'],
            5 => ['05:30', '15:00'],
            6 => ['06:00', '15:00'],
        ];

        foreach ($hours as $day => [$open, $close]) {
            BusinessHour::updateOrCreate(
                ['day_of_week' => $day],
                ['open_time' => $open, 'close_time' => $close, 'is_closed' => false]
            );
        }
    }
}
