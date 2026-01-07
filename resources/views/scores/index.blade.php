@extends('layouts.app')

@section('title', 'Matriks Penilaian CS')
@section('header', 'Matriks Penilaian CS')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">Matriks Penilaian Customer Service</h2>
            <p class="mt-1 text-sm text-slate-600">Evaluasi performa CS berdasarkan kriteria yang telah ditentukan</p>
        </div>
        <a href="{{ route('scores.edit') }}" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm hover:shadow-md transition-all duration-200">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Input Penilaian
        </a>
    </div>

    @if($alternatives->count() > 0 && $criteria->count() > 0)
    <!-- Score Matrix Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
            <h3 class="text-base font-bold text-slate-900">Matriks Penilaian</h3>
            <p class="text-sm text-slate-600 mt-1">Nilai setiap CS berdasarkan kriteria yang telah ditentukan (Skala 1-5)</p>
        </div>

        <!-- Matrix Table -->
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
                                    $score = $scores[$alternative->id][$criterion->id] ?? null;
                                @endphp
                                <td class="px-5 py-4 text-center border-l border-slate-100">
                                    @if($score)
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg font-semibold text-sm
                                            @if($score == 5) bg-teal-100 text-teal-700
                                            @elseif($score >= 4) bg-emerald-100 text-emerald-700
                                            @elseif($score >= 3) bg-blue-100 text-blue-700
                                            @elseif($score >= 2) bg-amber-100 text-amber-700
                                            @else bg-red-100 text-red-700
                                            @endif">
                                            {{ $score }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-sm font-medium">-</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Legend Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
            <h3 class="text-sm font-bold text-slate-900">Keterangan Skala Penilaian</h3>
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

    @else
    <!-- Empty State -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
            <div class="flex items-center justify-center w-16 h-16 bg-indigo-100 rounded-2xl mb-4">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
            </div>
            <h3 class="text-base font-semibold text-slate-900 mb-2">Belum Bisa Melakukan Penilaian</h3>
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
