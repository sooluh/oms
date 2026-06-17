<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Guava\IconPicker\Forms\Components\IconPicker;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make()
                    ->columns(12)
                    ->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->columnSpan(6)
                            ->afterStateUpdated(function (string $operation, $state, $set) {
                                if ($operation === 'create') {
                                    $set('slug', str($state)->slug());
                                }
                            }),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->afterStateUpdated(fn ($state, $set) => $set('slug', str($state)->slug()))
                            ->columnSpan(6),

                        IconPicker::make('icon')
                            ->label('Icon')
                            ->columns(6)
                            ->gridSearchResults()
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->columnSpanFull(),

                        ToggleButtons::make('search_mode')
                            ->label('Search Mode')
                            ->options([
                                'OR' => 'OR Mode',
                                'AND' => 'AND Mode',
                            ])
                            ->default('OR')
                            ->required()
                            ->grouped()
                            ->helperText('OR Mode: Any identifier is enough. AND Mode: All identifiers must match.')
                            ->columnSpanFull(),

                        DatePicker::make('start_date')
                            ->label('Start Date')
                            ->native(false)
                            ->columnSpan(6),

                        DatePicker::make('end_date')
                            ->label('End Date')
                            ->native(false)
                            ->afterOrEqual('start_date')
                            ->columnSpan(6),

                        TextInput::make('max_attempt')
                            ->label('Max Attempts')
                            ->numeric()
                            ->minValue(1)
                            ->helperText('Maximum search attempts before lock (leave empty for unlimited)')
                            ->columnSpan(4),

                        TextInput::make('attempt_duration')
                            ->label('Lock Duration (minutes)')
                            ->numeric()
                            ->minValue(1)
                            ->helperText('How long to lock after max attempts reached')
                            ->columnSpan(4),

                        TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->columnSpan(4),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
