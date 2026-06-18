<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Qopiku\FilamentSqids\Traits\HasSqids;

class ServiceRecord extends Model
{
    use HasFactory, HasSqids;

    protected $fillable = [
        'service_id',
        'identifier_1',
        'identifier_2',
        'identifier_3',
        'identifier_4',
        'identifier_5',
        'data',
        'attachment_disk',
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
