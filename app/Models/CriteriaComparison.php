<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CriteriaComparison extends Model
{
    protected $fillable = [
        'ahp_session_id',
        'criterion_1_id',
        'criterion_2_id',
        'value',
    ];

    protected $casts = [
        'value' => 'decimal:4',
    ];

    public function ahpSession(): BelongsTo
    {
        return $this->belongsTo(AhpSession::class);
    }

    public function criterion1(): BelongsTo
    {
        // withTrashed() untuk tetap bisa load criterion yang sudah di-soft delete
        // Penting untuk history AHP session
        return $this->belongsTo(Criterion::class, 'criterion_1_id')->withTrashed();
    }

    public function criterion2(): BelongsTo
    {
        // withTrashed() untuk tetap bisa load criterion yang sudah di-soft delete
        // Penting untuk history AHP session
        return $this->belongsTo(Criterion::class, 'criterion_2_id')->withTrashed();
    }
}
