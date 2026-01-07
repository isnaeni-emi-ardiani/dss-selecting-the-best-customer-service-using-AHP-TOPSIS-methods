@extends('layouts.app')

@section('title', 'Manajemen Kriteria')
@section('header', 'Manajemen Kriteria')

@section('content')
<div class="space-y-6">
    <!-- Active Session Info Banner -->
    @if($activeSession)
    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 rounded-xl p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-emerald-900">Session AHP Aktif: {{ $activeSession->name }}</h3>
                    <p class="text-xs text-emerald-700 mt-0.5">Bobot kriteria diambil dari session ini untuk perhitungan TOPSIS</p>
                </div>
            </div>
            <a href="{{ route('ahp.index') }}" class="text-sm font-medium text-emerald-700 hover:text-emerald-800 hover:underline">
                Kelola Session →
            </a>
        </div>
    </div>
    @else
    <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-xl p-4">
        <div class="flex items-center gap-3">
            <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-semibold text-amber-900">Belum Ada Session AHP Aktif</h3>
                <p class="text-xs text-amber-700 mt-0.5">Bobot kriteria belum tersedia. Silakan buat dan aktifkan session AHP terlebih dahulu.</p>
            </div>
            <a href="{{ route('ahp.create') }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg bg-amber-600 text-white hover:bg-amber-700 transition-colors">
                Buat Session AHP
            </a>
        </div>
    </div>
    @endif

    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">Daftar Kriteria Evaluasi</h2>
            <p class="mt-1 text-sm text-slate-600">Kelola kriteria penilaian customer service</p>
        </div>
        <a href="{{ route('criteria.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm hover:shadow-md transition-all duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Kriteria
        </a>
    </div>

    @if($criteria->count() > 0)
    <!-- Modern Table Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider w-24">Kode</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Nama Kriteria</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Deskripsi</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-slate-700 uppercase tracking-wider w-32">Tipe</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider w-48">Bobot</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider w-48">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($criteria as $criterion)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <!-- Code Badge -->
                        <td class="px-6 py-4">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-indigo-100 text-indigo-700">
                                <span class="text-base font-bold">{{ $criterion->code }}</span>
                            </div>
                        </td>

                        <!-- Name -->
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-slate-900">{{ $criterion->name }}</div>
                        </td>

                        <!-- Description -->
                        <td class="px-6 py-4">
                            @if($criterion->description)
                            <p class="text-sm text-slate-600 line-clamp-2 max-w-md">{{ $criterion->description }}</p>
                            @else
                            <p class="text-sm text-slate-400 italic">Tidak ada deskripsi</p>
                            @endif
                        </td>

                        <!-- Type Badge -->
                        <td class="px-6 py-4 text-center">
                            @if($criterion->type === 'benefit')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Benefit
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M14.707 12.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Cost
                            </span>
                            @endif
                        </td>

                        <!-- Weight Progress -->
                        <td class="px-6 py-4">
                            @if($criterion->weight > 0)
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between text-xs">
                                    {{-- <span class="text-slate-600">Weight</span> --}}
                                    <span class="font-semibold text-slate-900">{{ number_format($criterion->weight * 100, 2) }}%</span>
                                </div>
                                {{-- <div class="w-full bg-slate-200 rounded-full h-2">
                                    <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300" style="width: {{ $criterion->weight * 100 }}%"></div>
                                </div> --}}
                            </div>
                            @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-50 text-slate-700 border border-slate-200">
                                Belum dihitung
                            </span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('criteria.edit', $criterion) }}" class="inline-flex items-center justify-center px-3 py-2 text-xs font-medium rounded-xl bg-white text-slate-700 border border-slate-300 hover:bg-slate-50 transition-all duration-200">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Edit
                                </a>
                                <form action="{{ route('criteria.destroy', $criterion) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus kriteria ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center px-3 py-2 text-xs font-medium rounded-xl bg-white text-red-600 border border-red-300 hover:bg-red-50 transition-all duration-200">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Table Footer Summary -->
        <div class="px-6 py-3 bg-slate-50 border-t border-slate-200">
            <div class="flex items-center justify-between text-sm text-slate-600">
                <span>Total: <span class="font-semibold text-slate-900">{{ $criteria->count() }}</span> kriteria</span>
                @php
                    $totalWeight = $criteria->sum('weight');
                @endphp
                @if($totalWeight > 0)
                <span>Total Bobot: <span class="font-semibold {{ abs($totalWeight - 1.0) < 0.01 ? 'text-emerald-600' : 'text-red-600' }}">{{ number_format($totalWeight * 100, 2) }}%</span></span>
                @endif
            </div>
        </div>
    </div>
    @else
    <!-- Empty State -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 text-center py-12">
        <svg class="mx-auto h-16 w-16 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <h3 class="mt-4 text-base font-semibold text-slate-900">Belum ada kriteria</h3>
        <p class="mt-2 text-sm text-slate-600">Mulai dengan menambahkan kriteria evaluasi pertama</p>
        <div class="mt-6">
            <a href="{{ route('criteria.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm hover:shadow-md transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Kriteria Pertama
            </a>
        </div>
    </div>
    @endif
</div>
@endsection
