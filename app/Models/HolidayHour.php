<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HolidayHour extends Model
{
    protected $fillable = ['date', 'label', 'open_time', 'close_time', 'is_closed'];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_closed' => 'boolean',
        ];
    }
}
