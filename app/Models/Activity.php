<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Activity extends Model
{
    use HasUuids;

    protected $fillable = [
        'subject_type',
        'subject_id',
        'user_id',
        'type',
        'subject_line',
        'body',
        'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
        ];
    }

    // ── Relationships ──────────────────────────────────────

    /**
     * The record this activity was logged against
     * (Deal, Contact, Account, or Project).
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
