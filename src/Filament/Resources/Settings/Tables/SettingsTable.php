<?php

namespace Matteomascellani\FilamentSettings\Filament\Resources\Settings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Matteomascellani\FilamentSettings\Models\Setting;

class SettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('key')
                    ->label('Chiave')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('group')
                    ->label('Gruppo')
                    ->badge()
                    ->sortable(),

                TextColumn::make('value_type')
                    ->label('Tipo')
                    ->badge()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Attivo')
                    ->boolean(),

                TextColumn::make('values_count')
                    ->label('Valori')
                    ->counts('values')
                    ->sortable(),

                TextColumn::make('sort')
                    ->label('Ordine')
                    ->sortable(),
            ])
            ->defaultSort('sort')
            ->reorderable('sort')
            ->filters([
                SelectFilter::make('group')
                    ->label('Gruppo')
                    ->options(function (): array {
                        return Setting::query()
                            ->select('group')
                            ->distinct()
                            ->orderBy('group')
                            ->pluck('group', 'group')
                            ->toArray();
                    }),
                TernaryFilter::make('is_active')
                    ->label('Attivo'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
