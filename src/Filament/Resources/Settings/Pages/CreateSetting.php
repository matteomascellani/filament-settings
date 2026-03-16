<?php

namespace Matteomascellani\FilamentSettings\Filament\Resources\Settings\Pages;

use Filament\Resources\Pages\CreateRecord;
use Matteomascellani\FilamentSettings\Filament\Resources\Settings\SettingResource;

class CreateSetting extends CreateRecord
{
    protected static string $resource = SettingResource::class;
}
