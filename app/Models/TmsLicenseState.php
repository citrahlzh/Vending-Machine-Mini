<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TmsLicenseState extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'file_hash',
        'status',
        'verified_at',
        'license_expires_at'
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'license_expires_at' => 'date'
    ];
}
