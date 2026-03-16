<?php

namespace Matteomascellani\FilamentSettings\Filament\Resources\Settings\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Matteomascellani\FilamentSettings\Filament\Resources\Settings\SettingResource;

class ListSettings extends ListRecords
{
    protected static string $resource = SettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
