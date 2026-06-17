<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\RecentSearches;
use App\Filament\Widgets\SearchChart;
use App\Filament\Widgets\SearchStatsOverview;
use App\Models\Service;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    protected static string $routePath = '/dashboard';

    protected function getHeaderActions(): array
    {
        return [
            FilterAction::make()
                ->label('Filter')
                ->schema([
                    Select::make('service_id')
                        ->label('Services')
                        ->placeholder('All services')
                        ->native(false)
                        ->options(Service::pluck('name', 'id')),

                    DatePicker::make('start_date')
                        ->label('Start Date')
                        ->placeholder('Select a start date')
                        ->native(false),

                    DatePicker::make('end_date')
                        ->label('End Date')
                        ->placeholder('Select a end date')
                        ->native(false),
                ]),
        ];
    }

    public function getWidgets(): array
    {
        return [
            SearchStatsOverview::class,
            SearchChart::class,
            RecentSearches::class,
        ];
    }
}
