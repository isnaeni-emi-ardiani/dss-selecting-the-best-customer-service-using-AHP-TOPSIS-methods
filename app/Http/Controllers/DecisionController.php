<?php

namespace App\Http\Controllers;

use App\Models\{Alternative, Criterion, Decision, AhpSession, Score};
use App\Services\TopsisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DecisionController extends Controller
{
    protected $topsisService;

    public function __construct(TopsisService $topsisService)
    {
        $this->topsisService = $topsisService;
    }

    public function index()
    {
        $decisions = Decision::where('user_id', Auth::id())
            ->latest()
            ->with('details.alternative')
            ->get();

        return view('decisions.index', compact('decisions'));
    }

    public function create()
    {
        $criteria = Criterion::all();
        $alternatives = Alternative::where('is_active', true)->get();
        $activeSession = AhpSession::getActiveSession();

        // Validasi: pastikan ada active AHP session dengan bobot
        $hasWeights = $activeSession && $activeSession->weights && count($activeSession->weights) > 0;

        // Count data
        $criteriaCount = $criteria->count();
        $alternativesCount = $alternatives->count();
        $scoresCount = \App\Models\Score::count();

        // Check if can calculate
        $canCalculate = $criteriaCount > 0 &&
                       $alternativesCount >= 2 &&
                       $hasWeights &&
                       $scoresCount > 0;

        return view('decisions.create', compact(
            'criteria',
            'alternatives',
            'canCalculate',
            'criteriaCount',
            'alternativesCount',
            'scoresCount',
            'hasWeights',
            'activeSession'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Ambil active AHP session
        $activeSession = AhpSession::getActiveSession();

        if (!$activeSession) {
            return redirect()->back()
                ->with('error', 'Tidak ada AHP session yang aktif. Silakan aktifkan salah satu session AHP terlebih dahulu.');
        }

        // OPTIMIZED: Select only IDs first to reduce data transfer
        $criteriaIds = Criterion::pluck('id')->toArray();
        $alternativeIds = Alternative::where('is_active', true)->pluck('id')->toArray();

        // Ambil weights dari active session - need full criteria for weight mapping
        $criteria = Criterion::orderBy('id')->get();
        $weights = [];
        foreach ($criteria as $criterion) {
            $weights[] = $activeSession->getWeightForCriterion($criterion->id);
        }

        // Buat decision dengan waktu saat ini (WIB sesuai config)
        $decision = Decision::create([
            'title' => $request->title,
            'description' => $request->description,
            'decision_date' => now(), // Menggunakan waktu saat ini dengan timezone WIB
            'user_id' => Auth::id(),
            'criteria_ids' => $criteriaIds, // Snapshot criteria yang digunakan
        ]);

        $result = $this->topsisService->calculate($decision, $alternativeIds, $criteriaIds, $weights);

        return redirect()->route('decisions.show', $decision)
            ->with('success', 'Perhitungan keputusan berhasil dilakukan menggunakan bobot dari session "' . $activeSession->name . '".');
    }

    public function show(Decision $decision)
    {
        // OPTIMIZED: Eager load all relationships in one query
        $decision->load([
            'details' => function($query) {
                $query->orderBy('rank');
            },
            'details.alternative',
            'user'
        ]);

        // Gunakan snapshot criteria IDs dari decision untuk menjaga immutability history
        $criteriaIds = $decision->criteria_ids && count($decision->criteria_ids) > 0
            ? $decision->criteria_ids
            : Criterion::pluck('id')->toArray();

        $criteria = Criterion::withTrashed()
            ->whereIn('id', $criteriaIds)
            ->get()
            ->sortBy(function($c) use ($criteriaIds) {
                return array_search($c->id, $criteriaIds);
            })
            ->values();

        // Get the winner (rank 1) - already loaded via eager loading
        $winner = $decision->details->where('rank', 1)->first();

        // OPTIMIZED: Pre-fetch all scores in single query
        $alternativeIds = $decision->details->pluck('alternative_id')->toArray();
        $scores = Score::whereIn('alternative_id', $alternativeIds)
            ->whereIn('criterion_id', $criteriaIds)
            ->get()
            ->groupBy('alternative_id')
            ->map(function($group) {
                return $group->keyBy('criterion_id');
            });

        // OPTIMIZED: Pre-compute all TOPSIS matrices for display
        $topsisData = $this->precomputeTopsisData($criteria, $scores, $alternativeIds, $criteriaIds);

        return view('decisions.show', compact('decision', 'criteria', 'winner', 'scores', 'topsisData'));
    }

    /**
     * Pre-compute all TOPSIS calculation data for display
     * This avoids N+1 queries in the view
     */
    private function precomputeTopsisData($criteria, $scores, $alternativeIds, $criteriaIds)
    {
        $n = count($criteriaIds);

        // 1. Build decision matrix
        $matrix = [];
        foreach ($alternativeIds as $altId) {
            $row = [];
            foreach ($criteriaIds as $critId) {
                $score = $scores->get($altId)?->get($critId);
                $row[$critId] = $score ? $score->score : 0;
            }
            $matrix[$altId] = $row;
        }

        // 2. Calculate sum of squares per criterion (for normalization)
        $sumSquares = [];
        foreach ($criteriaIds as $critId) {
            $sum = 0;
            foreach ($alternativeIds as $altId) {
                $sum += pow($matrix[$altId][$critId], 2);
            }
            $sumSquares[$critId] = sqrt($sum);
        }

        // 3. Normalized matrix
        $normalizedMatrix = [];
        foreach ($alternativeIds as $altId) {
            foreach ($criteriaIds as $critId) {
                $normalizedMatrix[$altId][$critId] = $sumSquares[$critId] > 0
                    ? $matrix[$altId][$critId] / $sumSquares[$critId]
                    : 0;
            }
        }

        // 4. Get weights from active session or use equal weights
        $activeSession = AhpSession::getActiveSession();
        $weights = [];
        if ($activeSession && $activeSession->weights) {
            $i = 0;
            foreach ($criteriaIds as $critId) {
                $weights[$critId] = $activeSession->weights[$i] ?? 0;
                $i++;
            }
        } else {
            $equalWeight = $n > 0 ? 1 / $n : 0;
            foreach ($criteriaIds as $critId) {
                $weights[$critId] = $equalWeight;
            }
        }

        // 5. Weighted normalized matrix
        $weightedMatrix = [];
        foreach ($alternativeIds as $altId) {
            foreach ($criteriaIds as $critId) {
                $weightedMatrix[$altId][$critId] = $normalizedMatrix[$altId][$critId] * $weights[$critId];
            }
        }

        // 6. Ideal solutions (A+ and A-)
        $idealPositive = [];
        $idealNegative = [];
        $criteriaKeyedById = $criteria->keyBy('id');
        foreach ($criteriaIds as $critId) {
            $column = [];
            foreach ($alternativeIds as $altId) {
                $column[] = $weightedMatrix[$altId][$critId];
            }
            $criterion = $criteriaKeyedById->get($critId);
            if ($criterion && $criterion->type === 'benefit') {
                $idealPositive[$critId] = count($column) > 0 ? max($column) : 0;
                $idealNegative[$critId] = count($column) > 0 ? min($column) : 0;
            } else {
                $idealPositive[$critId] = count($column) > 0 ? min($column) : 0;
                $idealNegative[$critId] = count($column) > 0 ? max($column) : 0;
            }
        }

        return [
            'matrix' => $matrix,
            'normalizedMatrix' => $normalizedMatrix,
            'weightedMatrix' => $weightedMatrix,
            'weights' => $weights,
            'idealPositive' => $idealPositive,
            'idealNegative' => $idealNegative,
        ];
    }

    public function destroy(Decision $decision)
    {
        $decision->delete();

        return redirect()->route('decisions.index')
            ->with('success', 'Keputusan berhasil dihapus.');
    }
}
