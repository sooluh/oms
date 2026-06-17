<?php

namespace App\Filament\Resources\ServiceRecord;

use App\Filament\Resources\ServiceRecord\Pages\ListServiceRecords;
use App\Models\Service;
use App\Models\ServiceRecord;
use Filafly\Icons\Phosphor\Enums\Phosphor;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;

class ServiceRecordResource extends Resource
{
    protected static ?string $model = ServiceRecord::class;

    protected static ?string $slug = 'services-records';

    public static function getPages(): array
    {
        return [
            'index' => ListServiceRecords::route('/'),
        ];
    }

    public static function getNavigationItems(): array
    {
        $services = Service::active()->ordered()->get();

        $items = [];

        foreach ($services as $service) {
            $items[] = NavigationItem::make($service->name)
                ->icon(Phosphor::TableBold)
                ->group('Services')
                ->url(static::getUrl('index', ['service_id' => $service->id]))
                ->badge((string) $service->records()->count(), 'primary')
                ->sort(1)
                ->isActiveWhen(fn (): bool => request()->query('service_id') == $service->id);
        }

        return $items;
    }
}
