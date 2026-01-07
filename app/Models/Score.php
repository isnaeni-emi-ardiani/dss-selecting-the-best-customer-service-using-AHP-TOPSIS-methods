<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Score extends Model
{
    protected $fillable = [
        'alternative_id',
        'criterion_id',
        'score',
    ];

    protected $casts = [
        'score' => 'integer',
    ];

    public function alternative(): BelongsTo
    {
        // withTrashed() untuk tetap bisa load alternative yang sudah di-soft delete
        // Penting untuk history scores
        return $this->belongsTo(Alternative::class)->withTrashed();
    }

    public function criterion(): BelongsTo
    {
        // withTrashed() untuk tetap bisa load criterion yang sudah di-soft delete
        // Penting untuk history scores dan decisions
        return $this->belongsTo(Criterion::class)->withTrashed();
    }
}
