<?php

namespace App\Filament\Resources\Services\Tables;

use App\Enums\SearchMode;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ServiceTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable(),

                TextColumn::make('search_mode')
                    ->label('Search Mode')
                    ->badge(),

                TextColumn::make('start_date')
                    ->label('Start Date')
                    ->date()
                    ->placeholder('—'),

                TextColumn::make('end_date')
                    ->label('End Date')
                    ->date()
                    ->placeholder('—'),

                ToggleColumn::make('is_active')
                    ->label('Active'),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->native(false),

                SelectFilter::make('search_mode')
                    ->label('Search Mode')
                    ->native(false)
                    ->options(SearchMode::class),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
