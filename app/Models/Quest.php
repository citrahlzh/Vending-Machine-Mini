<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'game_type',
        'prompt',
        'option',
        'answer',
        'image_url',
        'is_active',
    ];

    protected $casts = [
        'option' => 'array',
        'answer' => 'array',
        'is_active' => 'boolean',
    ];

    public function game()
    {
        return $this->belongsToMany(Game::class, 'game_quests')
            ->using(GameQuest::class)
            ->withPivot('order')
            ->withTimestamps();
    }

    public function playResponses()
    {
        return $this->hasMany(PlayResponse::class);
    }
}
