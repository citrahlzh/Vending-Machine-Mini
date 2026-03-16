<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reward extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'product_display_id',
        'stock',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'quantity' => 'integer',
        'is_active' => 'boolean',
    ];

    public function issuedRewards()
    {
        return $this->hasMany(IssuedReward::class);
    }

    public function productDisplay()
    {
        return $this->belongsTo(ProductDisplay::class);
    }
}
