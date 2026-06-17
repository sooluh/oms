<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Enums\FieldType;
use App\Enums\SearchMode;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Guava\IconPicker\Forms\Components\IconPicker;
use YousefAman\ModalRepeater\Column;
use YousefAman\ModalRepeater\ModalRepeater;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make()
                    ->columns(2)
                    ->columnSpan(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set, $context) {
                                if ($context === 'create') {
                                    $set('slug', str($state)->slug());
                                }
                            }),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        IconPicker::make('icon')
                            ->label('Icon')
                            ->placeholder('Search for an icon...')
                            ->closeOnSelect()
                            ->sets(['phosphor-icons'])
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Description')
                            ->placeholder('Brief description of this service...')
                            ->rows(3)
                            ->columnSpanFull(),

                        ToggleButtons::make('search_mode')
                            ->label('Search Mode')
                            ->options(SearchMode::class)
                            ->default(SearchMode::Or)
                            ->required()
                            ->grouped()
                            ->live()
                            ->helperText(fn ($get): string => $get('search_mode') === SearchMode::Or
                                ? 'Searches across all identifiers — a match on any one is enough.'
                                : 'Searches across all identifiers — every one must match.')
                            ->columnSpanFull(),

                        DatePicker::make('start_date')
                            ->label('Start Date')
                            ->placeholder('Select start date')
                            ->native(false)
                            ->columnSpan(1),

                        DatePicker::make('end_date')
                            ->label('End Date')
                            ->placeholder('Select end date')
                            ->native(false)
                            ->afterOrEqual('start_date')
                            ->columnSpan(1),
                    ]),

                Section::make()
                    ->columnSpan(1)
                    ->schema([
                        TextInput::make('max_attempt')
                            ->label('Max Attempts')
                            ->placeholder('Unlimited')
                            ->numeric()
                            ->minValue(1)
                            ->helperText('Maximum search attempts before lock (leave empty for unlimited)'),

                        TextInput::make('attempt_duration')
                            ->label('Lock Duration (minutes)')
                            ->placeholder('e.g., 30')
                            ->numeric()
                            ->minValue(1)
                            ->helperText('How long to lock after max attempts reached'),

                        TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->inline(false)
                            ->default(true),
                    ]),

                Section::make('Fields')
                    ->columnSpanFull()
                    ->description('Define the fields and identifiers for this service. Maximum 5 identifiers allowed.')
                    ->visible(fn (string $operation): bool => $operation === 'edit')
                    ->schema([
                        ModalRepeater::make('fields')
                            ->hiddenLabel()
                            ->relationship()
                            ->orderColumn('sort_order')
                            ->defaultItems(0)
                            ->addActionLabel('Add Field')
                            ->emptyLabel('No fields defined yet.')
                            ->addAction(fn (Action $action) => $action
                                ->slideOver()
                                ->before(function (array $data, Action $action, array $arguments) {
                                    if (! ($data['is_identifier'] ?? false)) {
                                        return;
                                    }

                                    $itemKey = $arguments['item'] ?? null;
                                    $fields = collect(data_get($action->getLivewire(), 'data.fields', []));

                                    if ($itemKey) {
                                        $fields = $fields->reject(fn (mixed $value, string|int $key) => (string) $key === (string) $itemKey);
                                    }

                                    if ($fields->where('is_identifier', true)->count() >= 5) {
                                        Notification::make()
                                            ->warning()
                                            ->title('Maximum 5 identifiers allowed per service.')
                                            ->send();

                                        $action->halt();
                                    }
                                }))
                            ->editAction(fn (Action $action) => $action
                                ->slideOver()
                                ->before(function (array $data, Action $action, array $arguments) {
                                    if (! ($data['is_identifier'] ?? false)) {
                                        return;
                                    }

                                    $itemKey = $arguments['item'] ?? null;
                                    $fields = collect(data_get($action->getLivewire(), 'data.fields', []));

                                    if ($itemKey) {
                                        $fields = $fields->reject(fn (mixed $value, string|int $key) => (string) $key === (string) $itemKey);
                                    }

                                    if ($fields->where('is_identifier', true)->count() >= 5) {
                                        Notification::make()
                                            ->warning()
                                            ->title('Maximum 5 identifiers allowed per service.')
                                            ->send();

                                        $action->halt();
                                    }
                                }))
                            ->modalColumns(1)
                            ->modalWidth(Width::Large)
                            ->tableColumns([
                                Column::make('name')->label('Name'),
                                Column::make('label')->label('Label'),
                                Column::make('type')
                                    ->label('Type')
                                    ->badge()
                                    ->formatUsing(fn (?FieldType $value): string => $value?->getLabel() ?? '-'),
                                Column::make('required')->label('Required')->boolean(),
                                Column::make('visible')->label('Visible')->boolean(),
                                Column::make('is_identifier')->label('Identifier')->boolean(),
                            ])
                            ->schema([
                                TextInput::make('name')
                                    ->label('Column Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->rules(['regex:/^[a-z][a-z0-9_]*$/'])
                                    ->helperText('e.g., nis, nama, kelas'),

                                TextInput::make('label')
                                    ->label('Display Label')
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('e.g., NIS, Nama Siswa, Kelas'),

                                Select::make('type')
                                    ->label('Type')
                                    ->options(FieldType::class)
                                    ->native(false)
                                    ->required()
                                    ->default(FieldType::Text)
                                    ->live(),

                                TextInput::make('true_label')
                                    ->label('True Label')
                                    ->placeholder('e.g., Yes, Lulus, Aktif')
                                    ->visible(fn ($get): bool => $get('type') === FieldType::Boolean || $get('type') === FieldType::Boolean->value)
                                    ->required(fn ($get): bool => $get('type') === FieldType::Boolean || $get('type') === FieldType::Boolean->value)
                                    ->maxLength(50),

                                TextInput::make('false_label')
                                    ->label('False Label')
                                    ->placeholder('e.g., No, Tidak Lulus, Tidak Aktif')
                                    ->visible(fn ($get): bool => $get('type') === FieldType::Boolean || $get('type') === FieldType::Boolean->value)
                                    ->required(fn ($get): bool => $get('type') === FieldType::Boolean || $get('type') === FieldType::Boolean->value)
                                    ->maxLength(50),

                                Toggle::make('required')
                                    ->label('Required')
                                    ->inline(false)
                                    ->default(false),

                                Toggle::make('visible')
                                    ->label('Visible to Students')
                                    ->inline(false)
                                    ->default(true),

                                Toggle::make('is_identifier')
                                    ->label('Mark as Identifier')
                                    ->inline(false)
                                    ->default(false)
                                    ->live(),
                            ]),
                    ]),
            ]);
    }
}
