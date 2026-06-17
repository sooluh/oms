<?php

namespace App\Filament\Resources\Services\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Guava\IconPicker\Tables\Columns\IconColumn as GuavaIconColumn;

class ServiceTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                GuavaIconColumn::make('icon')
                    ->label('Icon')
                    ->size('lg'),

                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable(),

                ToggleColumn::make('is_active')
                    ->label('Active'),

                TextColumn::make('search_mode')
                    ->label('Mode')
                    ->badge(),

                TextColumn::make('start_date')
                    ->label('Starts')
                    ->date()
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label('Ends')
                    ->date()
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label('Order')
                    ->numeric()
                    ->sortable(),

            ])
            ->defaultSort('sort_order')
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Active Status'),

                SelectFilter::make('search_mode')
                    ->label('Search Mode')
                    ->options([
                        'OR' => 'OR Mode',
                        'AND' => 'AND Mode',
                    ]),
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
