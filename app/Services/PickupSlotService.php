<?php

namespace App\Services;

use App\Models\BusinessHour;
use App\Models\HolidayHour;
use Illuminate\Support\Carbon;

/**
 * Business-hours awareness: is the shop open, which pickup dates are
 * orderable, and which time slots exist for a given date.
 */
class PickupSlotService
{
    public function __construct(private readonly SettingsService $settings) {}

    /** Effective open/close for a calendar date (holiday overrides weekly). */
    public function hoursFor(Carbon $date): ?array
    {
        $holiday = HolidayHour::whereDate('date', $date->toDateString())->first();
        if ($holiday) {
            if ($holiday->is_closed) {
                return null;
            }

            return ['open' => $holiday->open_time, 'close' => $holiday->close_time, 'label' => $holiday->label];
        }

        $hours = BusinessHour::where('day_of_week', $date->dayOfWeek)->first();
        if (! $hours || $hours->is_closed || ! $hours->open_time || ! $hours->close_time) {
            return null;
        }

        return ['open' => $hours->open_time, 'close' => $hours->close_time, 'label' => null];
    }

    public function isOpenAt(Carbon $moment): bool
    {
        $hours = $this->hoursFor($moment);
        if (! $hours) {
            return false;
        }

        $time = $moment->format('H:i:s');

        return $time >= $hours['open'] && $time <= $hours['close'];
    }

    public function isOpenNow(): bool
    {
        return $this->isOpenAt(now());
    }

    /** Whether online ordering is currently accepted at all. */
    public function orderingAvailable(): bool
    {
        if (! $this->settings->bool('ordering_enabled', true)) {
            return false;
        }

        if ($this->settings->bool('preordering_enabled', true)) {
            return $this->orderableDates() !== [];
        }

        return $this->isOpenNow();
    }

    /** @return array<string> Y-m-d dates that accept pickup orders. */
    public function orderableDates(): array
    {
        $maxDays = max(0, $this->settings->int('advance_order_days', 7));
        $preorder = $this->settings->bool('preordering_enabled', true);

        $dates = [];
        $limit = $preorder ? $maxDays : 0;

        for ($i = 0; $i <= $limit; $i++) {
            $date = today()->addDays($i);
            if ($this->slotsFor($date) !== []) {
                $dates[] = $date->toDateString();
            }
        }

        return $dates;
    }

    /** @return array<string> H:i pickup slots for a date. */
    public function slotsFor(Carbon $date): array
    {
        $hours = $this->hoursFor($date);
        if (! $hours) {
            return [];
        }

        $interval = max(5, $this->settings->int('pickup_interval_minutes', 15));
        $leadMinutes = max(0, $this->settings->int('pickup_lead_minutes', 20));

        $open = $date->copy()->setTimeFromTimeString($hours['open']);
        $close = $date->copy()->setTimeFromTimeString($hours['close']);

        // Earliest slot must respect kitchen lead time when ordering today.
        $earliest = $date->isToday() ? now()->addMinutes($leadMinutes) : $open;
        if ($earliest->lt($open)) {
            $earliest = $open;
        }

        // Round up to the next interval boundary.
        $minute = (int) ceil($earliest->minute / $interval) * $interval;
        $cursor = $earliest->copy()->startOfHour()->addMinutes($minute);

        $slots = [];
        while ($cursor->lte($close)) {
            $slots[] = $cursor->format('H:i');
            $cursor->addMinutes($interval);
        }

        return $slots;
    }

    public function isValidSlot(Carbon $date, string $time): bool
    {
        return in_array(substr($time, 0, 5), $this->slotsFor($date), true);
    }
}
