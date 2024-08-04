<?php

namespace App\Services;

use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;

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
                            ->label('Nama Berkas')
                            ->required(),

                        Forms\Components\TextInput::make('url')
                            ->label('Tautan')
                            ->url()
                            ->required(),
                    ])
                    ->required((bool) $column->is_required)
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
}
