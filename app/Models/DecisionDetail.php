<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DecisionDetail extends Model
{
    protected $fillable = [
        'decision_id',
        'alternative_id',
        'd_plus',
        'd_minus',
        'preference_value',
        'rank',
    ];

    protected $casts = [
        'd_plus' => 'decimal:8',
        'd_minus' => 'decimal:8',
        'preference_value' => 'decimal:8',
        'rank' => 'integer',
    ];

    public function decision(): BelongsTo
    {
        return $this->belongsTo(Decision::class);
    }

    public function alternative(): BelongsTo
    {
        // withTrashed() untuk tetap bisa load alternative yang sudah di-soft delete
        // Ini penting untuk menjaga history decision yang lama
        return $this->belongsTo(Alternative::class)->withTrashed();
    }
}
