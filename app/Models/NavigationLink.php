<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class NavigationLink extends Model
{
    protected $fillable = ['location', 'label', 'url', 'new_tab', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return [
            'new_tab' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function scopeLocation(Builder $query, string $location): Builder
    {
        return $query->where('location', $location)
            ->where('is_active', true)
            ->orderBy('sort_order');
    }
}
