<?php

namespace Matteomascellani\FilamentSettings\Filament\Resources\Settings;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Matteomascellani\FilamentSettings\Filament\Resources\Settings\Pages\CreateSetting;
use Matteomascellani\FilamentSettings\Filament\Resources\Settings\Pages\EditSetting;
use Matteomascellani\FilamentSettings\Filament\Resources\Settings\Pages\ListSettings;
use Matteomascellani\FilamentSettings\Filament\Resources\Settings\RelationManagers\ValuesRelationManager;
use Matteomascellani\FilamentSettings\Filament\Resources\Settings\Schemas\SettingForm;
use Matteomascellani\FilamentSettings\Filament\Resources\Settings\Tables\SettingsTable;
use Matteomascellani\FilamentSettings\Models\Setting;
use UnitEnum;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static UnitEnum|string|null $navigationGroup = 'Sistema';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Impostazioni';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'impostazione';

    protected static ?string $pluralModelLabel = 'impostazioni';

    public static function form(Schema $schema): Schema
    {
        return SettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SettingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ValuesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSettings::route('/'),
            'create' => CreateSetting::route('/create'),
            'edit' => EditSetting::route('/{record}/edit'),
        ];
    }
}
