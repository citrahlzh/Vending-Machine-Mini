<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class GameQuest extends Pivot
{
    protected $table = 'game_quests';

    protected $fillable = [
        'game_id',
        'quest_id',
        'order'
    ];
}
