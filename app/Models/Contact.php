<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Contact extends Model implements HasMedia
{
    use HasUuids, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'account_id',
        'name',
        'email',
        'phone',
        'job_title',
    ];

    // ── Relationships ──────────────────────────────────────

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'subject')->latest('occurred_at');
    }
}
