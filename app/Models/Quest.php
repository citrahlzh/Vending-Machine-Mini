<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'game_id',
        'type',
        'game_type',
        'prompt',
        'option',
        'answer',
        'image_url',
        'is_active',
    ];

    protected $casts = [
        'prompt' => 'array',
        'option' => 'array',
        'answer' => 'array',
        'is_active' => 'boolean',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function playResponses()
    {
        return $this->hasMany(PlayResponse::class);
    }
}
