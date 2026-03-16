<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Game extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'config_json',
        'is_active',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'config_json' => 'array',
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function plays()
    {
        return $this->hasMany(Play::class);
    }

    public function quests()
    {
        return $this->belongsToMany(Quest::class, 'game_quests')
            ->using(GameQuest::class)
            ->withPivot('order')
            ->withTimestamps();
    }

    public function spinSegments()
    {
        return $this->hasMany(SpinSegment::class);
    }
}
