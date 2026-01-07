<?php

namespace App\Services;

use App\Models\Criterion;
use App\Models\CriteriaComparison;
use App\Models\AhpSession;

class AhpService
{
    // Tabel Random Index (RI) untuk n = 1 sampai 15
    private const RANDOM_INDEX = [
        1 => 0.00, 2 => 0.00, 3 => 0.58, 4 => 0.90, 5 => 1.12,
        6 => 1.24, 7 => 1.32, 8 => 1.41, 9 => 1.45, 10 => 1.49,
        11 => 1.51, 12 => 1.48, 13 => 1.56, 14 => 1.57, 15 => 1.59
    ];

    /**
     * Buat matriks perbandingan lengkap dari input user
     */
    public function buildComparisonMatrix(array $comparisons, array $criteriaIds): array
    {
        $n = count($criteriaIds);

        // Buat mapping dari criterion_id ke array index
        $idToIndex = [];
        foreach ($criteriaIds as $index => $id) {
            $idToIndex[$id] = $index;
        }

        // Inisialisasi matriks dengan 1 di diagonal
        $matrix = array_fill(0, $n, array_fill(0, $n, 1));

        // Isi dari data perbandingan
        foreach ($comparisons as $comparison) {
            $criterion1Id = $comparison['criterion_1_id'];
            $criterion2Id = $comparison['criterion_2_id'];

            // Skip jika ID tidak valid
            if (!isset($idToIndex[$criterion1Id]) || !isset($idToIndex[$criterion2Id])) {
                continue;
            }

            $i = $idToIndex[$criterion1Id];
            $j = $idToIndex[$criterion2Id];
            $value = $comparison['value'];

            $matrix[$i][$j] = $value;
            $matrix[$j][$i] = 1 / $value; // Kebalikan (reciprocal)
        }

        return $matrix;
    }

    /**
     * Hitung jumlah per kolom
     */
    public function calculateColumnSums(array $matrix): array
    {
        $n = count($matrix);
        $sums = array_fill(0, $n, 0);

        for ($j = 0; $j < $n; $j++) {
            for ($i = 0; $i < $n; $i++) {
                $sums[$j] += $matrix[$i][$j];
            }
        }

        return $sums;
    }

    /**
     * Normalisasi matriks (setiap elemen dibagi jumlah kolomnya)
     */
    public function normalizeMatrix(array $matrix, array $columnSums): array
    {
        $n = count($matrix);
        $normalized = [];

        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $normalized[$i][$j] = $matrix[$i][$j] / $columnSums[$j];
            }
        }

        return $normalized;
    }

    /**
     * Hitung prioritas (rata-rata per baris) = BOBOT
     */
    public function calculatePriorities(array $normalizedMatrix): array
    {
        $n = count($normalizedMatrix);
        $priorities = [];

        for ($i = 0; $i < $n; $i++) {
            $sum = array_sum($normalizedMatrix[$i]);
            $priorities[$i] = $sum / $n;
        }

        return $priorities;
    }

    /**
     * Hitung λ max (Lambda Max)
     */
    public function calculateLambdaMax(array $matrix, array $priorities): float
    {
        $n = count($matrix);
        $weightedSum = [];

        // Kalikan matriks dengan vektor prioritas
        for ($i = 0; $i < $n; $i++) {
            $sum = 0;
            for ($j = 0; $j < $n; $j++) {
                $sum += $matrix[$i][$j] * $priorities[$j];
            }
            $weightedSum[$i] = $sum;
        }

        // Bagi dengan prioritas masing-masing dan rata-rata
        $lambdaMax = 0;
        for ($i = 0; $i < $n; $i++) {
            $lambdaMax += $weightedSum[$i] / $priorities[$i];
        }

        return $lambdaMax / $n;
    }

    /**
     * Hitung Consistency Index (CI)
     */
    public function calculateCI(float $lambdaMax, int $n): float
    {
        if ($n <= 1) return 0;
        return ($lambdaMax - $n) / ($n - 1);
    }

    /**
     * Hitung Consistency Ratio (CR)
     */
    public function calculateCR(float $ci, int $n): float
    {
        $ri = self::RANDOM_INDEX[$n] ?? 0;
        if ($ri == 0) return 0;
        return $ci / $ri;
    }

    /**
     * Proses lengkap AHP dan simpan ke database
     */
    public function calculate(AhpSession $session, array $comparisonData, array $criteriaIds): array
    {
        $n = count($criteriaIds);

        // 1. Build matriks lengkap
        $matrix = $this->buildComparisonMatrix($comparisonData, $criteriaIds);

        // 2. Hitung jumlah kolom
        $columnSums = $this->calculateColumnSums($matrix);

        // 3. Normalisasi matriks
        $normalizedMatrix = $this->normalizeMatrix($matrix, $columnSums);

        // 4. Hitung prioritas/bobot
        $priorities = $this->calculatePriorities($normalizedMatrix);

        // 5. Hitung λ max
        $lambdaMax = $this->calculateLambdaMax($matrix, $priorities);

        // 6. Hitung CI
        $ci = $this->calculateCI($lambdaMax, $n);

        // 7. Hitung CR
        $ri = self::RANDOM_INDEX[$n] ?? 0;
        $cr = $this->calculateCR($ci, $n);

        // 8. Cek konsistensi (CR < 0.1)
        $isConsistent = $cr < 0.1;

        // 9. Update session
        $session->update([
            'lambda_max' => $lambdaMax,
            'consistency_index' => $ci,
            'random_index' => $ri,
            'consistency_ratio' => $cr,
            'is_consistent' => $isConsistent,
            'weights' => $priorities,
        ]);

        return [
            'matrix' => $matrix,
            'columnSums' => $columnSums,
            'normalizedMatrix' => $normalizedMatrix,
            'priorities' => $priorities,
            'lambdaMax' => $lambdaMax,
            'ci' => $ci,
            'ri' => $ri,
            'cr' => $cr,
            'isConsistent' => $isConsistent,
        ];
    }
}
