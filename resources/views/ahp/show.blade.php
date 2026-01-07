@extends('layouts.app')

@section('title', 'Detail Session AHP')
@section('header', 'Detail Session AHP')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('ahp.index') }}" class="inline-flex items-center text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors group">
                    <svg class="w-4 h-4 mr-1.5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali
                </a>
            </div>
            <h2 class="text-lg font-semibold text-slate-900">Detail Session AHP</h2>
            <p class="mt-1 text-sm text-slate-600">Hasil perhitungan bobot kriteria dengan metode AHP</p>
        </div>
    </div>

    <!-- Session Info Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-br from-indigo-50 via-purple-50 to-white border-b border-slate-200">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-xl font-bold text-slate-900 mb-2">{{ $session->name }}</h2>
                    <p class="text-sm text-slate-600">{{ $session->created_at ? $session->created_at->format('d M Y H:i') . ' WIB' : '-' }}</p>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold text-indigo-600 mb-1">{{ $session->matrix_size }}×{{ $session->matrix_size }}</div>
                    <p class="text-xs text-slate-600">Matrix Size</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Consistency Check -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-slate-900">Consistency Ratio</h3>
                    @if($session->is_consistent)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-100 text-emerald-700">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Konsisten
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-red-100 text-red-700">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            Inkonsisten
                        </span>
                    @endif
                </div>
                <div class="text-4xl font-bold {{ $session->is_consistent ? 'text-emerald-600' : 'text-red-600' }} mb-2">
                    {{ number_format($session->consistency_ratio ?? 0, 4) }}
                </div>
                <p class="text-sm text-slate-600">CR < 0.1 menunjukkan perbandingan konsisten</p>
            </div>
        </div>

        <!-- Lambda Max -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6">
                <h3 class="text-base font-bold text-slate-900 mb-4">Lambda Max (λmax)</h3>
                <div class="text-4xl font-bold text-indigo-600 mb-2">
                    {{ number_format($session->lambda_max ?? 0, 4) }}
                </div>
                <p class="text-sm text-slate-600">Eigenvalue maksimum dari matriks</p>
            </div>
        </div>

        <!-- CI & RI -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6">
                <h3 class="text-base font-bold text-slate-900 mb-4">Consistency Index</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-slate-600">CI (Consistency Index)</p>
                        <p class="text-2xl font-bold text-slate-900">{{ number_format($session->matrix_size > 1 ? (($session->lambda_max ?? 0) - $session->matrix_size) / ($session->matrix_size - 1) : 0, 4) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600">RI (Random Index)</p>
                        <p class="text-2xl font-bold text-slate-900">{{ number_format($session->random_index ?? 0, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Perhitungan AHP -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
            <h3 class="text-base font-bold text-slate-900">Detail Perhitungan AHP</h3>
            <p class="text-sm text-slate-600 mt-1">Langkah-langkah perhitungan metode Analytical Hierarchy Process</p>
        </div>

        <div class="p-6">
            <!-- Accordion -->
            <div class="space-y-4">
                <!-- 1. Matriks Perbandingan Berpasangan -->
                <details class="group" open>
                    <summary class="flex items-center justify-between cursor-pointer list-none p-4 bg-slate-50 hover:bg-slate-100 rounded-xl transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-8 h-8 bg-indigo-100 text-indigo-600 rounded-lg font-bold text-sm">1</div>
                            <div>
                                <h4 class="font-semibold text-slate-900">Matriks Perbandingan Berpasangan</h4>
                                <p class="text-xs text-slate-600">Nilai perbandingan antar kriteria menggunakan skala Saaty</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-slate-400 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </summary>
                    <div class="mt-4 overflow-x-auto">
                        <table class="w-full text-sm border-collapse">
                            <thead>
                                <tr>
                                    <th class="border border-slate-300 bg-slate-100 px-4 py-2 font-semibold text-slate-700"></th>
                                    @foreach($criteria as $criterion)
                                        <th class="border border-slate-300 bg-indigo-50 px-4 py-2 font-semibold text-slate-700">
                                            {{ $criterion->code }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($criteria as $i => $criterion1)
                                    <tr>
                                        <td class="border border-slate-300 bg-indigo-50 px-4 py-2 font-semibold text-slate-700">
                                            {{ $criterion1->code }}
                                        </td>
                                        @foreach($criteria as $j => $criterion2)
                                            <td class="border border-slate-300 px-4 py-2 text-center {{ $i == $j ? 'bg-indigo-100 font-semibold' : 'bg-white' }}">
                                                {{ number_format($comparisonMatrix[$i][$j] ?? 1, 3) }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </details>

                <!-- 2. Jumlah Tiap Kolom -->
                <details class="group">
                    <summary class="flex items-center justify-between cursor-pointer list-none p-4 bg-slate-50 hover:bg-slate-100 rounded-xl transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-8 h-8 bg-indigo-100 text-indigo-600 rounded-lg font-bold text-sm">2</div>
                            <div>
                                <h4 class="font-semibold text-slate-900">Jumlah Tiap Kolom</h4>
                                <p class="text-xs text-slate-600">Total nilai pada setiap kolom matriks</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-slate-400 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </summary>
                    <div class="mt-4 overflow-x-auto">
                        <table class="w-full text-sm border-collapse">
                            <thead>
                                <tr class="bg-slate-100">
                                    <th class="border border-slate-300 px-4 py-2 text-left font-semibold text-slate-700">Kriteria</th>
                                    <th class="border border-slate-300 px-4 py-2 text-center font-semibold text-slate-700">Jumlah Kolom</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($criteria as $j => $criterion)
                                @php
                                    $columnSum = 0;
                                    for ($i = 0; $i < count($criteria); $i++) {
                                        $columnSum += $comparisonMatrix[$i][$j];
                                    }
                                @endphp
                                <tr class="hover:bg-slate-50">
                                    <td class="border border-slate-300 px-4 py-2 font-medium">{{ $criterion->code }} - {{ $criterion->name }}</td>
                                    <td class="border border-slate-300 px-4 py-2 text-center font-mono">{{ number_format($columnSum, 6) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </details>

                <!-- 3. Matriks Normalisasi -->
                <details class="group">
                    <summary class="flex items-center justify-between cursor-pointer list-none p-4 bg-slate-50 hover:bg-slate-100 rounded-xl transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-8 h-8 bg-indigo-100 text-indigo-600 rounded-lg font-bold text-sm">3</div>
                            <div>
                                <h4 class="font-semibold text-slate-900">Matriks Normalisasi</h4>
                                <p class="text-xs text-slate-600">Setiap elemen dibagi dengan jumlah kolomnya</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-slate-400 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </summary>
                    <div class="mt-4 overflow-x-auto">
                        <table class="w-full text-sm border-collapse">
                            <thead>
                                <tr>
                                    <th class="border border-slate-300 bg-slate-100 px-4 py-2 font-semibold text-slate-700"></th>
                                    @foreach($criteria as $criterion)
                                        <th class="border border-slate-300 bg-indigo-50 px-4 py-2 font-semibold text-slate-700">
                                            {{ $criterion->code }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($criteria as $i => $criterion1)
                                    <tr>
                                        <td class="border border-slate-300 bg-indigo-50 px-4 py-2 font-semibold text-slate-700">
                                            {{ $criterion1->code }}
                                        </td>
                                        @foreach($criteria as $j => $criterion2)
                                            @php
                                                $columnSum = 0;
                                                for ($k = 0; $k < count($criteria); $k++) {
                                                    $columnSum += $comparisonMatrix[$k][$j];
                                                }
                                                $normalized = $columnSum > 0 ? $comparisonMatrix[$i][$j] / $columnSum : 0;
                                            @endphp
                                            <td class="border border-slate-300 px-4 py-2 text-center font-mono text-xs bg-white">
                                                {{ number_format($normalized, 8) }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </details>

                <!-- 4. Perhitungan Bobot Prioritas -->
                <details class="group">
                    <summary class="flex items-center justify-between cursor-pointer list-none p-4 bg-slate-50 hover:bg-slate-100 rounded-xl transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-8 h-8 bg-indigo-100 text-indigo-600 rounded-lg font-bold text-sm">4</div>
                            <div>
                                <h4 class="font-semibold text-slate-900">Perhitungan Bobot Prioritas</h4>
                                <p class="text-xs text-slate-600">Rata-rata baris matriks normalisasi (Geometric Mean)</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-slate-400 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </summary>
                    <div class="mt-4">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm border-collapse">
                                <thead>
                                    <tr class="bg-slate-100">
                                        <th class="border border-slate-300 px-4 py-3 text-left font-semibold text-slate-700">Kode</th>
                                        <th class="border border-slate-300 px-4 py-3 text-left font-semibold text-slate-700">Nama Kriteria</th>
                                        <th class="border border-slate-300 px-4 py-3 text-center font-semibold text-slate-700">Bobot</th>
                                        <th class="border border-slate-300 px-4 py-3 text-center font-semibold text-slate-700">Persentase</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($criteria as $index => $criterion)
                                    <tr class="hover:bg-slate-50">
                                        <td class="border border-slate-300 px-4 py-3 text-center">
                                            <span class="inline-flex items-center justify-center w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 text-white rounded-lg font-semibold text-sm">
                                                {{ $criterion->code }}
                                            </span>
                                        </td>
                                        <td class="border border-slate-300 px-4 py-3 font-medium text-slate-900">
                                            {{ $criterion->name }}
                                        </td>
                                        <td class="border border-slate-300 px-4 py-3 text-center">
                                            <span class="text-base font-mono font-semibold text-slate-700">{{ number_format($weights[$index] ?? 0, 6) }}</span>
                                        </td>
                                        <td class="border border-slate-300 px-4 py-3 text-center">
                                            <span class="text-lg font-bold text-indigo-600">{{ number_format(($weights[$index] ?? 0) * 100, 2) }}%</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </details>

                <!-- 5. Perhitungan Lambda Max -->
                <details class="group">
                    <summary class="flex items-center justify-between cursor-pointer list-none p-4 bg-slate-50 hover:bg-slate-100 rounded-xl transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-8 h-8 bg-indigo-100 text-indigo-600 rounded-lg font-bold text-sm">5</div>
                            <div>
                                <h4 class="font-semibold text-slate-900">Perhitungan Lambda Max (λmax)</h4>
                                <p class="text-xs text-slate-600">Eigenvalue maksimum untuk uji konsistensi</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-slate-400 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </summary>
                    <div class="mt-4">
                        <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4 mb-4">
                            <h5 class="font-semibold text-slate-900 mb-3 text-sm">Weighted Sum Vector (Matriks × Weights)</h5>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm border-collapse">
                                    <thead>
                                        <tr class="bg-white">
                                            <th class="border border-slate-300 px-4 py-2 text-left font-semibold text-slate-700">Kriteria</th>
                                            <th class="border border-slate-300 px-4 py-2 text-center font-semibold text-slate-700">Weighted Sum</th>
                                            <th class="border border-slate-300 px-4 py-2 text-center font-semibold text-slate-700">λ (Weighted Sum / Weight)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $lambdaSum = 0;
                                        @endphp
                                        @foreach($criteria as $i => $criterion)
                                            @php
                                                $weightedSum = 0;
                                                for ($j = 0; $j < count($criteria); $j++) {
                                                    $weightedSum += $comparisonMatrix[$i][$j] * $weights[$j];
                                                }
                                                $lambda = $weights[$i] > 0 ? $weightedSum / $weights[$i] : 0;
                                                $lambdaSum += $lambda;
                                            @endphp
                                            <tr class="hover:bg-slate-50">
                                                <td class="border border-slate-300 px-4 py-2 font-medium">{{ $criterion->code }} - {{ $criterion->name }}</td>
                                                <td class="border border-slate-300 px-4 py-2 text-center font-mono text-xs">{{ number_format($weightedSum, 8) }}</td>
                                                <td class="border border-slate-300 px-4 py-2 text-center font-mono text-xs">{{ number_format($lambda, 8) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="bg-white border border-slate-300 rounded-xl p-4">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-slate-900">Lambda Max (λmax) = Rata-rata λ</span>
                                <span class="text-2xl font-bold text-indigo-600">{{ number_format($session->lambda_max ?? 0, 6) }}</span>
                            </div>
                        </div>
                    </div>
                </details>

                <!-- 6. Uji Konsistensi -->
                <details class="group">
                    <summary class="flex items-center justify-between cursor-pointer list-none p-4 bg-slate-50 hover:bg-slate-100 rounded-xl transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-8 h-8 bg-indigo-100 text-indigo-600 rounded-lg font-bold text-sm">6</div>
                            <div>
                                <h4 class="font-semibold text-slate-900">Uji Konsistensi</h4>
                                <p class="text-xs text-slate-600">Perhitungan CI, RI, dan CR untuk validasi</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-slate-400 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </summary>
                    <div class="mt-4 space-y-4">
                        <!-- Formula Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- CI Card -->
                            <div class="bg-white border border-slate-300 rounded-xl p-4">
                                <h6 class="text-xs font-semibold text-slate-600 mb-2">Consistency Index (CI)</h6>
                                <div class="mb-2">
                                    <code class="text-xs bg-slate-100 px-2 py-1 rounded">CI = (λmax - n) / (n - 1)</code>
                                </div>
                                @php
                                    $n = $session->matrix_size;
                                    $ci = $n > 1 ? (($session->lambda_max ?? 0) - $n) / ($n - 1) : 0;
                                @endphp
                                <div class="text-xs text-slate-600 mb-1">
                                    CI = ({{ number_format($session->lambda_max ?? 0, 6) }} - {{ $n }}) / ({{ $n }} - 1)
                                </div>
                                <div class="text-2xl font-bold text-slate-900">{{ number_format($ci, 6) }}</div>
                            </div>

                            <!-- RI Card -->
                            <div class="bg-white border border-slate-300 rounded-xl p-4">
                                <h6 class="text-xs font-semibold text-slate-600 mb-2">Random Index (RI)</h6>
                                <div class="mb-2">
                                    <code class="text-xs bg-slate-100 px-2 py-1 rounded">Dari tabel standar</code>
                                </div>
                                <div class="text-xs text-slate-600 mb-1">
                                    Untuk n = {{ $n }}
                                </div>
                                <div class="text-2xl font-bold text-slate-900">{{ number_format($session->random_index ?? 0, 2) }}</div>
                            </div>

                            <!-- CR Card -->
                            <div class="bg-white border border-{{ $session->is_consistent ? 'emerald' : 'red' }}-300 rounded-xl p-4">
                                <h6 class="text-xs font-semibold text-slate-600 mb-2">Consistency Ratio (CR)</h6>
                                <div class="mb-2">
                                    <code class="text-xs bg-slate-100 px-2 py-1 rounded">CR = CI / RI</code>
                                </div>
                                <div class="text-xs text-slate-600 mb-1">
                                    CR = {{ number_format($ci, 6) }} / {{ number_format($session->random_index ?? 0, 2) }}
                                </div>
                                <div class="text-2xl font-bold {{ $session->is_consistent ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ number_format($session->consistency_ratio ?? 0, 6) }}
                                </div>
                            </div>
                        </div>

                        <!-- Kesimpulan -->
                        <div class="bg-{{ $session->is_consistent ? 'emerald' : 'red' }}-50 border border-{{ $session->is_consistent ? 'emerald' : 'red' }}-200 rounded-xl p-4">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0">
                                    @if($session->is_consistent)
                                        <svg class="w-6 h-6 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    @else
                                        <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="font-semibold {{ $session->is_consistent ? 'text-emerald-900' : 'text-red-900' }} mb-1">
                                        {{ $session->is_consistent ? 'Perbandingan Konsisten' : 'Perbandingan Tidak Konsisten' }}
                                    </h6>
                                    <p class="text-sm {{ $session->is_consistent ? 'text-emerald-700' : 'text-red-700' }}">
                                        @if($session->is_consistent)
                                            CR = {{ number_format($session->consistency_ratio ?? 0, 4) }} < 0.1, maka perbandingan berpasangan konsisten dan bobot yang dihasilkan dapat dipercaya.
                                        @else
                                            CR = {{ number_format($session->consistency_ratio ?? 0, 4) }} ≥ 0.1, maka perbandingan berpasangan tidak konsisten. Disarankan untuk meninjau ulang nilai perbandingan.
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </details>
            </div>
        </div>
    </div>

    <!-- Explanation Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
            <h3 class="text-base font-bold text-slate-900">Interpretasi Hasil</h3>
        </div>
        <div class="p-6 space-y-3">
            <div class="flex items-start">
                <div class="flex-shrink-0 flex items-center justify-center w-8 h-8 bg-indigo-100 rounded-lg mr-3">
                    <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="text-sm text-slate-700">
                    <span class="font-semibold text-slate-900">Bobot Prioritas:</span> Menunjukkan tingkat kepentingan relatif setiap kriteria. Total bobot selalu = 100%.
                </div>
            </div>
            <div class="flex items-start">
                <div class="flex-shrink-0 flex items-center justify-center w-8 h-8 bg-indigo-100 rounded-lg mr-3">
                    <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="text-sm text-slate-700">
                    <span class="font-semibold text-slate-900">Lambda Max (λmax):</span> Eigenvalue maksimum matriks. Nilai ideal = n (jumlah kriteria). Semakin dekat ke n, semakin konsisten.
                </div>
            </div>
            <div class="flex items-start">
                <div class="flex-shrink-0 flex items-center justify-center w-8 h-8 bg-indigo-100 rounded-lg mr-3">
                    <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="text-sm text-slate-700">
                    <span class="font-semibold text-slate-900">Consistency Ratio (CR):</span> Rasio konsistensi perbandingan. CR < 0.1 dianggap konsisten dan dapat diterima untuk pengambilan keputusan.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
