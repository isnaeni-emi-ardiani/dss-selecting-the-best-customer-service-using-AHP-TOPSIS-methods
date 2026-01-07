<?php

namespace App\Http\Controllers;

use App\Models\{Alternative, Criterion, Score};
use Illuminate\Http\Request;

class ScoreController extends Controller
{
    public function index()
    {
        $alternatives = Alternative::where('is_active', true)->get();
        $criteria = Criterion::all();

        // Build matrix scores
        $scores = [];
        foreach ($alternatives as $alt) {
            foreach ($criteria as $crit) {
                $score = Score::where('alternative_id', $alt->id)
                    ->where('criterion_id', $crit->id)
                    ->first();

                $scores[$alt->id][$crit->id] = $score ? $score->score : null;
            }
        }

        return view('scores.index', compact('alternatives', 'criteria', 'scores'));
    }

    public function edit()
    {
        $alternatives = Alternative::where('is_active', true)->get();
        $criteria = Criterion::all();

        // Build matrix scores - pre-fill dengan data existing
        $scores = [];
        $hasEmptyScore = false;

        foreach ($alternatives as $alt) {
            foreach ($criteria as $crit) {
                $score = Score::where('alternative_id', $alt->id)
                    ->where('criterion_id', $crit->id)
                    ->first();

                $scores[$alt->id][$crit->id] = $score ? $score->score : null;

                // Check jika ada yang belum diisi
                if (!$score || $score->score === null) {
                    $hasEmptyScore = true;
                }
            }
        }

        return view('scores.edit', compact('alternatives', 'criteria', 'scores', 'hasEmptyScore'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'scores' => 'required|array',
            'scores.*.*' => 'nullable|integer|min:1|max:5',
        ]);

        $updatedCount = 0;

        foreach ($request->scores as $altId => $criteriaScores) {
            foreach ($criteriaScores as $critId => $scoreValue) {
                // Skip jika nilai kosong/null
                if ($scoreValue === null || $scoreValue === '') {
                    continue;
                }

                Score::updateOrCreate(
                    [
                        'alternative_id' => $altId,
                        'criterion_id' => $critId,
                    ],
                    ['score' => $scoreValue]
                );

                $updatedCount++;
            }
        }

        return redirect()->route('scores.index')
            ->with('success', "Berhasil menyimpan {$updatedCount} penilaian CS.");
    }
}
