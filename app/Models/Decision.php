<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Decision extends Model
{
    protected $fillable = [
        'title',
        'description',
        'decision_date',
        'user_id',
        'criteria_ids',
    ];

    protected $casts = [
        'decision_date' => 'date',
        'criteria_ids' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(DecisionDetail::class);
    }
}
