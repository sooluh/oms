<?php

namespace App\Filament\Widgets;

use App\Models\SearchLog;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;

class SearchChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Daily Search Activities';

    protected function getData(): array
    {
        $serviceId = $this->filters['service_id'] ?? null;
        $startDate = $this->filters['start_date'] ?? null;
        $endDate = $this->filters['end_date'] ?? null;

        $start = $startDate ? Carbon::parse($startDate) : now()->subDays(14);
        $end = $endDate ? Carbon::parse($endDate) : now();

        $query = SearchLog::query()
            ->select(
                DB::raw('date(created_at) as date'),
                DB::raw('sum(case when is_success then 1 else 0 end) as success_count'),
                DB::raw('sum(case when is_success then 0 else 1 end) as failed_count')
            )
            ->where('created_at', '>=', $start->startOfDay())
            ->where('created_at', '<=', $end->endOfDay());

        if ($serviceId) {
            $query->where('service_id', $serviceId);
        }

        $records = $query
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $labels = [];
        $successData = [];
        $failedData = [];

        $period = new DatePeriod(
            $start,
            new DateInterval('P1D'),
            (clone $end)->addDay()
        );

        $recordMap = $records->keyBy('date');

        foreach ($period as $date) {
            $formattedDate = $date->format('Y-m-d');
            $labels[] = $date->format('d M');

            $record = $recordMap->get($formattedDate);
            $successData[] = $record ? (int) $record->success_count : 0;
            $failedData[] = $record ? (int) $record->failed_count : 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Success',
                    'data' => $successData,
                    'borderColor' => '#22c55e',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'fill' => 'start',
                ],
                [
                    'label' => 'Failed',
                    'data' => $failedData,
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill' => 'start',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
