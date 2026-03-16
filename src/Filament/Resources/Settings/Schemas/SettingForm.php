<?php

namespace Matteomascellani\FilamentSettings\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dettagli impostazione')
                    ->columns(2)
                    ->schema([
                        TextInput::make('label')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('key')
                            ->label('Chiave')
                            ->required()
                            ->maxLength(255)
                            ->unique('settings', 'key', ignoreRecord: true)
                            ->placeholder('theme.primary'),

                        TextInput::make('group')
                            ->label('Gruppo')
                            ->required()
                            ->maxLength(100)
                            ->default('theme')
                            ->placeholder('theme'),

                        Select::make('value_type')
                            ->label('Tipo valore')
                            ->options([
                                'string' => 'Stringa',
                                'color' => 'Colore',
                                'number' => 'Numero',
                                'boolean' => 'Booleano',
                                'json' => 'JSON',
                            ])
                            ->default('string')
                            ->required(),

                        Toggle::make('is_active')
                            ->label('Attivo')
                            ->default(true),

                        TextInput::make('sort')
                            ->label('Ordine')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        Textarea::make('description')
                            ->label('Descrizione')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
