<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlayResponse extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'play_id',
        'quest_id',
        'user_answer',
        'is_correct',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function play()
    {
        return $this->belongsTo(Play::class);
    }

    public function quest()
    {
        return $this->belongsTo(Quest::class);
    }
}
