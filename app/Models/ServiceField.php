<?php

namespace App\Models;

use App\Enums\FieldType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Qopiku\FilamentSqids\Traits\HasSqids;

class ServiceField extends Model
{
    use HasFactory, HasSqids;

    protected $fillable = [
        'service_id',
        'name',
        'label',
        'type',
        'true_label',
        'false_label',
        'required',
        'visible',
        'is_identifier',
        'identifier_order',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'type' => FieldType::class,
            'required' => 'boolean',
            'visible' => 'boolean',
            'is_identifier' => 'boolean',
            'identifier_order' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (ServiceField $field) {
            if ($field->is_identifier) {
                $existingIdentifiers = ServiceField::query()
                    ->where('service_id', $field->service_id)
                    ->where('is_identifier', true);

                if ($field->getKey()) {
                    $existingIdentifiers->whereKeyNot($field);
                }

                if ($field->identifier_order === null) {
                    $usedOrders = $existingIdentifiers->pluck('identifier_order')->toArray();
                    $available = collect([1, 2, 3, 4, 5])->diff($usedOrders)->first();

                    if ($available === null) {
                        return false;
                    }

                    $field->identifier_order = $available;
                } else {
                    $duplicate = (clone $existingIdentifiers)
                        ->where('identifier_order', $field->identifier_order)
                        ->exists();

                    if ($duplicate) {
                        return false;
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
