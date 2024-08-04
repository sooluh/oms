<?php

namespace App\Filament\Resources\RecordResource\Pages;

use App\Filament\Resources\RecordResource;
use App\Services\RecordService;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords as BaseListRecords;

class ListRecords extends BaseListRecords
{
    protected static string $resource = RecordResource::class;

    private ?string $page = null;

    public function mount(): void
    {
        $this->page = request()->query('slug');
        $page = RecordService::getInstance($this->page)->getPage();

        abort_unless($page, 404);

        parent::mount();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->url(fn () => CreateRecord::getUrl(['slug' => $this->page])),
        ];
    }
}
