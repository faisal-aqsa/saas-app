<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Account extends Model implements HasMedia
{
    use HasUuids, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'name',
        'industry',
        'website',
        'phone',
        'address',
    ];

    // ── Relationships ──────────────────────────────────────

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'subject')->latest('occurred_at');
    }
}
