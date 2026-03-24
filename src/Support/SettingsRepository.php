<?php

namespace Matteomascellani\FilamentSettings\Support;

use Illuminate\Database\Eloquent\Model;
use Matteomascellani\FilamentSettings\Models\Setting;

class SettingsRepository
{
    public function value(string $key, ?Model $scope = null, mixed $fallback = null): mixed
    {
        $setting = Setting::query()
            ->where('key', $key)
            ->where('is_active', true)
            ->first();

        if (! $setting) {
            return config("filament-settings.defaults.{$key}", $fallback);
        }

        if ($scope) {
            $scopedValue = $setting->values()
                ->where('settable_type', $scope::class)
                ->where('settable_id', $scope->getKey())
                ->orderBy('sort')
                ->orderBy('id')
                ->value('value');

            if ($scopedValue !== null) {
                return $this->normalizeMediaUrl($setting, $scopedValue);
            }
        }

        $globalValue = $setting->values()
            ->whereNull('settable_type')
            ->whereNull('settable_id')
            ->orderBy('sort')
            ->orderBy('id')
            ->value('value');

        if ($globalValue !== null) {
            return $this->normalizeMediaUrl($setting, $globalValue);
        }

        return config("filament-settings.defaults.{$key}", $fallback);
    }

    /**
     * For media-type settings, rewrite absolute URLs to use the current APP_URL.
     * This makes the value portable across environments (tunnels, staging, production).
     */
    protected function normalizeMediaUrl(Setting $setting, mixed $value): mixed
    {
        if ($setting->value_type !== 'media' || ! is_string($value) || $value === '') {
            return $value;
        }

        // If stored as absolute URL, replace its origin with the current APP_URL.
        if (str_starts_with($value, 'http')) {
            $parsed = parse_url($value);
            $path = ($parsed['path'] ?? '') . (isset($parsed['query']) ? '?' . $parsed['query'] : '');

            return rtrim(config('app.url'), '/') . $path;
        }

        // If stored as relative path (/storage/...), prepend APP_URL.
        if (str_starts_with($value, '/')) {
            return rtrim(config('app.url'), '/') . $value;
        }

        return $value;
    }
}
