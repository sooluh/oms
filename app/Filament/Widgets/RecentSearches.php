<?php

namespace App\Filament\Widgets;

use App\Models\SearchLog;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentSearches extends TableWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Recent Search Histories';

    public function table(Table $table): Table
    {
        return $table
            ->paginated([6])
            ->defaultPaginationPageOption(6)
            ->query(function (): Builder {
                $serviceId = $this->filters['service_id'] ?? null;
                $startDate = $this->filters['start_date'] ?? null;
                $endDate = $this->filters['end_date'] ?? null;

                $query = SearchLog::query();

                if ($serviceId) {
                    $query->where('service_id', $serviceId);
                }

                if ($startDate) {
                    $query->where('created_at', '>=', $startDate);
                }

                if ($endDate) {
                    $query->where('created_at', '<=', $endDate);
                }

                return $query->latest('created_at');
            })
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date Time')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('service.name')
                    ->label('Service')
                    ->searchable(),

                TextColumn::make('is_success')
                    ->label('Status')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Sukses' : 'Gagal'),

                TextColumn::make('ip_address')
                    ->label('IP Address'),
            ]);
    }
}
