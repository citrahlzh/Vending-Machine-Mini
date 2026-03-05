<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Play extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'idempotency_key',
        'game_id',
        'status',
        'score',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'status' => 'string',
        'score' => 'integer',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function responses()
    {
        return $this->hasMany(PlayResponse::class);
    }

    public function issuedRewards()
    {
        return $this->hasMany(IssuedReward::class);
    }
}
