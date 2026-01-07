<?php

namespace App\Http\Controllers;

use App\Models\{Criterion, AhpSession, CriteriaComparison};
use App\Services\AhpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AhpController extends Controller
{
    protected $ahpService;

    public function __construct(AhpService $ahpService)
    {
        $this->ahpService = $ahpService;
    }

    public function index()
    {
        $sessions = AhpSession::where('user_id', Auth::id())
            ->latest()
            ->with('comparisons')
            ->get();

        return view('ahp.index', compact('sessions'));
    }

    public function create()
    {
        $criteria = Criterion::all();

        if ($criteria->count() < 2) {
            return redirect()->route('criteria.index')
                ->with('error', 'Minimal 2 kriteria diperlukan untuk perbandingan AHP.');
        }

        return view('ahp.create', compact('criteria'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'comparisons' => 'required|array|min:1',
        ]);

        $criteria = Criterion::orderBy('id')->get();
        $n = $criteria->count();

        if ($n < 2) {
            return redirect()->route('criteria.index')
                ->with('error', 'Minimal 2 kriteria diperlukan untuk perbandingan AHP.');
        }

        try {
            // Get criteria IDs untuk snapshot - menjaga immutability history
            $criteriaIds = $criteria->pluck('id')->toArray();

            // Buat session baru dengan snapshot criteria
            $session = AhpSession::create([
                'name' => $request->name,
                'user_id' => Auth::id(),
                'matrix_size' => $n,
                'criteria_ids' => $criteriaIds, // Snapshot criteria yang digunakan
            ]);

            // Simpan perbandingan dan validasi tidak ada duplikat
            $comparisons = [];
            $savedPairs = [];

            foreach ($request->comparisons as $key => $value) {
                [$c1, $c2] = explode('-', $key);

                // Pastikan value valid
                if (!is_numeric($value) || $value <= 0) {
                    throw new \Exception("Nilai perbandingan tidak valid untuk {$key}");
                }

                // Cek duplikat (baik c1-c2 maupun c2-c1)
                $pairKey = $c1 < $c2 ? "{$c1}-{$c2}" : "{$c2}-{$c1}";
                if (in_array($pairKey, $savedPairs)) {
                    continue; // Skip jika sudah ada
                }
                $savedPairs[] = $pairKey;

                $comparison = CriteriaComparison::create([
                    'ahp_session_id' => $session->id,
                    'criterion_1_id' => $c1,
                    'criterion_2_id' => $c2,
                    'value' => $value,
                ]);

                $comparisons[] = $comparison->toArray();
            }

            // Validasi jumlah perbandingan yang dibutuhkan
            $requiredComparisons = ($n * ($n - 1)) / 2;
            if (count($comparisons) < $requiredComparisons) {
                $session->delete();
                return redirect()->back()
                    ->withInput()
                    ->with('error', "Perbandingan tidak lengkap. Dibutuhkan {$requiredComparisons} perbandingan, hanya " . count($comparisons) . " yang valid.");
            }

            // Hitung AHP
            $result = $this->ahpService->calculate($session, $comparisons, $criteriaIds);

            // Tidak perlu update weight di criteria table lagi
            // Bobot sekarang langsung tersimpan di session->weights

            $crStatus = $result['cr'] < 0.1 ? 'Konsisten' : 'Tidak Konsisten';

            return redirect()->route('ahp.show', $session)
                ->with('success', "Perhitungan AHP berhasil. CR: " . number_format($result['cr'], 3) . " ({$crStatus})");

        } catch (\Exception $e) {
            // Jika ada session yang sudah dibuat, hapus
            if (isset($session)) {
                $session->delete();
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(AhpSession $ahp)
    {
        $ahp->load('comparisons.criterion1', 'comparisons.criterion2');

        // Gunakan snapshot criteria IDs dari AHP session untuk menjaga immutability history
        // Jika criteria_ids tidak ada (data lama sebelum migration), gunakan semua criteria
        if ($ahp->criteria_ids && count($ahp->criteria_ids) > 0) {
            $criteria = Criterion::withTrashed()
                ->whereIn('id', $ahp->criteria_ids)
                ->orderBy('id')
                ->get();
        } else {
            $criteria = Criterion::withTrashed()->orderBy('id')->get();
        }

        // Build matrix untuk tampilan
        $n = $criteria->count();

        // Buat mapping dari criterion_id ke index array (0-based)
        $criteriaIdToIndex = [];
        foreach ($criteria as $index => $criterion) {
            $criteriaIdToIndex[$criterion->id] = $index;
        }

        $comparisonMatrix = array_fill(0, $n, array_fill(0, $n, 1));

        foreach ($ahp->comparisons as $comp) {
            $i = $criteriaIdToIndex[$comp->criterion_1_id] ?? 0;
            $j = $criteriaIdToIndex[$comp->criterion_2_id] ?? 0;
            $comparisonMatrix[$i][$j] = $comp->value;
            $comparisonMatrix[$j][$i] = 1 / $comp->value;
        }

        // GUNAKAN bobot yang sudah tersimpan di database, JANGAN hitung ulang
        // Ini untuk menjaga konsistensi dengan bobot yang digunakan di TOPSIS
        $weights = $ahp->weights ?? [];

        // Rename variable untuk view
        $session = $ahp;

        return view('ahp.show', compact('session', 'criteria', 'comparisonMatrix', 'weights'));
    }

    public function destroy(AhpSession $ahp)
    {
        $ahp->delete();

        return redirect()->route('ahp.index')
            ->with('success', 'Session AHP berhasil dihapus.');
    }

    public function activate(AhpSession $ahp)
    {
        // Validasi: session hanya bisa diaktifkan jika jumlah criteria sama
        if (!$ahp->canBeActivated()) {
            $currentCount = Criterion::count();
            $sessionCount = count($ahp->criteria_ids ?? []);

            return redirect()->back()
                ->with('error', "Session tidak dapat diaktifkan. Jumlah kriteria tidak sesuai (Session: {$sessionCount}, Saat ini: {$currentCount}). Silakan buat session AHP baru dengan {$currentCount} kriteria.");
        }

        // Activate session
        $ahp->activate();

        return redirect()->route('ahp.index')
            ->with('success', "Session \"{$ahp->name}\" berhasil diaktifkan. Bobot dari session ini akan digunakan untuk perhitungan TOPSIS.");
    }

    public function deactivate(AhpSession $ahp)
    {
        $ahp->update(['is_active' => false]);

        return redirect()->route('ahp.index')
            ->with('success', "Session \"{$ahp->name}\" berhasil dinonaktifkan.");
    }
}
