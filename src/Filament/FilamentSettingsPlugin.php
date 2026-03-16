<?php

namespace Matteomascellani\FilamentSettings\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Matteomascellani\FilamentSettings\Filament\Resources\Settings\SettingResource;

class FilamentSettingsPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'filament-settings';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            SettingResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
