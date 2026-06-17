<?php

namespace App\Filament\Widgets;

use App\Models\SearchLog;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SearchStatsOverview extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected function getStats(): array
    {
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

        $total = $query->count();
        $success = (clone $query)->where('is_success', true)->count();
        $failed = (clone $query)->where('is_success', false)->count();
        $uniqueVisitors = (clone $query)->distinct('ip_address')->count('ip_address');

        return [
            Stat::make('Total Searches', number_format($total))
                ->description('All search activities')
                ->color('info'),

            Stat::make('Successful Searches', number_format($success))
                ->description($total > 0 ? round(($success / $total) * 100, 1).'% ot the total' : '0%')
                ->color('success'),

            Stat::make('Failed Searches', number_format($failed))
                ->description($total > 0 ? round(($failed / $total) * 100, 1).'% of the total' : '0%')
                ->color('danger'),

            Stat::make('Unique Visitor', number_format($uniqueVisitors))
                ->description('Visitor that accessed the site')
                ->color('warning'),
        ];
    }
}
