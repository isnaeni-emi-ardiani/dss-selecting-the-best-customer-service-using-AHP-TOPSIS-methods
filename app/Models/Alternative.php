<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Alternative extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Boot method untuk auto-generate code
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($alternative) {
            if (empty($alternative->code)) {
                // Ambil semua kode yang pernah ada (termasuk yang sudah dihapus)
                $existingCodes = static::withTrashed()
                    ->where('code', 'like', 'A%')
                    ->pluck('code')
                    ->map(function ($code) {
                        return intval(substr($code, 1));
                    })
                    ->toArray();

                // Cari nomor terkecil yang belum digunakan, mulai dari 1
                $nextNumber = 1;
                while (in_array($nextNumber, $existingCodes)) {
                    $nextNumber++;
                }

                $alternative->code = 'A' . $nextNumber;
            }
        });
    }

    public function scores(): HasMany
    {
        return $this->hasMany(Score::class);
    }

    public function decisionDetails(): HasMany
    {
        return $this->hasMany(DecisionDetail::class);
    }
}
