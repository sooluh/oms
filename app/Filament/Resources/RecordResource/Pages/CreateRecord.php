<?php

namespace App\Filament\Resources\RecordResource\Pages;

use App\Filament\Resources\RecordResource;
use App\Services\RecordService;
use Filament\Resources\Pages\CreateRecord as BaseCreateRecord;

class CreateRecord extends BaseCreateRecord
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
}
