<?php

use Illuminate\Database\Eloquent\Model;
use Matteomascellani\FilamentSettings\Support\SettingsRepository;

if (! function_exists('filament_setting')) {
    function filament_setting(string $key, ?Model $scope = null, mixed $fallback = null): mixed
    {
        return app(SettingsRepository::class)->value($key, $scope, $fallback);
    }
}
