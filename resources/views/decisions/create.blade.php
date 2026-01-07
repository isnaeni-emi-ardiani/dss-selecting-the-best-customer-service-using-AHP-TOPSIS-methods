@extends('layouts.app')

@section('title', 'Hitung Keputusan Baru')
@section('header', 'Hitung Keputusan Baru')

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
            <h2 class="text-lg font-semibold text-slate-900">Hitung Keputusan Baru</h2>
            <p class="mt-1 text-sm text-slate-600">Hitung ranking CS terbaik menggunakan metode TOPSIS</p>
        </div>
    </div>

    <!-- Info Card -->
    <div class="bg-gradient-to-br from-indigo-50 via-purple-50 to-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
            <h3 class="text-base font-bold text-slate-900">Tentang Perhitungan TOPSIS</h3>
        </div>
        <div class="p-6">
            <p class="text-sm text-slate-700 mb-4">Sistem akan menghitung ranking CS berdasarkan:</p>
            <ul class="space-y-2">
                <li class="flex items-start gap-2 text-sm text-slate-700">
                    <svg class="w-5 h-5 text-indigo-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Bobot kriteria dari perhitungan AHP
                </li>
                <li class="flex items-start gap-2 text-sm text-slate-700">
                    <svg class="w-5 h-5 text-indigo-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Nilai penilaian setiap CS terhadap kriteria
                </li>
                <li class="flex items-start gap-2 text-sm text-slate-700">
                    <svg class="w-5 h-5 text-indigo-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Normalisasi Euclidean dan jarak ke solusi ideal
                </li>
                <li class="flex items-start gap-2 text-sm text-slate-700">
                    <svg class="w-5 h-5 text-indigo-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Preference value (C*) untuk menentukan ranking
                </li>
            </ul>
        </div>
    </div>

    @if($canCalculate)
    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden max-w-3xl">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
            <h3 class="text-base font-bold text-slate-900">Form Keputusan</h3>
        </div>

        <form action="{{ route('decisions.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-semibold text-slate-900 mb-2">
                    Judul Keputusan <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="title"
                    id="title"
                    value="{{ old('title') }}"
                    class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('title') border-red-500 @enderror"
                    placeholder="Contoh: Evaluasi CS Periode Desember 2024"
                    required
                >
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-semibold text-slate-900 mb-2">
                    Deskripsi <span class="text-slate-500 font-normal text-xs">(Opsional)</span>
                </label>
                <textarea
                    name="description"
                    id="description"
                    rows="3"
                    class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all resize-none @error('description') border-red-500 @enderror"
                    placeholder="Informasi tambahan tentang keputusan ini..."
                >{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Info Waktu -->
            <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-indigo-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <h5 class="text-sm font-semibold text-indigo-900">Waktu Keputusan</h5>
                        <p class="text-sm text-indigo-700 mt-1">
                            Sistem secara otomatis mencatat tanggal.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Data Summary -->
            <div class="bg-slate-50 rounded-xl p-5 border border-slate-200">
                <h4 class="text-sm font-semibold text-slate-900 mb-4">Data yang Akan Diproses</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="bg-white rounded-lg p-4 border border-slate-200">
                        <div class="text-2xl font-bold text-indigo-600 mb-1">{{ $criteriaCount }}</div>
                        <div class="text-xs text-slate-600">Kriteria Evaluasi</div>
                    </div>
                    <div class="bg-white rounded-lg p-4 border border-slate-200">
                        <div class="text-2xl font-bold text-emerald-600 mb-1">{{ $alternativesCount }}</div>
                        <div class="text-xs text-slate-600">Customer Service</div>
                    </div>
                    <div class="bg-white rounded-lg p-4 border border-slate-200">
                        <div class="text-2xl font-bold text-purple-600 mb-1">{{ $scoresCount }}</div>
                        <div class="text-xs text-slate-600">Total Penilaian</div>
                    </div>
                </div>
            </div>

            <!-- Warning -->
            <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded-lg">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h5 class="text-sm font-semibold text-amber-900">Perhatian</h5>
                        <p class="text-sm text-amber-700 mt-1">
                            Pastikan bobot kriteria sudah dihitung melalui AHP dan semua CS sudah dinilai sebelum membuat keputusan.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-200">
                <a href="{{ route('decisions.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium rounded-xl bg-white text-slate-700 border border-slate-300 hover:bg-slate-50 transition-all duration-200">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm hover:shadow-md transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    Hitung Keputusan
                </button>
            </div>
        </form>
    </div>

    @else
    <!-- Empty State -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-12 text-center">
        <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <h3 class="mt-4 text-base font-semibold text-slate-900">Belum Bisa Menghitung Keputusan</h3>
        <p class="mt-2 text-sm text-slate-600">Pastikan langkah-langkah berikut sudah selesai:</p>
        <div class="mt-6 space-y-3 text-left max-w-md mx-auto">
            @if($criteriaCount == 0)
                <div class="flex items-center gap-2 text-sm text-slate-600">
                    <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    Tambahkan minimal 1 kriteria
                </div>
            @else
                <div class="flex items-center gap-2 text-sm text-emerald-600">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ $criteriaCount }} kriteria tersedia
                </div>
            @endif

            @if($alternativesCount == 0)
                <div class="flex items-center gap-2 text-sm text-slate-600">
                    <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    Tambahkan minimal 2 data CS
                </div>
            @else
                <div class="flex items-center gap-2 text-sm text-emerald-600">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ $alternativesCount }} CS tersedia
                </div>
            @endif

            @if(!$hasWeights)
                <div class="flex items-center gap-2 text-sm text-slate-600">
                    <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    Hitung bobot kriteria melalui AHP
                </div>
            @else
                <div class="flex items-center gap-2 text-sm text-emerald-600">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Bobot kriteria sudah tersedia
                </div>
            @endif

            @if($scoresCount == 0)
                <div class="flex items-center gap-2 text-sm text-slate-600">
                    <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    Input penilaian CS untuk semua kriteria
                </div>
            @else
                <div class="flex items-center gap-2 text-sm text-emerald-600">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ $scoresCount }} penilaian tersedia
                </div>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
