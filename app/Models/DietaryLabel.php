<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DietaryLabel extends Model
{
    protected $fillable = ['name', 'slug', 'icon'];

    public function menuItems(): BelongsToMany
    {
        return $this->belongsToMany(MenuItem::class);
    }
}
