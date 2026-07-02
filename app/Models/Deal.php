<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Deal extends Model implements HasMedia
{
    use HasUuids, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'stage_id',
        'account_id',
        'owner_id',
        'title',
        'value',
        'expected_close_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'value'               => 'decimal:2',
            'expected_close_date' => 'date',
        ];
    }

    // ── Relationships ──────────────────────────────────────

    public function stage(): BelongsTo
    {
        return $this->belongsTo(PipelineStage::class, 'stage_id');
    }

    public function pipeline(): \Illuminate\Database\Eloquent\Relations\HasOneThrough
    {
        // Deal → PipelineStage → Pipeline  (read-only, for convenience)
        return $this->hasOneThrough(
            Pipeline::class,
            PipelineStage::class,
            'id',         // stages.id
            'id',         // pipelines.id
            'stage_id',   // deals.stage_id
            'pipeline_id' // stages.pipeline_id
        );
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'subject')->latest('occurred_at');
    }
}
