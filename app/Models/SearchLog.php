<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SearchLog extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'service_id',
        'input_value',
        'is_success',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'is_success' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
