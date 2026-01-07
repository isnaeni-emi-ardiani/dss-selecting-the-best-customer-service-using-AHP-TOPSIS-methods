<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AhpService;
use App\Services\TopsisService;
use App\Models\{User, Criterion, Alternative, AhpSession, CriteriaComparison, Decision};

class TestDssCalculation extends Command
{
    protected $signature = 'dss:test';
    protected $description = 'Test DSS AHP-TOPSIS calculation with Excel data';

    public function handle()
    {
        $this->info('🧮 Testing DSS AHP-TOPSIS Calculation');
        $this->newLine();

        // Pastikan data sudah ada
        if (Criterion::count() === 0) {
            $this->error('❌ No criteria found. Please run: php artisan db:seed --class=DssTestDataSeeder');
            return Command::FAILURE;
        }

        // Bersihkan data test sebelumnya
        AhpSession::query()->delete();
        Decision::query()->delete();

        // ============= AHP TEST =============
        $this->info('📊 TAHAP 1: AHP CALCULATION');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        $user = User::first();
        $ahpService = new AhpService();

        // Buat AHP Session
        $session = AhpSession::create([
            'name' => 'Test Calculation - Excel Data',
            'user_id' => $user->id,
            'matrix_size' => 4,
        ]);

        // Data perbandingan dari Excel (sheet 2, baris 1-4, kolom B-E)
        $comparisonsInput = [
            [1, 2, 3],    // C1 vs C2 = 3
            [1, 3, 4],    // C1 vs C3 = 4
            [1, 4, 5],    // C1 vs C4 = 5
            [2, 3, 0.5],  // C2 vs C3 = 0.5 (atau 2)
            [2, 4, 3],    // C2 vs C4 = 3
            [3, 4, 2],    // C3 vs C4 = 2
        ];

        $criteriaIds = [1, 2, 3, 4]; // ID kriteria yang digunakan

        $comparisons = [];
        foreach ($comparisonsInput as $comp) {
            $comparison = CriteriaComparison::create([
                'ahp_session_id' => $session->id,
                'criterion_1_id' => $comp[0],
                'criterion_2_id' => $comp[1],
                'value' => $comp[2],
            ]);
            $comparisons[] = $comparison->toArray();
        }

        // Hitung AHP
        $ahpResult = $ahpService->calculate($session, $comparisons, $criteriaIds);

        // Tampilkan hasil
        $this->table(
            ['Criterion', 'C1', 'C2', 'C3', 'C4'],
            [
                ['C1', '1.000', number_format(3, 3), number_format(4, 3), number_format(5, 3)],
                ['C2', number_format(1/3, 3), '1.000', number_format(0.5, 3), number_format(3, 3)],
                ['C3', number_format(1/4, 3), number_format(2, 3), '1.000', number_format(2, 3)],
                ['C4', number_format(1/5, 3), number_format(1/3, 3), number_format(0.5, 3), '1.000'],
            ]
        );

        $this->info('📍 Column Sums:');
        foreach ($ahpResult['columnSums'] as $i => $sum) {
            $this->line('  Column ' . ($i+1) . ': ' . number_format($sum, 3));
        }

        $this->newLine();
        $this->info('📍 Priorities (Weights):');
        $criteria = Criterion::all();
        foreach ($ahpResult['priorities'] as $i => $priority) {
            $this->line('  ' . $criteria[$i]->code . ': ' . number_format($priority, 3));

            // Update weight di database
            $criteria[$i]->update(['weight' => $priority]);
        }

        $this->newLine();
        $this->info('📍 Consistency Check:');
        $this->line('  λ max: ' . number_format($ahpResult['lambdaMax'], 3));
        $this->line('  CI: ' . number_format($ahpResult['ci'], 3));
        $this->line('  RI: ' . number_format($ahpResult['ri'], 3));
        $this->line('  CR: ' . number_format($ahpResult['cr'], 3));

        if ($ahpResult['isConsistent']) {
            $this->info('  ✓ CONSISTENT (CR < 0.1)');
        } else {
            $this->error('  ✗ INCONSISTENT (CR ≥ 0.1)');
        }

        $this->newLine(2);

        // ============= TOPSIS TEST =============
        $this->info('📊 TAHAP 2: TOPSIS CALCULATION');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        $topsisService = new TopsisService();

        // Buat Decision
        $decision = Decision::create([
            'title' => 'Test TOPSIS - Excel Data',
            'description' => 'Evaluasi CS menggunakan data dari Excel',
            'decision_date' => today(),
            'user_id' => $user->id,
        ]);

        $alternativeIds = Alternative::pluck('id')->toArray();
        $criteriaIds = Criterion::pluck('id')->toArray();
        $weights = $criteria->pluck('weight')->toArray();

        // Hitung TOPSIS
        $topsisResult = $topsisService->calculate($decision, $alternativeIds, $criteriaIds, $weights);

        // Tampilkan Decision Matrix
        $this->info('📍 Decision Matrix (X):');
        $alternatives = Alternative::all();
        $headers = array_merge(['CS'], $criteria->pluck('code')->toArray());
        $rows = [];
        foreach ($topsisResult['matrix'] as $i => $row) {
            $rows[] = array_merge([$alternatives[$i]->name], $row);
        }
        $this->table($headers, $rows);

        $this->newLine();

        // Tampilkan Hasil Akhir
        $this->info('📍 FINAL RESULTS (Ranking):');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        $results = collect($topsisResult['results'])->sortBy('rank');
        $finalRows = [];
        foreach ($results as $result) {
            $alt = Alternative::find($result['alternative_id']);
            $finalRows[] = [
                $result['rank'],
                $alt->code,
                $alt->name,
                number_format($result['d_plus'], 6),
                number_format($result['d_minus'], 6),
                number_format($result['preference_value'], 6),
            ];
        }

        $this->table(
            ['Rank', 'Code', 'Name', 'D+', 'D-', 'C* (Preference)'],
            $finalRows
        );

        $this->newLine();
        $winner = Alternative::find($results->first()['alternative_id']);
        $this->info('🏆 WINNER: ' . $winner->name . ' (' . $winner->code . ')');

        $this->newLine();
        $this->info('✅ Calculation completed! Compare with your Excel file.');

        return Command::SUCCESS;
    }
}
