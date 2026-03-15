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
                return $scopedValue;
            }
        }

        $globalValue = $setting->values()
            ->whereNull('settable_type')
            ->whereNull('settable_id')
            ->orderBy('sort')
            ->orderBy('id')
            ->value('value');

        if ($globalValue !== null) {
            return $globalValue;
        }

        return config("filament-settings.defaults.{$key}", $fallback);
    }
}
