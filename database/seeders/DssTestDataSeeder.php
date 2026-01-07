<?php

namespace Database\Seeders;

use App\Models\{User, Criterion, Alternative, Score};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DssTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create test user
        $user = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
            ]
        );

        // 2. Create 4 criteria (code will be auto-generated)
        $criteriaData = [
            [
                'name' => 'Pencapaian Target Sales 50jt',
                'description' => 'Kemampuan mencapai target penjualan bulanan sebesar 50 juta rupiah',
                'type' => 'benefit',
            ],
            [
                'name' => 'Respon customer (response rate chat)',
                'description' => 'Kecepatan dan ketepatan dalam merespon pertanyaan customer melalui chat',
                'type' => 'benefit',
            ],
            [
                'name' => 'Target FU customer 1000 data',
                'description' => 'Kemampuan melakukan follow-up kepada minimal 1000 data customer per bulan',
                'type' => 'benefit',
            ],
            [
                'name' => 'Mematuhi SOP',
                'description' => 'Konsistensi dalam mengikuti Standard Operating Procedure perusahaan',
                'type' => 'benefit',
            ],
        ];

        foreach ($criteriaData as $data) {
            Criterion::firstOrCreate(
                ['name' => $data['name']],
                $data
            );
        }

        // 3. Create 10 alternatives dengan scores (code will be auto-generated)
        // Format: [nama, [C1, C2, C3, C4]]
        $alternativesData = [
            ['Nura', [5, 5, 5, 3]],
            ['Ayik', [3, 5, 5, 4]],
            ['Nur', [4, 5, 4, 3]],
            ['Aida', [4, 5, 5, 4]],
            ['Tiwi', [4, 4, 4, 4]],
            ['Nanda', [5, 5, 5, 4]],
            ['Riri', [3, 4, 4, 3]],
            ['Evi', [3, 4, 5, 4]],
            ['Jusita', [2, 3, 4, 3]],
            ['Latifah', [2, 4, 4, 4]],
        ];

        // Get all criteria IDs in order
        $criteria = Criterion::orderBy('id')->get();

        foreach ($alternativesData as [$name, $scores]) {
            $alternative = Alternative::firstOrCreate(
                ['name' => $name],
                [
                    'description' => 'Customer Service Staff - ' . $name,
                    'is_active' => true,
                ]
            );

            // Create scores for each criterion
            foreach ($criteria as $index => $criterion) {
                Score::updateOrCreate(
                    [
                        'alternative_id' => $alternative->id,
                        'criterion_id' => $criterion->id,
                    ],
                    [
                        'score' => $scores[$index],
                    ]
                );
            }
        }

        $this->command->info('✓ Database seeded successfully!');
        $this->command->info('  - 1 user created (admin@test.com / password)');
        $this->command->info('  - 4 criteria created');
        $this->command->info('  - 10 alternatives created with scores');
    }
}
