<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use InvalidArgumentException;

class Service extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
        'is_active',
        'start_date',
        'end_date',
        'search_mode',
        'max_attempt',
        'attempt_duration',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'start_date' => 'date',
            'end_date' => 'date',
            'sort_order' => 'integer',
            'max_attempt' => 'integer',
            'attempt_duration' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where(function (Builder $query) {
                $query->whereNull('start_date')->orWhere('start_date', '<=', today());
            })
            ->where(function (Builder $query) {
                $query->whereNull('end_date')->orWhere('end_date', '>=', today());
            });
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function fields(): HasMany
    {
        return $this->hasMany(ServiceField::class)->ordered();
    }

    public function identifierFields(): HasMany
    {
        return $this
            ->hasMany(ServiceField::class)
            ->where('is_identifier', true)
            ->orderBy('identifier_order');
    }

    public function records(): HasMany
    {
        return $this->hasMany(ServiceRecord::class);
    }

    public function searchLogs(): HasMany
    {
        return $this->hasMany(SearchLog::class);
    }

    public function validateIdentifiers(array $fields): void
    {
        $identifierCount = collect($fields)
            ->where('is_identifier', true)
            ->count();

        if ($identifierCount > 3) {
            throw new InvalidArgumentException('Maximum 3 identifiers allowed per service.');
        }

        $usedOrders = [];

        foreach ($fields as &$field) {
            if (! empty($field['is_identifier'])) {
                if (empty($field['identifier_order'])) {
                    $available = array_diff([1, 2, 3], $usedOrders);
                    $field['identifier_order'] = ! empty($available) ? min($available) : null;
                }
                $usedOrders[] = $field['identifier_order'];
            } else {
                $field['identifier_order'] = null;
            }
        }
    }
}
