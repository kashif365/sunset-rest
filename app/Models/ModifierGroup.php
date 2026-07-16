<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModifierGroup extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'selection_type', 'min_select', 'max_select', 'is_required', 'sort_order'];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
        ];
    }

    public function options(): HasMany
    {
        return $this->hasMany(ModifierOption::class)->orderBy('sort_order');
    }

    public function availableOptions(): HasMany
    {
        return $this->options()->where('is_available', true);
    }

    public function menuItems(): BelongsToMany
    {
        return $this->belongsToMany(MenuItem::class);
    }
}
