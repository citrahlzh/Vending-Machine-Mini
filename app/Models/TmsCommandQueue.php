<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TmsCommandQueue extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tms_command_id',
        'type',
        'payload',
        'status',
        'received_at',
        'executed_at',
        'error_message'
    ];

    protected $casts = [
        'payload' => 'array',
        'received_at' => 'datetime',
        'executed_at' => 'datetime'
    ];
}
