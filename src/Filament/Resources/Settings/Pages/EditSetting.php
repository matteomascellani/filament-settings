<?php

namespace Matteomascellani\FilamentSettings\Filament\Resources\Settings\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Matteomascellani\FilamentSettings\Filament\Resources\Settings\SettingResource;

class EditSetting extends EditRecord
{
    protected static string $resource = SettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
