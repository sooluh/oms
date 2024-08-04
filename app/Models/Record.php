<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Record extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'page_id',
        'identifier',
        'data',
        'enhancer_id',
    ];

    protected $casts = [
        'data' => 'array',
    ];
}
