<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Criterion extends Model
{
    use SoftDeletes;

    protected $table = 'criteria';

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
    ];

    /**
     * Boot method untuk auto-generate code
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($criterion) {
            if (empty($criterion->code)) {
                $criterion->code = static::generateCode();
            }
        });
    }

    /**
     * Generate code otomatis (C1, C2, C3, dst)
     */
    public static function generateCode()
    {
        // Ambil code terakhir (termasuk yang soft deleted)
        $lastCriterion = static::withTrashed()
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastCriterion) {
            return 'C1';
        }

        // Extract nomor dari code (C1 -> 1, C2 -> 2, dst)
        $lastNumber = (int) preg_replace('/[^0-9]/', '', $lastCriterion->code);
        $newNumber = $lastNumber + 1;

        return 'C' . $newNumber;
    }

    public function scores(): HasMany
    {
        return $this->hasMany(Score::class, 'criterion_id');
    }

    /**
     * Get bobot dari active AHP session
     */
    public function getWeightAttribute()
    {
        $activeSession = \App\Models\AhpSession::getActiveSession();

        if (!$activeSession) {
            return null;
        }

        return $activeSession->getWeightForCriterion($this->id);
    }

    /**
     * Get bobot formatted as percentage
     */
    public function getWeightPercentageAttribute()
    {
        $weight = $this->weight;
        return $weight ? number_format($weight * 100, 2) . '%' : '-';
    }

    /**
     * Check apakah criterion ini ada di active session
     * Berdasarkan posisi - jika jumlah kriteria sama dengan session, berarti semua kriteria "in session"
     */
    public function isInActiveSession()
    {
        $activeSession = \App\Models\AhpSession::getActiveSession();

        if (!$activeSession || !$activeSession->criteria_ids) {
            return false;
        }

        // Jika jumlah kriteria aktif sama dengan jumlah kriteria di session,
        // berarti semua kriteria aktif adalah "in session" berdasarkan posisi
        $currentCount = \App\Models\Criterion::count();
        $sessionCount = count($activeSession->criteria_ids);

        return $currentCount === $sessionCount;
    }
}
