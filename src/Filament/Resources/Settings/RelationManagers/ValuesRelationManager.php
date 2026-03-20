<?php

namespace Matteomascellani\FilamentSettings\Filament\Resources\Settings\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ValuesRelationManager extends RelationManager
{
    protected static string $relationship = 'values';

    protected static ?string $title = 'Valori';

    /**
     * @return array<string, string>
     */
    protected function getWeekdayOptions(): array
    {
        return [
            'monday' => 'Lunedi',
            'tuesday' => 'Martedi',
            'wednesday' => 'Mercoledi',
            'thursday' => 'Giovedi',
            'friday' => 'Venerdi',
            'saturday' => 'Sabato',
            'sunday' => 'Domenica',
        ];
    }

    protected function normalizeTimeValue(mixed $time): ?string
    {
        if (! is_string($time) || trim($time) === '') {
            return null;
        }

        $time = trim($time);

        if (preg_match('/^\d{2}:\d{2}$/', $time) === 1) {
            return $time . ':00';
        }

        if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $time) === 1) {
            return $time;
        }

        return null;
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return array<int, array<string, string>>
     */
    protected function normalizeHoursDaysRanges(array $rows): array
    {
        $allowedDays = array_keys($this->getWeekdayOptions());

        $normalized = [];

        foreach ($rows as $row) {
            $day = is_string($row['day'] ?? null) ? strtolower(trim((string) $row['day'])) : null;
            $start = $this->normalizeTimeValue($row['start_time'] ?? null);
            $end = $this->normalizeTimeValue($row['end_time'] ?? null);

            if (! $day || ! in_array($day, $allowedDays, true) || ! $start || ! $end || $start >= $end) {
                continue;
            }

            $normalized[] = [
                'day' => $day,
                'start_time' => $start,
                'end_time' => $end,
            ];
        }

        return $normalized;
    }

    /**
     * @return array<string, string>
     */
    protected function getQuarterHourTimeOptions(): array
    {
        $options = [];

        for ($minutes = 0; $minutes < (24 * 60); $minutes += 15) {
            $hours = intdiv($minutes, 60);
            $mins = $minutes % 60;
            $time = sprintf('%02d:%02d:00', $hours, $mins);
            $options[$time] = $time;
        }

        return $options;
    }

    protected function timeToMinutes(?string $time): ?int
    {
        $normalized = $this->normalizeTimeValue($time);

        if (! $normalized) {
            return null;
        }

        [$hours, $minutes] = explode(':', $normalized);

        return ((int) $hours * 60) + (int) $minutes;
    }

    /**
     * @return array<string, string>
     */
    protected function getEndTimeOptions(?string $startTime): array
    {
        $all = $this->getQuarterHourTimeOptions();
        $startMinutes = $this->timeToMinutes($startTime);

        if ($startMinutes === null) {
            return $all;
        }

        return collect($all)
            ->filter(function (string $label, string $value) use ($startMinutes): bool {
                $endMinutes = $this->timeToMinutes($value);

                return $endMinutes !== null && $endMinutes > $startMinutes;
            })
            ->all();
    }

    /**
     * @return array<int, array<string, string>>
     */
    protected function decodeHoursDaysRanges(?string $value): array
    {
        if (! is_string($value) || trim($value) === '') {
            return [];
        }

        try {
            $decoded = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable) {
            return [];
        }

        if (! is_array($decoded)) {
            return [];
        }

        /** @var array<int, array<string, mixed>> $decoded */
        return $this->normalizeHoursDaysRanges($decoded);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateValueData(array $data): array
    {
        $valueType = (string) ($this->getOwnerRecord()->value_type ?? 'string');

        if ($valueType !== 'hours_days_range') {
            unset($data['value_day_time_ranges']);

            return $data;
        }

        $ranges = is_array($data['value_day_time_ranges'] ?? null) ? $data['value_day_time_ranges'] : [];

        $data['value'] = json_encode(
            $this->normalizeHoursDaysRanges($ranges),
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
        ) ?: '[]';

        unset($data['value_day_time_ranges']);

        return $data;
    }

    protected function formatHoursDaysRangeValue(?string $value): string
    {
        $rows = $this->decodeHoursDaysRanges($value);

        if ($rows === []) {
            return '-';
        }

        $dayLabels = $this->getWeekdayOptions();

        return collect($rows)
            ->map(function (array $row) use ($dayLabels): string {
                $day = $dayLabels[$row['day']] ?? $row['day'];

                return sprintf('%s %s-%s', $day, $row['start_time'], $row['end_time']);
            })
            ->implode(' | ');
    }

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

                    Repeater::make('value_day_time_ranges')
                        ->label('Range ore e giorni')
                        ->visible(fn (): bool => $valueType === 'hours_days_range')
                        ->required(fn (): bool => $valueType === 'hours_days_range')
                        ->dehydrated(true)
                        ->defaultItems(1)
                        ->minItems(1)
                        ->maxItems(7)
                        ->reorderable(false)
                        ->schema([
                            Select::make('day')
                                ->label('Giorno')
                                ->options($this->getWeekdayOptions())
                                ->required()
                                ->searchable()
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                            Select::make('start_time')
                                ->label('Ora inizio')
                                ->options($this->getQuarterHourTimeOptions())
                                ->required()
                                ->searchable()
                                ->afterStateUpdated(function ($state, callable $set, callable $get): void {
                                    $endTime = $get('end_time');
                                    $startMinutes = $this->timeToMinutes(is_string($state) ? $state : null);
                                    $endMinutes = $this->timeToMinutes(is_string($endTime) ? $endTime : null);

                                    if ($startMinutes !== null && $endMinutes !== null && $endMinutes <= $startMinutes) {
                                        $set('end_time', null);
                                    }
                                }),

                            Select::make('end_time')
                                ->label('Ora fine')
                                ->options(fn (callable $get): array => $this->getEndTimeOptions(
                                    is_string($get('start_time')) ? $get('start_time') : null
                                ))
                                ->required()
                                ->searchable(),
                        ])
                        ->afterStateHydrated(function (Repeater $component, mixed $state, ?Model $record): void {
                            if (is_array($state)) {
                                $component->state($this->normalizeHoursDaysRanges(array_values($state)));

                                return;
                            }

                            if ($record) {
                                $component->state($this->decodeHoursDaysRanges((string) ($record->value ?? '')));
                            }
                        })
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
            'App\\Models\\User' => 'Utenti',
            'App\\Models\\Product' => 'Prodotti',
            'App\\Models\\Reservation' => 'Prenotazioni',
            'App\\Models\\ReservationNight' => 'Notti prenotazione',
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
                    ->formatStateUsing(fn (?string $state): string => (string) (($this->getOwnerRecord()->value_type ?? 'string') === 'hours_days_range'
                        ? $this->formatHoursDaysRangeValue($state)
                        : ($state ?? '-')))
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
                    ->mutateDataUsing(fn (array $data): array => $this->mutateValueData($data))
                    ->label('Aggiungi valore'),
            ])
            ->recordActions([
                EditAction::make()
                    ->schema($this->getValueSchema())
                    ->mutateDataUsing(fn (array $data): array => $this->mutateValueData($data)),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}
