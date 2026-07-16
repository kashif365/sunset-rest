<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BusinessHoursRequest;
use App\Http\Requests\Admin\HolidayHourRequest;
use App\Models\BusinessHour;
use App\Models\HolidayHour;

class BusinessHoursController extends Controller
{
    public function edit()
    {
        $hours = BusinessHour::orderBy('day_of_week')->get()->keyBy('day_of_week');

        // Guarantee all 7 rows exist for the form.
        for ($day = 0; $day <= 6; $day++) {
            if (! $hours->has($day)) {
                $hours->put($day, new BusinessHour(['day_of_week' => $day, 'is_closed' => true]));
            }
        }

        return view('admin.hours.edit', [
            'hours' => $hours->sortKeys(),
            'holidays' => HolidayHour::orderBy('date')->get(),
        ]);
    }

    public function update(BusinessHoursRequest $request)
    {
        foreach ($request->validated('hours') as $row) {
            BusinessHour::updateOrCreate(
                ['day_of_week' => (int) $row['day_of_week']],
                [
                    'is_closed' => filter_var($row['is_closed'] ?? false, FILTER_VALIDATE_BOOL),
                    'open_time' => $row['open_time'] ?? null,
                    'close_time' => $row['close_time'] ?? null,
                ]
            );
        }

        return back()->with('success', 'Business hours saved.');
    }

    public function storeHoliday(HolidayHourRequest $request)
    {
        $data = $request->validated();
        $data['is_closed'] = $request->boolean('is_closed');

        HolidayHour::create($data);

        return back()->with('success', 'Holiday hours added.');
    }

    public function destroyHoliday(HolidayHour $holidayHour)
    {
        $holidayHour->delete();

        return back()->with('success', 'Holiday entry removed.');
    }
}
