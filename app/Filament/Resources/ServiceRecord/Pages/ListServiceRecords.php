<?php

namespace App\Filament\Resources\ServiceRecord\Pages;

use App\Enums\FieldType;
use App\Filament\Resources\ServiceRecord\ServiceRecordResource;
use App\Filament\Resources\Services\ServiceResource;
use App\Models\Service;
use App\Models\ServiceRecord;
use App\Services\ExportService;
use App\Services\ImportService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Callout;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class ListServiceRecords extends ListRecords
{
    protected static string $resource = ServiceRecordResource::class;

    public ?Service $serviceModel = null;

    public ?array $importData = null;

    public function mount(): void
    {
        parent::mount();

        $serviceId = request()->query('service_id');

        if ($serviceId) {
            $this->serviceModel = Service::find($serviceId);
        }

        if (! $this->serviceModel) {
            $this->serviceModel = Service::active()->orderBy('name')->first();
        }
    }

    public function getTitle(): string
    {
        if ($this->serviceModel) {
            return $this->serviceModel->name;
        }

        return 'Service Records';
    }

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();

        if ($this->serviceModel) {
            $query->where('service_id', $this->serviceModel->id);
        }

        return $query;
    }

    protected function getTableColumns(): array
    {
        if (! $this->serviceModel) {
            return [
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Imported')
                    ->dateTime()
                    ->sortable(),
            ];
        }

        $fields = $this->serviceModel->fields()->ordered()->get();
        $columns = [];

        foreach ($fields as $field) {
            $column = TextColumn::make('data.'.$field->name)
                ->label($field->label)
                ->searchable()
                ->placeholder('-');

            if ($field->type === FieldType::Date) {
                $column->date();
            } elseif ($field->type === FieldType::Boolean) {
                $column->formatStateUsing(fn (mixed $state): ?string => match ($state) {
                    '1', 1, true => $field->true_label ?? 'Yes',
                    '0', 0, false => $field->false_label ?? 'No',
                    default => null,
                });
            } elseif ($field->type === FieldType::Link) {
                $column->url(fn (mixed $state): ?string => $state ?: null)
                    ->openUrlInNewTab();
            } elseif ($field->type === FieldType::Attachment) {
                $column->formatStateUsing(fn (mixed $state): ?string => $state ? __('Download') : null)
                    ->url(fn (mixed $state): ?string => $state ? Storage::url($state) : null)
                    ->openUrlInNewTab();
            }

            $columns[] = $column;
        }

        $columns[] = TextColumn::make('created_at')
            ->label('Imported At')
            ->dateTime()
            ->sortable()
            ->toggleable(isToggledHiddenByDefault: true);

        return $columns;
    }

    protected function getTableActions(): array
    {
        return [
            EditAction::make()
                ->slideOver()
                ->modalHeading('Edit Record')
                ->modalWidth(Width::Large)
                ->schema(fn (): array => $this->getDynamicFormFields())
                ->action(function (ServiceRecord $record, array $data): void {
                    $identifierFields = $this->serviceModel->identifierFields()->get();

                    foreach ($identifierFields as $idField) {
                        $column = 'identifier_'.$idField->identifier_order;
                        $data[$column] = $data['data'][$idField->name] ?? null;
                    }

                    $record->update($data);
                }),

            DeleteAction::make(),
        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
            DeleteBulkAction::make(),
        ];
    }

    protected function getHeaderActions(): array
    {
        if (! $this->serviceModel) {
            return [];
        }

        return [
            CreateAction::make()
                ->label('Add Record')
                ->slideOver()
                ->modalHeading('Add Record '.$this->serviceModel->name)
                ->modalWidth(Width::Large)
                ->modalSubmitAction(fn (?Action $action) => $this->serviceModel->fields()->exists() ? $action : false)
                ->createAnother(fn (): bool => $this->serviceModel->fields()->exists())
                ->schema(fn (): array => $this->getDynamicFormFields())
                ->action(function (array $data): void {
                    $data['service_id'] = $this->serviceModel->id;

                    $identifierFields = $this->serviceModel->identifierFields()->get();

                    foreach ($identifierFields as $idField) {
                        $column = 'identifier_'.$idField->identifier_order;
                        $data[$column] = $data['data'][$idField->name] ?? null;
                    }

                    ServiceRecord::create($data);

                    Notification::make()
                        ->success()
                        ->title('Record added successfully.')
                        ->send();
                }),

            Action::make('import')
                ->label('Import Data')
                ->color('info')
                ->slideOver()
                ->modalHeading('Import Data '.($this->serviceModel->name ?? ''))
                ->modalWidth(Width::Large)
                ->modalCancelActionLabel('Close')
                ->modalSubmitActionLabel('Preview & Validate')
                ->modalSubmitAction(fn (?Action $action) => $this->importData === null && $this->serviceModel->fields()->exists() ? $action : false)
                ->schema(fn (): array => $this->importData === null ? (
                    $this->serviceModel->fields()->exists()
                        ? [
                            FileUpload::make('file')
                                ->label('Excel / CSV File')
                                ->maxSize(51200)
                                ->required()
                                ->visibility('private')
                                ->directory('imports'),
                        ]
                        : [
                            Callout::make()
                                ->heading('Fields Not Configured')
                                ->description('This service has no fields configured yet. Please set up the fields before importing data.')
                                ->color('info')
                                ->icon('phosphor-wrench-bold')
                                ->footerActions([
                                    Action::make('configure')
                                        ->label('Configure Fields')
                                        ->url(fn (): ?string => $this->serviceModel ? ServiceResource::getUrl('edit', ['record' => $this->serviceModel]) : null),
                                ]),
                        ]
                ) : [])
                ->modalContent(function (): ?View {
                    if ($this->importData === null) {
                        return null;
                    }

                    return view('filament.resources.services.pages.import-preview', [
                        'data' => $this->importData,
                    ]);
                })
                ->action(function (array $data): void {
                    $file = $data['file'] ?? null;

                    if (! $file) {
                        Notification::make()
                            ->danger()
                            ->title('No File')
                            ->body('Please upload an Excel / CSV file first.')
                            ->send();

                        $this->halt();

                        return;
                    }

                    $this->processImportFile($file);

                    $this->halt();
                })
                ->extraModalFooterActions(fn (): array => $this->importData !== null ? [
                    Action::make('executeImport')
                        ->label('Import Records')
                        ->color('primary')
                        ->action(fn () => $this->executeImport()),

                    Action::make('resetImport')
                        ->label('Start Over')
                        ->color('gray')
                        ->action(fn () => $this->resetImport()),
                ] : []),

            Action::make('export')
                ->label('Export Data')
                ->color('success')
                ->slideOver()
                ->modalHeading('Export Data '.($this->serviceModel->name ?? ''))
                ->modalWidth(Width::Large)
                ->modalSubmitAction(fn (?Action $action) => $this->serviceModel->fields()->exists() ? $action : false)
                ->schema(fn (): array => $this->serviceModel->fields()->exists() ? [
                    Select::make('format')
                        ->label('Format')
                        ->options([
                            'xlsx' => 'Excel (.xlsx)',
                            'csv' => 'CSV (.csv)',
                        ])
                        ->native(false)
                        ->default('xlsx')
                        ->required(),

                    Toggle::make('include_hidden')
                        ->label('Include Hidden Fields')
                        ->helperText('Include fields configured as invisible to students.')
                        ->default(false),

                    Select::make('filter_type')
                        ->label('Filter Records')
                        ->options([
                            'all' => 'All Records',
                            'period' => 'By Import Period',
                        ])
                        ->native(false)
                        ->default('all')
                        ->live()
                        ->required(),

                    DatePicker::make('start_date')
                        ->label('Start Date')
                        ->placeholder('Select start date')
                        ->native(false)
                        ->visible(fn ($get): bool => $get('filter_type') === 'period')
                        ->required(fn ($get): bool => $get('filter_type') === 'period'),

                    DatePicker::make('end_date')
                        ->label('End Date')
                        ->placeholder('Select end date')
                        ->native(false)
                        ->afterOrEqual('start_date')
                        ->visible(fn ($get): bool => $get('filter_type') === 'period')
                        ->required(fn ($get): bool => $get('filter_type') === 'period'),
                ] : [
                    Callout::make()
                        ->heading('Fields Not Configured')
                        ->description('This service has no fields configured yet. Please set up the fields before exporting data.')
                        ->color('info')
                        ->icon('phosphor-wrench-bold')
                        ->footerActions([
                            Action::make('configure')
                                ->label('Configure Fields')
                                ->url(fn (): ?string => $this->serviceModel ? ServiceResource::getUrl('edit', ['record' => $this->serviceModel]) : null),
                        ]),
                ])
                ->action(function (array $data) {
                    try {
                        $exportService = app(ExportService::class);

                        return $exportService->export(
                            $this->serviceModel,
                            $data['format'],
                            $data['include_hidden'] ?? false,
                            $data['filter_type'] === 'period' ? $data['start_date'] : null,
                            $data['filter_type'] === 'period' ? $data['end_date'] : null
                        );
                    } catch (Throwable $e) {
                        Log::error('Export execution error: '.$e->getMessage(), [
                            'exception' => $e,
                            'service_id' => $this->serviceModel->id,
                        ]);

                        Notification::make()
                            ->danger()
                            ->title('Export Error')
                            ->body('Failed to export records: '.$e->getMessage())
                            ->send();
                    }
                }),
        ];
    }

    private function getDynamicFormFields(): array
    {
        $fields = $this->serviceModel->fields()->ordered()->get();

        if ($fields->isEmpty()) {
            return [
                Callout::make()
                    ->heading('Fields Not Configured')
                    ->description('This service has no fields configured yet. Please set up the fields before adding records.')
                    ->color('info')
                    ->icon('phosphor-wrench-bold')
                    ->footerActions([
                        Action::make('configure')
                            ->label('Configure Fields')
                            ->url(fn (): ?string => $this->serviceModel ? ServiceResource::getUrl('edit', ['record' => $this->serviceModel]) : null),
                    ]),
            ];
        }

        $components = [];

        foreach ($fields as $field) {
            $component = match ($field->type) {
                FieldType::Date => DatePicker::make('data.'.$field->name)->native(false),
                FieldType::Attachment => FileUpload::make('data.'.$field->name),
                FieldType::Boolean => Select::make('data.'.$field->name)
                    ->options([
                        '1' => $field->true_label ?? 'Yes',
                        '0' => $field->false_label ?? 'No',
                    ])
                    ->native(false),
                default => TextInput::make('data.'.$field->name),
            };

            $component
                ->label($field->label)
                ->required($field->required);

            $components[] = $component;
        }

        return $components;
    }

    public function processImportFile(mixed $file): void
    {
        $disk = config('filesystems.default');

        // Normalize array uploads to single file
        if (is_array($file)) {
            $file = array_values($file)[0] ?? null;
        }

        if (! $file) {
            Notification::make()
                ->danger()
                ->title('No File')
                ->body('Please upload an Excel / CSV file first.')
                ->send();

            return;
        }

        // Get the stored file path - with storeFiles(true) by default,
        // the file is already stored on disk and $file is a path string
        if (is_string($file)) {
            $fullPath = Storage::disk($disk)->path($file);
        } else {
            // Fallback: file is an UploadedFile object
            $path = $file->store('imports', $disk);
            $fullPath = Storage::disk($disk)->path($path);
            $file = $path; // Use this path for cleanup
        }

        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

        if (! in_array($extension, ['xlsx', 'xls', 'csv'])) {
            Notification::make()
                ->danger()
                ->title('Invalid File')
                ->body('The uploaded file must be an Excel (.xlsx, .xls) or CSV (.csv) file.')
                ->send();

            return;
        }

        try {
            $importService = app(ImportService::class);
            $parsed = $importService->parse($fullPath);

            if (empty($parsed['headers'])) {
                Notification::make()
                    ->danger()
                    ->title('Invalid File')
                    ->body('Could not read headers from the file.')
                    ->send();

                return;
            }

            $this->importData = $importService->validate(
                $this->serviceModel,
                $parsed['headers'],
                $parsed['rows']
            );
        } catch (Throwable $e) {
            Log::error('Import parse error: '.$e->getMessage(), [
                'exception' => $e,
                'service_id' => $this->serviceModel->id,
            ]);

            Notification::make()
                ->danger()
                ->title('Parse Error')
                ->body($e->getMessage())
                ->send();
        } finally {
            if (is_string($file) && Storage::disk($disk)->exists($file)) {
                Storage::disk($disk)->delete($file);
            }
        }
    }

    public function executeImport(): void
    {
        $identifierFields = $this->serviceModel->identifierFields()->get();

        $validRows = array_values(
            array_filter($this->importData['rows'], fn (array $row): bool => empty($row['errors']))
        );

        if (empty($validRows)) {
            Notification::make()
                ->danger()
                ->title('No Valid Rows')
                ->body('There are no valid rows to import.')
                ->send();

            return;
        }

        try {
            $importService = app(ImportService::class);
            $count = $importService->import($this->serviceModel, $identifierFields, $validRows);

            $this->importData = null;

            Notification::make()
                ->success()
                ->title('Import Complete')
                ->body("Successfully imported {$count} records.")
                ->send();

            $this->dispatch('close-modal', id: 'mountedActionModal');
        } catch (Throwable $e) {
            Log::error('Import execution error: '.$e->getMessage(), [
                'exception' => $e,
                'service_id' => $this->serviceModel->id,
            ]);

            Notification::make()
                ->danger()
                ->title('Import Error')
                ->body($e->getMessage())
                ->send();
        }
    }

    public function resetImport(): void
    {
        $this->importData = null;
    }
}
