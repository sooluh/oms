<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'logo',
        'columns',
        'is_active',
        'enhancer_id',
    ];

    protected $casts = [
        'columns' => 'array',
        'is_active' => 'boolean',
    ];
}
