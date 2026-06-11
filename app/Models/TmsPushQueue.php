<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TmsPushQueue extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'payload',
        'retry_count',
        'last_tried_at',
        'pushed_at'
    ];

    protected $casts = [
        'payload' => 'array',
        'last_tried_at' => 'datetime',
        'pushed_at' => 'datetime'
    ];
}
