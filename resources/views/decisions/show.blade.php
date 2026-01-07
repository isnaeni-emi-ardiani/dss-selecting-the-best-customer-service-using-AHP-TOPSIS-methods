@extends('layouts.app')

@section('title', 'Detail Keputusan')
@section('header', 'Detail Hasil Keputusan')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('decisions.index') }}" class="inline-flex items-center text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors group">
                    <svg class="w-4 h-4 mr-1.5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali
                </a>
            </div>
            <h2 class="text-lg font-semibold text-slate-900">Detail Hasil Keputusan</h2>
            <p class="mt-1 text-sm text-slate-600">Analisis lengkap hasil perhitungan TOPSIS</p>
        </div>
    </div>

    <!-- Decision Info Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-br from-indigo-50 via-purple-50 to-white border-b border-slate-200">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-xl font-bold text-slate-900 mb-2">{{ $decision->title }}</h2>
                    <p class="text-sm text-slate-600 mb-4">{{ $decision->description ?? 'Hasil perhitungan ranking menggunakan metode TOPSIS' }}</p>
                    <div class="flex items-center space-x-4 text-xs text-slate-600">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $decision->decision_date->format('d F Y') }}
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            {{ $decision->user->name }}
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold text-indigo-600 mb-1">{{ $decision->details->count() }}</div>
                    <p class="text-xs text-slate-600">CS Dievaluasi</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Winner Card -->
    @if($winner)
    <div class="bg-white rounded-xl shadow-sm border border-emerald-200 overflow-hidden">
        <div class="flex items-center gap-4 p-4 bg-gradient-to-r from-emerald-50 to-white">
            <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-lg shadow-sm">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-slate-600 mb-0.5">CS Terbaik</p>
                <h3 class="text-lg font-bold text-emerald-600 truncate">{{ $winner->alternative->name }}</h3>
            </div>
            <div class="flex-shrink-0 text-right">
                <p class="text-xs text-slate-600 mb-0.5">Preference Value</p>
                <p class="text-base font-bold text-emerald-700">{{ number_format($winner->preference_value, 4) }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Ranking Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
            <h3 class="text-base font-bold text-slate-900">Ranking Customer Service</h3>
            <p class="text-sm text-slate-600 mt-1">Urutan CS berdasarkan nilai preferensi TOPSIS</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider w-20">Rank</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Customer Service</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">D+ (Ideal)</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">D- (Anti-Ideal)</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">Preference (C*)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($decision->details as $detail)
                    <tr class="hover:bg-slate-50 transition-colors duration-150 {{ $detail->rank == 1 ? 'bg-emerald-50/50' : '' }}">
                        <td class="px-6 py-4 text-center">
                            @if($detail->rank == 1)
                                <div class="inline-flex items-center justify-center w-12 h-12 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-full shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </div>
                            @elseif($detail->rank == 2)
                                <div class="inline-flex items-center justify-center w-12 h-12 bg-gradient-to-br from-slate-300 to-slate-400 rounded-full shadow-md">
                                    <span class="text-xl font-bold text-white">2</span>
                                </div>
                            @elseif($detail->rank == 3)
                                <div class="inline-flex items-center justify-center w-12 h-12 bg-gradient-to-br from-amber-300 to-amber-400 rounded-full shadow-md">
                                    <span class="text-xl font-bold text-white">3</span>
                                </div>
                            @else
                                <div class="inline-flex items-center justify-center w-12 h-12 bg-slate-100 rounded-full">
                                    <span class="text-xl font-bold text-slate-600">{{ $detail->rank }}</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold mr-4">
                                    {{ $detail->alternative->code }}
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-slate-900">{{ $detail->alternative->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $detail->alternative->description ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm font-mono text-slate-700">{{ number_format($detail->d_plus, 6) }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm font-mono text-slate-700">{{ number_format($detail->d_minus, 6) }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-lg font-bold {{ $detail->rank <= 3 ? 'text-indigo-600' : 'text-slate-700' }}">
                                {{ number_format($detail->preference_value, 6) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Detail Perhitungan TOPSIS -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
            <h3 class="text-base font-bold text-slate-900">Detail Perhitungan TOPSIS</h3>
            <p class="text-sm text-slate-600 mt-1">Langkah-langkah perhitungan metode TOPSIS</p>
        </div>

        <div class="p-6">
            <!-- Accordion -->
            <div class="space-y-4">
                <!-- 1. Matriks Keputusan -->
                <details class="group" open>
                    <summary class="flex items-center justify-between cursor-pointer list-none p-4 bg-slate-50 hover:bg-slate-100 rounded-xl transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-8 h-8 bg-indigo-100 text-indigo-600 rounded-lg font-bold text-sm">1</div>
                            <div>
                                <h4 class="font-semibold text-slate-900">Matriks Keputusan (X)</h4>
                                <p class="text-xs text-slate-600">Data penilaian awal setiap CS terhadap kriteria</p>
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
                                    <th class="border border-slate-300 px-4 py-2 text-left text-slate-700 font-semibold">No</th>
                                    <th class="border border-slate-300 px-4 py-2 text-left text-slate-700 font-semibold">CS / Alternatif</th>
                                    @foreach($criteria as $criterion)
                                    <th class="border border-slate-300 px-4 py-2 text-center text-slate-700 font-semibold">{{ $criterion->code }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($decision->details as $index => $detail)
                                <tr class="hover:bg-slate-50">
                                    <td class="border border-slate-300 px-4 py-2 text-center">{{ $index + 1 }}</td>
                                    <td class="border border-slate-300 px-4 py-2 font-medium">{{ $detail->alternative->name }}</td>
                                    @foreach($criteria as $criterion)
                                    <td class="border border-slate-300 px-4 py-2 text-center font-mono">{{ $topsisData['matrix'][$detail->alternative_id][$criterion->id] ?? '-' }}</td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </details>

                <!-- 2. Matriks Normalisasi -->
                <details class="group">
                    <summary class="flex items-center justify-between cursor-pointer list-none p-4 bg-slate-50 hover:bg-slate-100 rounded-xl transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-8 h-8 bg-indigo-100 text-indigo-600 rounded-lg font-bold text-sm">2</div>
                            <div>
                                <h4 class="font-semibold text-slate-900">Matriks Normalisasi (R)</h4>
                                <p class="text-xs text-slate-600">Normalisasi menggunakan metode Euclidean</p>
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
                                    <th class="border border-slate-300 px-4 py-2 text-left text-slate-700 font-semibold">No</th>
                                    <th class="border border-slate-300 px-4 py-2 text-left text-slate-700 font-semibold">CS / Alternatif</th>
                                    @foreach($criteria as $criterion)
                                    <th class="border border-slate-300 px-4 py-2 text-center text-slate-700 font-semibold">{{ $criterion->code }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($decision->details as $index => $detail)
                                <tr class="hover:bg-slate-50">
                                    <td class="border border-slate-300 px-4 py-2 text-center">{{ $index + 1 }}</td>
                                    <td class="border border-slate-300 px-4 py-2 font-medium">{{ $detail->alternative->name }}</td>
                                    @foreach($criteria as $criterion)
                                    <td class="border border-slate-300 px-4 py-2 text-center font-mono text-xs">{{ number_format($topsisData['normalizedMatrix'][$detail->alternative_id][$criterion->id] ?? 0, 6) }}</td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </details>

                <!-- 3. Matriks Normalisasi Terbobot -->
                <details class="group">
                    <summary class="flex items-center justify-between cursor-pointer list-none p-4 bg-slate-50 hover:bg-slate-100 rounded-xl transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-8 h-8 bg-indigo-100 text-indigo-600 rounded-lg font-bold text-sm">3</div>
                            <div>
                                <h4 class="font-semibold text-slate-900">Matriks Normalisasi Terbobot (V)</h4>
                                <p class="text-xs text-slate-600">Matriks R dikalikan dengan bobot kriteria dari AHP</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-slate-400 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </summary>
                    <div class="mt-4 space-y-4">
                        <!-- Bobot Kriteria -->
                        <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4">
                            <h5 class="font-semibold text-slate-900 mb-3 text-sm">Bobot Kriteria (dari AHP)</h5>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                @foreach($criteria as $criterion)
                                <div class="bg-white rounded-lg p-3 border border-indigo-200">
                                    <div class="text-xs text-slate-600">{{ $criterion->code }} - {{ $criterion->name }}</div>
                                    <div class="text-lg font-bold text-indigo-600">{{ number_format($topsisData['weights'][$criterion->id] ?? 0, 4) }}</div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm border-collapse">
                                <thead>
                                    <tr class="bg-slate-100">
                                        <th class="border border-slate-300 px-4 py-2 text-left text-slate-700 font-semibold">No</th>
                                        <th class="border border-slate-300 px-4 py-2 text-left text-slate-700 font-semibold">CS / Alternatif</th>
                                        @foreach($criteria as $criterion)
                                        <th class="border border-slate-300 px-4 py-2 text-center text-slate-700 font-semibold">{{ $criterion->code }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($decision->details as $index => $detail)
                                    <tr class="hover:bg-slate-50">
                                        <td class="border border-slate-300 px-4 py-2 text-center">{{ $index + 1 }}</td>
                                        <td class="border border-slate-300 px-4 py-2 font-medium">{{ $detail->alternative->name }}</td>
                                        @foreach($criteria as $criterion)
                                        <td class="border border-slate-300 px-4 py-2 text-center font-mono text-xs">{{ number_format($topsisData['weightedMatrix'][$detail->alternative_id][$criterion->id] ?? 0, 6) }}</td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </details>

                <!-- 4. Solusi Ideal -->
                <details class="group">
                    <summary class="flex items-center justify-between cursor-pointer list-none p-4 bg-slate-50 hover:bg-slate-100 rounded-xl transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-8 h-8 bg-indigo-100 text-indigo-600 rounded-lg font-bold text-sm">4</div>
                            <div>
                                <h4 class="font-semibold text-slate-900">Solusi Ideal Positif (A+) dan Negatif (A-)</h4>
                                <p class="text-xs text-slate-600">Nilai terbaik dan terburuk untuk setiap kriteria</p>
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
                                    <th class="border border-slate-300 px-4 py-2 text-left text-slate-700 font-semibold">Solusi</th>
                                    @foreach($criteria as $criterion)
                                    <th class="border border-slate-300 px-4 py-2 text-center text-slate-700 font-semibold">
                                        {{ $criterion->code }}<br>
                                        <span class="text-xs font-normal">({{ $criterion->type }})</span>
                                    </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="bg-green-50">
                                    <td class="border border-slate-300 px-4 py-2 font-semibold text-green-700">A+ (Ideal Positif)</td>
                                    @foreach($criteria as $criterion)
                                    <td class="border border-slate-300 px-4 py-2 text-center font-mono text-xs bg-green-100">{{ number_format($topsisData['idealPositive'][$criterion->id] ?? 0, 6) }}</td>
                                    @endforeach
                                </tr>
                                <tr class="bg-red-50">
                                    <td class="border border-slate-300 px-4 py-2 font-semibold text-red-700">A- (Ideal Negatif)</td>
                                    @foreach($criteria as $criterion)
                                    <td class="border border-slate-300 px-4 py-2 text-center font-mono text-xs bg-red-100">{{ number_format($topsisData['idealNegative'][$criterion->id] ?? 0, 6) }}</td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </details>

                <!-- 5. Jarak dan Preferensi -->
                <details class="group">
                    <summary class="flex items-center justify-between cursor-pointer list-none p-4 bg-slate-50 hover:bg-slate-100 rounded-xl transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-8 h-8 bg-indigo-100 text-indigo-600 rounded-lg font-bold text-sm">5</div>
                            <div>
                                <h4 class="font-semibold text-slate-900">Jarak dan Preference Value</h4>
                                <p class="text-xs text-slate-600">Perhitungan D+, D-, dan C* untuk ranking final</p>
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
                                    <th class="border border-slate-300 px-4 py-2 text-center text-slate-700 font-semibold">Rank</th>
                                    <th class="border border-slate-300 px-4 py-2 text-left text-slate-700 font-semibold">CS / Alternatif</th>
                                    <th class="border border-slate-300 px-4 py-2 text-center text-slate-700 font-semibold">D+ (Ideal)</th>
                                    <th class="border border-slate-300 px-4 py-2 text-center text-slate-700 font-semibold">D- (Anti-Ideal)</th>
                                    <th class="border border-slate-300 px-4 py-2 text-center text-slate-700 font-semibold">C* (Preference)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($decision->details as $detail)
                                <tr class="hover:bg-slate-50 {{ $detail->rank == 1 ? 'bg-emerald-50' : '' }}">
                                    <td class="border border-slate-300 px-4 py-2 text-center font-bold {{ $detail->rank == 1 ? 'text-emerald-600' : '' }}">{{ $detail->rank }}</td>
                                    <td class="border border-slate-300 px-4 py-2 font-medium">{{ $detail->alternative->name }}</td>
                                    <td class="border border-slate-300 px-4 py-2 text-center font-mono text-xs">{{ number_format($detail->d_plus, 6) }}</td>
                                    <td class="border border-slate-300 px-4 py-2 text-center font-mono text-xs">{{ number_format($detail->d_minus, 6) }}</td>
                                    <td class="border border-slate-300 px-4 py-2 text-center font-mono font-bold {{ $detail->rank == 1 ? 'text-emerald-600' : '' }}">{{ number_format($detail->preference_value, 6) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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
                    <span class="font-semibold text-slate-900">Preference Value (C*):</span> Semakin tinggi nilai C*, semakin baik performanya. Nilai berkisar antara 0-1.
                </div>
            </div>
            <div class="flex items-start">
                <div class="flex-shrink-0 flex items-center justify-center w-8 h-8 bg-indigo-100 rounded-lg mr-3">
                    <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="text-sm text-slate-700">
                    <span class="font-semibold text-slate-900">D+ (Distance to Ideal):</span> Jarak ke solusi ideal positif. Semakin kecil semakin baik.
                </div>
            </div>
            <div class="flex items-start">
                <div class="flex-shrink-0 flex items-center justify-center w-8 h-8 bg-indigo-100 rounded-lg mr-3">
                    <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="text-sm text-slate-700">
                    <span class="font-semibold text-slate-900">D- (Distance to Anti-Ideal):</span> Jarak ke solusi ideal negatif. Semakin besar semakin baik.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
