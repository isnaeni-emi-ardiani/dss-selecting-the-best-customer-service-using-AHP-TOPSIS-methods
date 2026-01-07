@extends('layouts.app')

@section('title', 'Perbandingan Berpasangan AHP')
@section('header', 'Perbandingan Berpasangan AHP')

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
            <h2 class="text-lg font-semibold text-slate-900">Session AHP Baru</h2>
            <p class="mt-1 text-sm text-slate-600">Bandingkan setiap pasang kriteria menggunakan skala Saaty (1-9)</p>
        </div>
    </div>

    <!-- Saaty Scale Reference -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
            <h3 class="text-base font-bold text-slate-900">Panduan Skala Saaty</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                <div class="flex flex-col items-center gap-2 px-3 py-3 bg-slate-50 rounded-lg border border-slate-200">
                    <span class="inline-flex items-center justify-center w-8 h-8 bg-indigo-600 text-white font-bold rounded-lg text-sm">1</span>
                    <span class="text-xs text-center text-slate-700 font-medium leading-tight">Sama penting</span>
                </div>
                <div class="flex flex-col items-center gap-2 px-3 py-3 bg-slate-50 rounded-lg border border-slate-200">
                    <span class="inline-flex items-center justify-center w-8 h-8 bg-indigo-600 text-white font-bold rounded-lg text-sm">3</span>
                    <span class="text-xs text-center text-slate-700 font-medium leading-tight">Sedikit lebih penting</span>
                </div>
                <div class="flex flex-col items-center gap-2 px-3 py-3 bg-slate-50 rounded-lg border border-slate-200">
                    <span class="inline-flex items-center justify-center w-8 h-8 bg-indigo-600 text-white font-bold rounded-lg text-sm">5</span>
                    <span class="text-xs text-center text-slate-700 font-medium leading-tight">Lebih penting</span>
                </div>
                <div class="flex flex-col items-center gap-2 px-3 py-3 bg-slate-50 rounded-lg border border-slate-200">
                    <span class="inline-flex items-center justify-center w-8 h-8 bg-indigo-600 text-white font-bold rounded-lg text-sm">7</span>
                    <span class="text-xs text-center text-slate-700 font-medium leading-tight">Sangat lebih penting</span>
                </div>
                <div class="flex flex-col items-center gap-2 px-3 py-3 bg-slate-50 rounded-lg border border-slate-200">
                    <span class="inline-flex items-center justify-center w-8 h-8 bg-indigo-600 text-white font-bold rounded-lg text-sm">9</span>
                    <span class="text-xs text-center text-slate-700 font-medium leading-tight">Mutlak lebih penting</span>
                </div>
                <div class="flex flex-col items-center gap-2 px-3 py-3 bg-slate-50 rounded-lg border border-slate-200">
                    <span class="inline-flex items-center justify-center w-8 h-8 bg-slate-600 text-white font-bold rounded-lg text-xs">2,4,6,8</span>
                    <span class="text-xs text-center text-slate-700 font-medium leading-tight">Nilai antara</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
            <h3 class="text-base font-bold text-slate-900">Form Perbandingan</h3>
        </div>

        <form action="{{ route('ahp.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <!-- Session Name -->
            <div>
                <label for="name" class="block text-sm font-semibold text-slate-900 mb-2">
                    Nama Sesi <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    value="{{ old('name', 'AHP Session - ' . date('d M Y H:i')) }}"
                    class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                    required
                >
                <p class="mt-1 text-xs text-slate-500">Nama untuk mengidentifikasi sesi perbandingan ini</p>
            </div>

            <!-- Comparisons -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-sm font-semibold text-slate-900">Perbandingan Kriteria</h4>
                    <span class="inline-flex items-center px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-medium">
                        {{ $criteria->count() * ($criteria->count() - 1) / 2 }} Perbandingan
                    </span>
                </div>
                <div class="space-y-3">
                    @php
                        $count = 0;
                    @endphp
                    @foreach($criteria as $i => $c1)
                        @foreach($criteria as $j => $c2)
                            @if($i < $j)
                                @php $count++; @endphp
                                <div class="border border-slate-200 rounded-xl p-5 bg-slate-50/50">
                                    <!-- Header -->
                                    <div class="flex items-center gap-2 mb-4">
                                        <span class="inline-flex items-center justify-center w-7 h-7 bg-indigo-600 text-white font-bold rounded-lg text-xs">
                                            {{ $count }}
                                        </span>
                                        <span class="text-xs text-slate-600 font-medium">Perbandingan {{ $count }} dari {{ $criteria->count() * ($criteria->count() - 1) / 2 }}</span>
                                    </div>

                                    <!-- Criteria Comparison Row -->
                                    <div class="flex items-center gap-3 mb-4">
                                        <!-- First Criterion -->
                                        <div class="flex-1 flex items-center justify-end gap-2 px-4 py-3 bg-white rounded-lg border border-indigo-200">
                                            <span class="text-sm font-medium text-slate-700">{{ $c1->name }}</span>
                                            <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded text-xs font-bold">{{ $c1->code }}</span>
                                        </div>

                                        <!-- VS Divider -->
                                        <div class="flex-shrink-0">
                                            <span class="inline-flex items-center justify-center w-10 h-10 bg-slate-200 text-slate-600 rounded-lg text-xs font-bold">VS</span>
                                        </div>

                                        <!-- Second Criterion -->
                                        <div class="flex-1 flex items-center gap-2 px-4 py-3 bg-white rounded-lg border border-purple-200">
                                            <span class="px-2 py-0.5 bg-purple-100 text-purple-700 rounded text-xs font-bold">{{ $c2->code }}</span>
                                            <span class="text-sm font-medium text-slate-700">{{ $c2->name }}</span>
                                        </div>
                                    </div>

                                    <!-- Comparison Dropdown -->
                                    <div class="relative">
                                        <label class="block text-xs font-medium text-slate-700 mb-2">
                                            Pilih tingkat kepentingan:
                                        </label>
                                        <select
                                            name="comparisons[{{ $c1->id }}-{{ $c2->id }}]"
                                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all appearance-none bg-white text-sm"
                                            required
                                        >
                                            <option value="9">9 - {{ $c1->code }} Mutlak lebih penting</option>
                                            <option value="8">8 - {{ $c1->code }} Antara mutlak & sangat</option>
                                            <option value="7">7 - {{ $c1->code }} Sangat lebih penting</option>
                                            <option value="6">6 - {{ $c1->code }} Antara sangat & lebih</option>
                                            <option value="5">5 - {{ $c1->code }} Lebih penting</option>
                                            <option value="4">4 - {{ $c1->code }} Antara lebih & sedikit</option>
                                            <option value="3">3 - {{ $c1->code }} Sedikit lebih penting</option>
                                            <option value="2">2 - {{ $c1->code }} Antara sedikit & sama</option>
                                            <option value="1" selected>1 - Sama penting</option>
                                            <option value="0.5">2 - {{ $c2->code }} Antara sedikit & sama</option>
                                            <option value="0.333">3 - {{ $c2->code }} Sedikit lebih penting</option>
                                            <option value="0.25">4 - {{ $c2->code }} Antara lebih & sedikit</option>
                                            <option value="0.2">5 - {{ $c2->code }} Lebih penting</option>
                                            <option value="0.167">6 - {{ $c2->code }} Antara sangat & lebih</option>
                                            <option value="0.143">7 - {{ $c2->code }} Sangat lebih penting</option>
                                            <option value="0.125">8 - {{ $c2->code }} Antara mutlak & sangat</option>
                                            <option value="0.111">9 - {{ $c2->code }} Mutlak lebih penting</option>
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-500" style="top: 24px;">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @endforeach
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-200">
                <a href="{{ route('ahp.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium rounded-xl bg-white text-slate-700 border border-slate-300 hover:bg-slate-50 transition-all duration-200">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm hover:shadow-md transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    Hitung AHP
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
