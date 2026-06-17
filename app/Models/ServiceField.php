<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

class ServiceField extends Model
{
    protected $fillable = [
        'service_id',
        'name',
        'label',
        'type',
        'required',
        'visible',
        'is_identifier',
        'identifier_order',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'required' => 'boolean',
            'visible' => 'boolean',
            'is_identifier' => 'boolean',
            'identifier_order' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (ServiceField $field): void {
            if ($field->is_identifier) {
                $existingIdentifiers = ServiceField::query()
                    ->where('service_id', $field->service_id)
                    ->where('is_identifier', true);

                if ($field->getKey()) {
                    $existingIdentifiers->whereKeyNot($field);
                }

                if ($field->identifier_order === null) {
                    $usedOrders = $existingIdentifiers->pluck('identifier_order')->toArray();
                    $available = collect([1, 2, 3])->diff($usedOrders)->first();

                    if ($available === null) {
                        throw new InvalidArgumentException('Maximum 3 identifiers allowed per service.');
                    }

                    $field->identifier_order = $available;
                } else {
                    $duplicate = (clone $existingIdentifiers)
                        ->where('identifier_order', $field->identifier_order)
                        ->exists();

                    if ($duplicate) {
                        throw new InvalidArgumentException("Identifier order {$field->identifier_order} is already in use.");
                    }
                }
            } else {
                $field->identifier_order = null;
            }
        });
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order');
    }
}
