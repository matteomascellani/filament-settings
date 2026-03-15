<?php

namespace Matteomascellani\FilamentSettings;

use Illuminate\Support\ServiceProvider;

class FilamentSettingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/filament-settings.php',
            'filament-settings'
        );
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->publishes([
            __DIR__ . '/../config/filament-settings.php' => config_path('filament-settings.php'),
        ], 'filament-settings-config');

        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations'),
        ], 'filament-settings-migrations');
    }
}
