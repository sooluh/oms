<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRecord extends Model
{
    protected $fillable = [
        'service_id',
        'identifier_1',
        'identifier_2',
        'identifier_3',
        'data',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
