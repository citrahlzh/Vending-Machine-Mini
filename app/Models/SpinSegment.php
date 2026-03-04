<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpinSegment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'game_id',
        'reward_id',
        'label',
        'image_url',
        'weight',
        'is_active',
    ];

    protected $casts = [
        'weight' => 'integer',
        'is_active' => 'boolean',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function reward()
    {
        return $this->belongsTo(Reward::class);
    }
}
