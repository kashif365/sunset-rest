<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    private const CACHE_KEY = 'sbe.settings';

    /** @var array<string, string|null>|null */
    private ?array $loaded = null;

    /** @return array<string, string|null> */
    public function all(): array
    {
        if ($this->loaded === null) {
            $this->loaded = Cache::rememberForever(self::CACHE_KEY, function () {
                return Setting::query()->pluck('value', 'key')->all();
            });
        }

        return $this->loaded;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->all()[$key] ?? $default;
    }

    public function bool(string $key, bool $default = false): bool
    {
        $value = $this->get($key);

        return $value === null ? $default : filter_var($value, FILTER_VALIDATE_BOOL);
    }

    public function float(string $key, float $default = 0.0): float
    {
        $value = $this->get($key);

        return is_numeric($value) ? (float) $value : $default;
    }

    public function int(string $key, int $default = 0): int
    {
        $value = $this->get($key);

        return is_numeric($value) ? (int) $value : $default;
    }

    public function set(string $key, mixed $value, string $group = 'general'): void
    {
        Setting::updateOrCreate(['key' => $key], ['value' => $value, 'group' => $group]);
        $this->flush();
    }

    /** @param array<string, mixed> $values */
    public function setMany(array $values, string $group = 'general'): void
    {
        foreach ($values as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value, 'group' => $group]);
        }
        $this->flush();
    }

    public function flush(): void
    {
        $this->loaded = null;
        Cache::forget(self::CACHE_KEY);
    }
}
