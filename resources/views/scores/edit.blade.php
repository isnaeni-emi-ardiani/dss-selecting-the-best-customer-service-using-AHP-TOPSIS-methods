@extends('layouts.app')

@section('title', 'Input Penilaian CS')
@section('header', 'Input Penilaian CS')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('scores.index') }}" class="inline-flex items-center text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors group">
                    <svg class="w-4 h-4 mr-1.5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali
                </a>
            </div>
            <h2 class="text-lg font-semibold text-slate-900">Input Penilaian CS</h2>
            <p class="mt-1 text-sm text-slate-600">Berikan penilaian untuk setiap CS berdasarkan kriteria</p>
        </div>
    </div>

    @if($alternatives->count() > 0 && $criteria->count() > 0)
    <form action="{{ route('scores.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Instructions Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                <h3 class="text-sm font-bold text-slate-900">Panduan Penilaian</h3>
                <p class="text-sm text-slate-600 mt-1">Gunakan skala 1-5 untuk setiap kriteria</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                    <div class="flex items-center gap-2.5 p-3 rounded-lg bg-slate-50 border border-slate-200">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg font-semibold text-sm bg-red-100 text-red-700">1</span>
                        <span class="text-xs font-medium text-slate-700">Sangat Buruk</span>
                    </div>
                    <div class="flex items-center gap-2.5 p-3 rounded-lg bg-slate-50 border border-slate-200">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg font-semibold text-sm bg-amber-100 text-amber-700">2</span>
                        <span class="text-xs font-medium text-slate-700">Buruk</span>
                    </div>
                    <div class="flex items-center gap-2.5 p-3 rounded-lg bg-slate-50 border border-slate-200">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg font-semibold text-sm bg-blue-100 text-blue-700">3</span>
                        <span class="text-xs font-medium text-slate-700">Cukup</span>
                    </div>
                    <div class="flex items-center gap-2.5 p-3 rounded-lg bg-slate-50 border border-slate-200">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg font-semibold text-sm bg-emerald-100 text-emerald-700">4</span>
                        <span class="text-xs font-medium text-slate-700">Baik</span>
                    </div>
                    <div class="flex items-center gap-2.5 p-3 rounded-lg bg-slate-50 border border-slate-200">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg font-semibold text-sm bg-teal-100 text-teal-700">5</span>
                        <span class="text-xs font-medium text-slate-700">Sangat Baik</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Score Input Matrix -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Form Penilaian</h3>
                        <p class="text-sm text-slate-600 mt-1">Cell dengan latar hijau sudah berisi penilaian. Anda bisa mengubah nilai yang sudah ada atau fokus mengisi yang masih kosong.</p>
                    </div>
                    @if(!$hasEmptyScore)
                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg bg-emerald-100 text-emerald-700 text-xs font-medium">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Semua Terisi
                        </span>
                    @endif
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr>
                            <th class="sticky left-0 z-10 bg-slate-100 px-6 py-4 text-left font-semibold text-slate-700 border-r border-slate-300">
                                Customer Service
                            </th>
                            @foreach($criteria as $criterion)
                                <th class="bg-indigo-50 px-5 py-4 text-center border-l border-slate-200">
                                    <div class="flex flex-col items-center gap-1.5">
                                        <div class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-white border border-indigo-200">
                                            <span class="text-sm font-bold text-indigo-700">{{ $criterion->code }}</span>
                                        </div>
                                        <div class="text-xs font-medium text-slate-700 text-center px-2">{{ $criterion->name }}</div>
                                        <div class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium {{ $criterion->type === 'benefit' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                            {{ $criterion->type === 'benefit' ? '↑ Benefit' : '↓ Cost' }}
                                        </div>
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($alternatives as $alternative)
                            <tr class="border-t border-slate-200 hover:bg-slate-50 transition-colors">
                                <td class="sticky left-0 z-10 bg-white px-6 py-4 border-r border-slate-300">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center shadow-sm">
                                            <span class="text-white font-bold text-sm">{{ $alternative->code }}</span>
                                        </div>
                                        <span class="font-semibold text-slate-900">{{ $alternative->name }}</span>
                                    </div>
                                </td>
                                @foreach($criteria as $criterion)
                                    @php
                                        $currentScore = $scores[$alternative->id][$criterion->id] ?? null;
                                        $hasScore = $currentScore !== null;
                                    @endphp
                                    <td class="px-5 py-4 text-center border-l border-slate-100 {{ $hasScore ? 'bg-emerald-50' : 'bg-white' }}">
                                        <select
                                            name="scores[{{ $alternative->id }}][{{ $criterion->id }}]"
                                            class="w-full px-3 py-2 text-sm border rounded-lg text-center font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all {{ $hasScore ? 'border-emerald-300 bg-white text-slate-900' : 'border-slate-300' }}"
                                        >
                                            <option value="">-- Pilih --</option>
                                            <option value="1" {{ $currentScore == 1 ? 'selected' : '' }}>1 - Sangat Buruk</option>
                                            <option value="2" {{ $currentScore == 2 ? 'selected' : '' }}>2 - Buruk</option>
                                            <option value="3" {{ $currentScore == 3 ? 'selected' : '' }}>3 - Cukup</option>
                                            <option value="4" {{ $currentScore == 4 ? 'selected' : '' }}>4 - Baik</option>
                                            <option value="5" {{ $currentScore == 5 ? 'selected' : '' }}>5 - Sangat Baik</option>
                                        </select>
                                        @if($hasScore)
                                            <div class="inline-flex items-center mt-2 text-xs text-emerald-700 font-medium">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                Terisi
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('scores.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium rounded-xl bg-white text-slate-700 border border-slate-300 hover:bg-slate-50 transition-all duration-200">
                Batal
            </a>
            <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm hover:shadow-md transition-all duration-200">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan Penilaian
            </button>
        </div>
    </form>

    @else
    <!-- Empty State -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
            <div class="flex items-center justify-center w-16 h-16 bg-amber-100 rounded-2xl mb-4">
                <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-base font-semibold text-slate-900 mb-2">Tidak Dapat Melakukan Penilaian</h3>
            <p class="text-sm text-slate-600 mb-6 max-w-sm">
                @if($alternatives->count() == 0)
                    Anda perlu menambahkan data CS terlebih dahulu
                @elseif($criteria->count() == 0)
                    Anda perlu menambahkan kriteria terlebih dahulu
                @endif
            </p>
            <div class="flex gap-3">
                @if($criteria->count() == 0)
                    <a href="{{ route('criteria.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm hover:shadow-md transition-all duration-200">
                        Tambah Kriteria
                    </a>
                @endif
                @if($alternatives->count() == 0)
                    <a href="{{ route('alternatives.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm hover:shadow-md transition-all duration-200">
                        Tambah CS
                    </a>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
