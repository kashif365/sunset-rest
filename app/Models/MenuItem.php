<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'slug', 'short_description', 'description',
        'price', 'discounted_price', 'image', 'image_alt', 'prep_time_minutes',
        'stock_quantity', 'low_stock_threshold', 'is_available', 'is_sold_out',
        'is_featured', 'is_bestseller', 'needs_verification',
        'available_from', 'available_until', 'available_days', 'sort_order',
        'seo_title', 'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discounted_price' => 'decimal:2',
            'is_available' => 'boolean',
            'is_sold_out' => 'boolean',
            'is_featured' => 'boolean',
            'is_bestseller' => 'boolean',
            'needs_verification' => 'boolean',
            'available_days' => 'array',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variations(): HasMany
    {
        return $this->hasMany(MenuItemVariation::class)->orderBy('sort_order');
    }

    public function modifierGroups(): BelongsToMany
    {
        return $this->belongsToMany(ModifierGroup::class)
            ->withPivot('sort_order')
            ->orderByPivot('sort_order');
    }

    public function dietaryLabels(): BelongsToMany
    {
        return $this->belongsToMany(DietaryLabel::class);
    }

    public function allergens(): BelongsToMany
    {
        return $this->belongsToMany(Allergen::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_available', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeBestseller(Builder $query): Builder
    {
        return $query->where('is_bestseller', true);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Price the customer actually pays (before variations/modifiers). */
    public function effectivePrice(): float
    {
        $price = $this->discounted_price ?? $this->price;

        return (float) $price;
    }

    public function hasDiscount(): bool
    {
        return $this->discounted_price !== null
            && (float) $this->discounted_price < (float) $this->price;
    }

    /** Whether the item can be added to the cart right now. */
    public function isOrderable(): bool
    {
        if (! $this->is_available || $this->is_sold_out) {
            return false;
        }

        if ($this->stock_quantity !== null && $this->stock_quantity <= 0) {
            return false;
        }

        return $this->isWithinDailyWindow();
    }

    public function isWithinDailyWindow(?Carbon $at = null): bool
    {
        $at ??= now();

        if (is_array($this->available_days) && $this->available_days !== []
            && ! in_array((int) $at->dayOfWeek, array_map('intval', $this->available_days), true)) {
            return false;
        }

        if ($this->available_from && $at->format('H:i:s') < $this->available_from) {
            return false;
        }

        if ($this->available_until && $at->format('H:i:s') > $this->available_until) {
            return false;
        }

        return true;
    }

    public function isLowStock(): bool
    {
        return $this->stock_quantity !== null
            && $this->stock_quantity <= (int) $this->low_stock_threshold;
    }
}
