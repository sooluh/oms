<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Guava\IconPicker\Forms\Components\IconPicker;

class ServiceForm
{
    public static function getComponents(): array
    {
        return [
            Section::make()
                ->columns(12)
                ->schema([
                    TextInput::make('name')
                        ->label('Name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->columnSpan(6)
                        ->afterStateUpdated(function ($state, $set, $context) {
                            if ($context === 'create') {
                                $set('slug', str($state)->slug());
                            }
                        }),

                    TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->columnSpan(6),

                    IconPicker::make('icon')
                        ->label('Icon')
                        ->placeholder('Search for an icon...')
                        ->columns(6)
                        ->gridSearchResults()
                        ->columnSpanFull(),

                    Textarea::make('description')
                        ->label('Description')
                        ->placeholder('Brief description of this service...')
                        ->rows(3)
                        ->columnSpanFull(),

                    ToggleButtons::make('search_mode')
                        ->label('Search Mode')
                        ->options([
                            'OR' => 'OR',
                            'AND' => 'AND',
                        ])
                        ->default('OR')
                        ->required()
                        ->grouped()
                        ->helperText('OR: Any identifier is enough. AND: All identifiers must match.')
                        ->columnSpanFull(),

                    DatePicker::make('start_date')
                        ->label('Start Date')
                        ->placeholder('Select start date')
                        ->native(false)
                        ->columnSpan(6),

                    DatePicker::make('end_date')
                        ->label('End Date')
                        ->placeholder('Select end date')
                        ->native(false)
                        ->afterOrEqual('start_date')
                        ->columnSpan(6),

                    TextInput::make('max_attempt')
                        ->label('Max Attempts')
                        ->placeholder('Unlimited')
                        ->numeric()
                        ->minValue(1)
                        ->helperText('Maximum search attempts before lock (leave empty for unlimited)')
                        ->columnSpan(4),

                    TextInput::make('attempt_duration')
                        ->label('Lock Duration (minutes)')
                        ->placeholder('e.g., 30')
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
                        ->inline(false)
                        ->default(true)
                        ->columnSpanFull(),
                ]),

            Section::make('Fields')
                ->description('Define the fields and identifiers for this service. Maximum 3 identifiers allowed.')
                ->visible(fn (string $operation): bool => $operation === 'edit')
                ->schema([
                    Repeater::make('fields')
                        ->relationship()
                        ->orderColumn('sort_order')
                        ->defaultItems(0)
                        ->addActionLabel('Add Field')
                        ->schema([
                            TextInput::make('name')
                                ->label('Column Name')
                                ->required()
                                ->maxLength(255)
                                ->rules(['regex:/^[a-z][a-z0-9_]*$/'])
                                ->helperText('e.g., nis, nama, kelas')
                                ->columnSpan(4),

                            TextInput::make('label')
                                ->label('Display Label')
                                ->required()
                                ->maxLength(255)
                                ->helperText('e.g., NIS, Nama Siswa, Kelas')
                                ->columnSpan(4),

                            Select::make('type')
                                ->label('Type')
                                ->options([
                                    'text' => 'Text',
                                    'long_text' => 'Long Text',
                                    'number' => 'Number',
                                    'date' => 'Date',
                                ])
                                ->native(false)
                                ->required()
                                ->default('text')
                                ->columnSpan(4),

                            Toggle::make('required')
                                ->label('Required')
                                ->inline(false)
                                ->default(false)
                                ->columnSpan(4),

                            Toggle::make('visible')
                                ->label('Visible to Students')
                                ->inline(false)
                                ->default(true)
                                ->columnSpan(4),

                            Toggle::make('is_identifier')
                                ->label('Mark as Identifier')
                                ->inline(false)
                                ->default(false)
                                ->live()
                                ->columnSpan(4),
                        ])
                        ->columns(12),
                ]),
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components(static::getComponents());
    }
}
