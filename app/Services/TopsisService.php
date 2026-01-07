<?php

namespace App\Services;

use App\Models\Decision;
use App\Models\DecisionDetail;
use App\Models\Alternative;
use App\Models\Criterion;
use App\Models\Score;

class TopsisService
{
    /**
     * Build matriks keputusan dari scores
     * Optimized: Fetch all scores in single query
     */
    public function buildDecisionMatrix(array $alternativeIds, array $criteriaIds): array
    {
        // Fetch all scores in one query - OPTIMIZATION
        $scores = Score::whereIn('alternative_id', $alternativeIds)
            ->whereIn('criterion_id', $criteriaIds)
            ->get()
            ->keyBy(function($score) {
                return $score->alternative_id . '_' . $score->criterion_id;
            });

        $matrix = [];
        foreach ($alternativeIds as $altId) {
            $row = [];
            foreach ($criteriaIds as $critId) {
                $key = $altId . '_' . $critId;
                $row[] = isset($scores[$key]) ? $scores[$key]->score : 0;
            }
            $matrix[] = $row;
        }

        return $matrix;
    }

    /**
     * Normalisasi matriks menggunakan metode euclidean
     * R_ij = X_ij / sqrt(sum(X_ij^2))
     */
    public function normalizeMatrix(array $matrix): array
    {
        $m = count($matrix); // jumlah alternatif
        $n = count($matrix[0]); // jumlah kriteria
        $normalized = [];

        // Hitung akar jumlah kuadrat per kolom
        for ($j = 0; $j < $n; $j++) {
            $sumSquares = 0;
            for ($i = 0; $i < $m; $i++) {
                $sumSquares += pow($matrix[$i][$j], 2);
            }
            $sqrtSum = sqrt($sumSquares);

            // Normalisasi
            for ($i = 0; $i < $m; $i++) {
                $normalized[$i][$j] = $sqrtSum > 0 ? $matrix[$i][$j] / $sqrtSum : 0;
            }
        }

        return $normalized;
    }

    /**
     * Hitung matriks terbobot (normalized matrix × weights)
     */
    public function calculateWeightedMatrix(array $normalizedMatrix, array $weights): array
    {
        $m = count($normalizedMatrix);
        $n = count($normalizedMatrix[0]);
        $weighted = [];

        for ($i = 0; $i < $m; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $weighted[$i][$j] = $normalizedMatrix[$i][$j] * $weights[$j];
            }
        }

        return $weighted;
    }

    /**
     * Tentukan solusi ideal positif (A+) dan negatif (A-)
     */
    public function calculateIdealSolutions(array $weightedMatrix, array $criteria): array
    {
        $n = count($weightedMatrix[0]);
        $aPlus = []; // Solusi ideal positif
        $aMinus = []; // Solusi ideal negatif

        for ($j = 0; $j < $n; $j++) {
            $column = array_column($weightedMatrix, $j);
            $criterionType = $criteria[$j]->type;

            if ($criterionType === 'benefit') {
                $aPlus[$j] = max($column);
                $aMinus[$j] = min($column);
            } else { // cost
                $aPlus[$j] = min($column);
                $aMinus[$j] = max($column);
            }
        }

        return ['aPlus' => $aPlus, 'aMinus' => $aMinus];
    }

    /**
     * Hitung jarak euclidean ke A+ dan A-
     */
    public function calculateDistances(array $weightedMatrix, array $aPlus, array $aMinus): array
    {
        $m = count($weightedMatrix);
        $n = count($weightedMatrix[0]);
        $distances = [];

        for ($i = 0; $i < $m; $i++) {
            $dPlus = 0;
            $dMinus = 0;

            for ($j = 0; $j < $n; $j++) {
                $dPlus += pow($weightedMatrix[$i][$j] - $aPlus[$j], 2);
                $dMinus += pow($weightedMatrix[$i][$j] - $aMinus[$j], 2);
            }

            $distances[$i] = [
                'dPlus' => sqrt($dPlus),
                'dMinus' => sqrt($dMinus),
            ];
        }

        return $distances;
    }

    /**
     * Hitung preference value (C*) dan ranking
     */
    public function calculatePreferenceValues(array $distances): array
    {
        $preferences = [];

        foreach ($distances as $i => $distance) {
            $dPlus = $distance['dPlus'];
            $dMinus = $distance['dMinus'];
            $sum = $dPlus + $dMinus;

            $preferences[$i] = $sum > 0 ? $dMinus / $sum : 0;
        }

        return $preferences;
    }

    /**
     * Proses lengkap TOPSIS
     * Optimized: Bulk insert, single query for criteria
     */
    public function calculate(Decision $decision, array $alternativeIds, array $criteriaIds, array $weights): array
    {
        // 1. Build matriks keputusan
        $matrix = $this->buildDecisionMatrix($alternativeIds, $criteriaIds);

        // 2. Normalisasi
        $normalizedMatrix = $this->normalizeMatrix($matrix);

        // 3. Matriks terbobot
        $weightedMatrix = $this->calculateWeightedMatrix($normalizedMatrix, $weights);

        // 4. Solusi ideal - OPTIMIZED: Single query with indexed result
        $criteria = Criterion::whereIn('id', $criteriaIds)->get()->keyBy('id');
        $orderedCriteria = collect($criteriaIds)->map(fn($id) => $criteria[$id])->all();
        $idealSolutions = $this->calculateIdealSolutions($weightedMatrix, $orderedCriteria);

        // 5. Jarak ke A+ dan A-
        $distances = $this->calculateDistances($weightedMatrix, $idealSolutions['aPlus'], $idealSolutions['aMinus']);

        // 6. Preference values
        $preferences = $this->calculatePreferenceValues($distances);

        // 7. Ranking (sort descending by preference value)
        arsort($preferences);
        $rank = 1;
        $results = [];

        foreach ($preferences as $i => $prefValue) {
            $results[] = [
                'alternative_id' => $alternativeIds[$i],
                'd_plus' => $distances[$i]['dPlus'],
                'd_minus' => $distances[$i]['dMinus'],
                'preference_value' => $prefValue,
                'rank' => $rank++,
            ];
        }

        // 8. Simpan ke decision_details - OPTIMIZED: Bulk insert
        $detailsData = [];
        $timestamp = now();

        foreach ($results as $result) {
            $detailsData[] = [
                'decision_id' => $decision->id,
                'alternative_id' => $result['alternative_id'],
                'd_plus' => $result['d_plus'],
                'd_minus' => $result['d_minus'],
                'preference_value' => $result['preference_value'],
                'rank' => $result['rank'],
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        // Bulk insert - single query instead of N queries
        DecisionDetail::insert($detailsData);

        return [
            'matrix' => $matrix,
            'normalizedMatrix' => $normalizedMatrix,
            'weightedMatrix' => $weightedMatrix,
            'idealSolutions' => $idealSolutions,
            'distances' => $distances,
            'preferences' => $preferences,
            'results' => $results,
        ];
    }
}
