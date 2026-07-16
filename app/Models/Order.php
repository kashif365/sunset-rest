<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    public const STATUSES = ['pending', 'confirmed', 'preparing', 'ready', 'completed', 'cancelled'];

    protected $fillable = [
        'order_number', 'customer_id', 'customer_name', 'customer_email', 'customer_phone',
        'status', 'order_type', 'payment_method', 'payment_status',
        'pickup_date', 'pickup_time', 'subtotal', 'tax', 'tip', 'discount', 'total',
        'coupon_id', 'coupon_code', 'notes', 'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'pickup_date' => 'date',
            'subtotal' => 'decimal:2',
            'tax' => 'decimal:2',
            'tip' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public static function generateOrderNumber(): string
    {
        do {
            $number = 'SBE-'.now()->format('ymd').'-'.strtoupper(str()->random(4));
        } while (static::where('order_number', $number)->exists());

        return $number;
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'pending' => 'pending',
            'confirmed' => 'confirmed',
            'preparing' => 'preparing',
            'ready' => 'ready',
            'completed' => 'completed',
            default => 'cancelled',
        };
    }
}
