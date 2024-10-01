<?php

namespace App\Filament\Resources\RecordResource\Pages;

use App\Filament\Resources\RecordResource;
use App\Services\RecordService;
use Filament\Resources\Pages\CreateRecord as BaseCreateRecord;
use Illuminate\Database\Eloquent\Model;

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

    protected function handleRecordCreation(array $data): Model
    {
        // TODO: aku tau ini tidak normal

        $json = request()->json()->all();
        $snapshot = json_decode($json['components'][0]['snapshot'], true);
        $updates = $json['components'][0]['updates'];
        $data = [];
        $groupped = [];

        foreach ($updates as $key => $value) {
            if (strpos($key, 'data.') === 0) {
                $fix = str_replace('data.', '', $key);

                if (preg_match('/^(?<prefix>[^.]+)\.(?<uuid>[a-f0-9\-]+)\.(?<suffix>[^.]+)$/', $fix, $matches)) {
                    $prefix = $matches['prefix'];
                    $suffix = $matches['suffix'];
                    $uuid = $matches['uuid'];

                    if (! isset($groupped[$prefix])) {
                        $groupped[$prefix] = [];
                    }

                    if (! isset($groupped[$prefix][$uuid])) {
                        $groupped[$prefix][$uuid] = [];
                    }

                    $groupped[$prefix][$uuid][$suffix] = $value;
                } else {
                    $data[$fix] = $value;
                }
            }
        }

        foreach ($groupped as $prefix => $items) {
            $data[$prefix] = array_values($items);
        }

        $result = [
            'identifier' => $updates['data.nim'],
            'page_id' => $snapshot['data']['data'][0]['page_id'],
            'data' => json_encode($data),
        ];

        return $this->getModel()::create($result);
    }
}
