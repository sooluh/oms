<?php

namespace App\Services;

use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;

class RecordService
{
    private static ?RecordService $instance = null;

    private ?Page $page = null;

    private function __construct(?string $slug)
    {
        $this->page = Page::query()->where('is_active', true)->where('slug', $slug)->first();
    }

    public static function getInstance(?string $slug): ?RecordService
    {
        if (self::$instance === null || self::$instance->page->slug !== $slug) {
            self::$instance = new self($slug);
        }

        return self::$instance;
    }

    public function getPage(): ?Page
    {
        return $this->page;
    }

    public function getForm(Form $form): Form
    {
        if (! $this->page) {
            return $form->schema([]);
        }

        $schema = collect($this->page->columns)->map(function ($column) {
            $column = (object) $column;

            if ($column->type === 'attachment') {
                return Forms\Components\Repeater::make($column->key)
                    ->label($column->label)
                    ->schema([
                        Forms\Components\TextInput::make('filename')
                            ->label('Nama Berkas'),

                        Forms\Components\TextInput::make('url')
                            ->label('Tautan')
                            ->url(),
                    ])
                    ->required((bool) $column->is_required)
                    ->defaultItems(3)
                    ->addable(false)
                    ->deletable(false)
                    ->reorderable(false)
                    ->columns(2)
                    ->columnSpan('full');
            }

            if ($column->type === 'boolean') {
                return Forms\Components\Radio::make($column->key)
                    ->label($column->label)
                    ->boolean()
                    ->inline()
                    ->inlineLabel(false)
                    ->required((bool) $column->is_required);
            }

            return Forms\Components\TextInput::make($column->key)
                ->label($column->label)
                ->numeric($column->type === 'number')
                ->url($column->type === 'url')
                ->password($column->type === 'password')
                ->revealable($column->type === 'password')
                ->required((bool) $column->is_required);
        });

        return $form->schema([
            Forms\Components\Section::make()
                ->schema([
                    Forms\Components\Hidden::make('page_id')
                        ->default($this->page->id),

                    ...$schema,
                ])
                ->columns(2),
        ]);
    }

    public function columnBuilder()
    {
        $columns = collect($this->page->columns)->map(function ($column) {
            $column = (object) $column;

            return $column;
        });

        $second = $columns->get(1);

        return [
            Tables\Columns\TextColumn::make('identifier')
                ->label($columns->first()->label)
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make("data.{$second->key}")
                ->label($second->label),
        ];
    }
}
