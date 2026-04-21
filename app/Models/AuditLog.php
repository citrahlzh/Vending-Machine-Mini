<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel',
        'event',
        'action',
        'description',
        'actor_type',
        'actor_id',
        'actor_name',
        'subject_type',
        'subject_id',
        'subject_label',
        'route_name',
        'method',
        'url',
        'ip_address',
        'user_agent',
        'status_code',
        'tags',
        'properties',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'properties' => 'array',
            'status_code' => 'integer',
        ];
    }

    public function actor(): MorphTo
    {
        return $this->morphTo();
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
