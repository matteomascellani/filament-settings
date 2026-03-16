<?php

namespace Matteomascellani\FilamentSettings\Filament\Resources\Settings\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ValuesRelationManager extends RelationManager
{
    protected static string $relationship = 'values';

    protected static ?string $title = 'Valori';

    /**
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    protected function getValueSchema(): array
    {
        $valueType = (string) ($this->getOwnerRecord()->value_type ?? 'string');

        return [
            Section::make('Valore impostazione')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label('Nome')
                        ->placeholder('default')
                        ->maxLength(255),

                    TextInput::make('sort')
                        ->label('Ordine')
                        ->numeric()
                        ->default(0)
                        ->minValue(0),

                    ColorPicker::make('value')
                        ->label('Valore')
                        ->visible(fn (): bool => $valueType === 'color')
                        ->required(fn (): bool => $valueType === 'color')
                        ->columnSpanFull(),

                    Toggle::make('value_boolean')
                        ->label('Valore')
                        ->visible(fn (): bool => $valueType === 'boolean')
                        ->dehydrated(false)
                        ->afterStateHydrated(function (Toggle $component, $state, ?\Illuminate\Database\Eloquent\Model $record): void {
                            $raw = (string) ($record?->value ?? '0');
                            $component->state(in_array(strtolower($raw), ['1', 'true', 'yes'], true));
                        })
                        ->afterStateUpdated(function ($state, callable $set): void {
                            $set('value', $state ? '1' : '0');
                        }),

                    TextInput::make('value')
                        ->label('Valore')
                        ->visible(fn (): bool => in_array($valueType, ['string', 'number'], true))
                        ->numeric(fn (): bool => $valueType === 'number')
                        ->required(fn (): bool => in_array($valueType, ['string', 'number'], true))
                        ->columnSpanFull(),

                    Textarea::make('value')
                        ->label('Valore JSON')
                        ->visible(fn (): bool => $valueType === 'json')
                        ->rows(6)
                        ->required(fn (): bool => $valueType === 'json')
                        ->columnSpanFull(),

                    Select::make('settable_type')
                        ->label('Applica a modello')
                        ->placeholder('Globale (nessun modello)')
                        ->options($this->getSettableModelOptions())
                        ->live(),

                    TextInput::make('settable_id')
                        ->label('ID modello')
                        ->numeric()
                        ->minValue(1)
                        ->nullable()
                        ->visible(fn (callable $get): bool => filled($get('settable_type'))),
                ]),
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function getSettableModelOptions(): array
    {
        $configured = config('filament-settings.settable_models', []);

        if (is_array($configured) && $configured !== []) {
            return array_filter(
                $configured,
                fn (mixed $label, mixed $class): bool => is_string($class) && is_string($label) && class_exists($class),
                ARRAY_FILTER_USE_BOTH,
            );
        }

        $fallback = [
            'App\\Models\\User' => 'User',
            'App\\Models\\Product' => 'Product',
            'App\\Models\\Reservation' => 'Reservation',
        ];

        return array_filter(
            $fallback,
            fn (string $label, string $class): bool => class_exists($class),
            ARRAY_FILTER_USE_BOTH,
        );
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->default('default')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('value')
                    ->label('Valore')
                    ->limit(70)
                    ->searchable(),

                TextColumn::make('settable_type')
                    ->label('Modello')
                    ->formatStateUsing(function (?string $state): string {
                        if (blank($state)) {
                            return 'Globale';
                        }

                        $options = $this->getSettableModelOptions();

                        return $options[$state] ?? class_basename($state);
                    }),

                TextColumn::make('settable_id')
                    ->label('Model ID')
                    ->placeholder('-'),

                TextColumn::make('sort')
                    ->label('Ordine')
                    ->sortable(),
            ])
            ->defaultSort('sort')
            ->headerActions([
                CreateAction::make()
                    ->schema($this->getValueSchema())
                    ->label('Aggiungi valore'),
            ])
            ->recordActions([
                EditAction::make()
                    ->schema($this->getValueSchema()),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}
