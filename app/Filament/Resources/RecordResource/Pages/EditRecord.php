<?php

namespace App\Filament\Resources\RecordResource\Pages;

use App\Filament\Resources\RecordResource;
use App\Services\RecordService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord as BaseEditRecord;

class EditRecord extends BaseEditRecord
{
    protected static string $resource = RecordResource::class;

    private ?string $page = null;

    public function mount(int|string $record): void
    {
        $this->page = request()->query('slug');
        $page = RecordService::getInstance($this->page)->getPage();

        abort_unless($page, 404);

        parent::mount($record);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
