<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AhpSession extends Model
{
    protected $fillable = [
        'name',
        'user_id',
        'matrix_size',
        'lambda_max',
        'consistency_index',
        'random_index',
        'consistency_ratio',
        'is_consistent',
        'is_active',
        'weights',
        'criteria_ids',
    ];

    protected $casts = [
        'matrix_size' => 'integer',
        'lambda_max' => 'decimal:6',
        'consistency_index' => 'decimal:6',
        'random_index' => 'decimal:6',
        'consistency_ratio' => 'decimal:6',
        'is_consistent' => 'boolean',
        'is_active' => 'boolean',
        'weights' => 'array',
        'criteria_ids' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comparisons(): HasMany
    {
        return $this->hasMany(CriteriaComparison::class);
    }

    /**
     * Scope untuk mendapatkan active session
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get active session (static method)
     */
    public static function getActiveSession()
    {
        return static::where('is_active', true)->first();
    }

    /**
     * Activate session ini dan deactivate yang lain
     */
    public function activate()
    {
        // Deactivate semua session lain
        static::where('is_active', true)->update(['is_active' => false]);

        // Activate session ini
        $this->update(['is_active' => true]);
    }

    /**
     * Check apakah session ini bisa diaktifkan
     * (jumlah criteria harus sama dengan jumlah criteria aktif saat ini)
     */
    public function canBeActivated()
    {
        $currentCriteriaCount = \App\Models\Criterion::count();
        $sessionCriteriaCount = count($this->criteria_ids ?? []);

        return $sessionCriteriaCount === $currentCriteriaCount;
    }

    /**
     * Get bobot untuk criterion tertentu dari session ini
     * Matching berdasarkan POSISI/URUTAN kriteria saat ini
     *
     * Contoh: Session punya 5 kriteria (C1,C2,C3,C4,C5)
     *         Kriteria aktif sekarang: C1,C2,C3,C4,C6
     *         C6 (posisi ke-5) akan dapat bobot dari posisi ke-5 session (yaitu C5)
     */
    public function getWeightForCriterion($criterionId)
    {
        if (!$this->criteria_ids || !$this->weights) {
            return 0;
        }

        // Ambil semua kriteria aktif saat ini, diurutkan berdasarkan ID
        $currentCriteria = \App\Models\Criterion::orderBy('id')->pluck('id');

        // Cari posisi criterion yang dicari di antara kriteria aktif
        $position = $currentCriteria->search($criterionId);

        // Jika tidak ditemukan atau posisi lebih besar dari jumlah weights
        if ($position === false || $position >= count($this->weights)) {
            return 0;
        }

        // Return bobot sesuai posisi
        return $this->weights[$position] ?? 0;
    }
}
