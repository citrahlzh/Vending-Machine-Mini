<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IssuedReward extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'play_id',
        'reward_id',
        'code',
        'status',
        'issued_at',
        'expires_at',
        'redeemed_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'expires_at' => 'datetime',
        'redeemed_at' => 'datetime',
    ];

    public function play()
    {
        return $this->belongsTo(Play::class);
    }

    public function reward()
    {
        return $this->belongsTo(Reward::class);
    }
}
