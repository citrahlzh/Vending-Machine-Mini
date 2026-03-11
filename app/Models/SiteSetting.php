<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteSetting extends Model
{
    protected $fillable = [
        'key',
        'label',
        'value',
        'type',
        'group',
    ];
}
