<?php

namespace App\Filament\Resources\Services\Pages;

use App\Filament\Resources\Services\ServiceResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;
use Illuminate\View\View;

class EditService extends EditRecord
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('share')
                ->label('Share & QR Code')
                ->color('info')
                ->slideOver()
                ->modalHeading(fn (): string => 'Share Service '.($this->record->name ?? ''))
                ->modalWidth(Width::Large)
                ->modalSubmitAction(false)
                ->modalCancelAction(false)
                ->modalContent(fn (): View => view('filament.resources.services.pages.share-modal', [
                    'service' => $this->record,
                    'url' => route('service.show', ['slug' => $this->record->slug]),
                ])),

            DeleteAction::make(),
        ];
    }
}
