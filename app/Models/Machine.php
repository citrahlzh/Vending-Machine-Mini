<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Machine extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'serial_number',
        'location',
        'operator_name',
        'category',
        'size',
        'photo_url',
        'is_android',
        'status',
        'condition_status',
    ];

    protected function casts(): array
    {
        return [
            'is_android' => 'boolean',
        ];
    }
}
