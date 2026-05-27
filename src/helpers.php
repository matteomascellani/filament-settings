<?php

use Illuminate\Database\Eloquent\Model;
use Matteomascellani\FilamentSettings\Support\SettingsRepository;

if (! function_exists('filament_setting')) {
    function filament_setting(string $key, ?Model $scope = null, mixed $fallback = null): mixed
    {
        return app(SettingsRepository::class)->value($key, $scope, $fallback);
    }
}

if (! function_exists('filament_setting_values')) {
    function filament_setting_values(string $key, ?Model $scope = null): array
    {
        return app(SettingsRepository::class)->values($key, $scope);
    }
}
