<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'type', 'title', 'description', 'badge_text', 'image', 'image_alt',
        'button_text', 'button_url', 'starts_at', 'ends_at', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'starts_at' => 'date',
            'ends_at' => 'date',
        ];
    }

    public function scopeCurrent(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhereDate('starts_at', '<=', today()))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhereDate('ends_at', '>=', today()))
            ->orderBy('sort_order');
    }

    public function scopeBanners(Builder $query): Builder
    {
        return $query->where('type', 'banner');
    }

    public function scopeOffers(Builder $query): Builder
    {
        return $query->where('type', 'offer');
    }
}
