@extends('layouts.app')

@section('title', 'Riwayat Session AHP')
@section('header', 'Riwayat Session AHP')

@section('content')
<div class="space-y-6">
    <!-- Elegant Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Session AHP</h2>
            <p class="mt-1.5 text-sm text-slate-600">Perhitungan bobot kriteria menggunakan metode Analytical Hierarchy Process</p>
        </div>
        <a href="{{ route('ahp.create') }}" class="inline-flex items-center justify-center px-5 py-3 text-sm font-semibold rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow-md hover:shadow-lg transition-all duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Session Baru
        </a>
    </div>

    @if($sessions->count() > 0)
    <!-- Elegant Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($sessions as $session)
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 hover:shadow-lg hover:border-indigo-300 transition-all duration-300 overflow-hidden group">
            <!-- Card Header with Gradient -->
            <div class="bg-gradient-to-br from-indigo-50 via-purple-50 to-white p-6 border-b border-slate-100">
                <div class="flex items-start justify-between mb-4">
                    <!-- Matrix Size Badge -->
                    <div class="relative">
                        <div class="absolute inset-0 bg-indigo-600 rounded-xl blur opacity-20 group-hover:opacity-30 transition-opacity"></div>
                        <div class="relative w-16 h-16 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                            <span class="text-2xl font-bold text-white">{{ $session->matrix_size }}</span>
                        </div>
                    </div>

                    <!-- Status Indicators -->
                    <div class="flex flex-col gap-2">
                        @if($session->is_active)
                        <div class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-gradient-to-r from-indigo-100 to-purple-100 text-indigo-700 border border-indigo-300 shadow-sm">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            AKTIF
                        </div>
                        @endif
                        @if($session->is_consistent)
                        <div class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200 shadow-sm">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Valid
                        </div>
                        @else
                        <div class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-100 text-red-700 border border-red-200 shadow-sm">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            Invalid
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Session Name -->
                <h3 class="text-lg font-bold text-slate-900 mb-2 line-clamp-2">{{ $session->name }}</h3>
                <p class="text-xs text-slate-500">{{ $session->comparisons->count() }} perbandingan berpasangan</p>
            </div>

            <!-- Card Body -->
            <div class="p-6 space-y-4">
                <!-- Matrix Size Info -->
                <div class="flex items-center justify-between py-3 px-4 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-sm font-medium text-slate-700">Matrix Size</span>
                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-indigo-100 text-indigo-700 border border-indigo-200">
                        {{ $session->matrix_size }} × {{ $session->matrix_size }}
                    </span>
                </div>

                <!-- Consistency Ratio -->
                <div class="flex items-center justify-between py-3 px-4 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-sm font-medium text-slate-700">Consistency Ratio</span>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-bold {{ $session->consistency_ratio < 0.1 ? 'text-emerald-700' : 'text-red-700' }}">
                            {{ number_format($session->consistency_ratio, 4) }}
                        </span>
                        @if($session->consistency_ratio < 0.1)
                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                        @else
                        <div class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></div>
                        @endif
                    </div>
                </div>

                <!-- Timestamp -->
                <div class="flex items-center gap-2 text-xs text-slate-500 pt-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ $session->created_at->format('d M Y, H:i') }} WIB
                </div>
            </div>

            <!-- Card Footer Actions -->
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between gap-2">
                <div class="flex gap-2">
                    @if($session->is_active)
                    <form action="{{ route('ahp.deactivate', $session) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold rounded-xl bg-slate-100 text-slate-600 border border-slate-300 hover:bg-slate-200 transition-all duration-200">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Nonaktifkan
                        </button>
                    </form>
                    @else
                        @php
                            $canActivate = $session->canBeActivated();
                            $currentCount = \App\Models\Criterion::count();
                            $sessionCount = count($session->criteria_ids ?? []);
                        @endphp
                        @if($canActivate)
                        <form action="{{ route('ahp.activate', $session) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold rounded-xl bg-indigo-600 text-white border border-indigo-600 hover:bg-indigo-700 transition-all duration-200">
                                <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Aktifkan
                            </button>
                        </form>
                        @else
                        <button disabled class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold rounded-xl bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed" title="Jumlah kriteria tidak cocok (Session: {{ $sessionCount }}, Saat ini: {{ $currentCount }})">
                            <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/>
                            </svg>
                            Tidak Dapat Diaktifkan
                        </button>
                        @endif
                    @endif
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('ahp.show', $session) }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold rounded-xl bg-white text-slate-700 border border-slate-300 hover:bg-slate-50 hover:border-indigo-300 hover:text-indigo-600 transition-all duration-200">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Detail
                    </a>
                    <form action="{{ route('ahp.destroy', $session) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus session ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold rounded-xl bg-white text-red-600 border border-red-300 hover:bg-red-50 transition-all duration-200">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Summary Footer -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="text-sm text-slate-600">
                    Total: <span class="font-bold text-slate-900">{{ $sessions->count() }}</span> session
                </div>
                <div class="w-px h-4 bg-slate-300"></div>
                <div class="text-sm text-slate-600">
                    Konsisten: <span class="font-bold text-emerald-600">{{ $sessions->where('is_consistent', true)->count() }}</span>
                </div>
                <div class="w-px h-4 bg-slate-300"></div>
                <div class="text-sm text-slate-600">
                    Inkonsisten: <span class="font-bold text-red-600">{{ $sessions->where('is_consistent', false)->count() }}</span>
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- Elegant Empty State -->
    <div class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
        <div class="bg-gradient-to-br from-indigo-50 via-purple-50 to-white p-12 text-center">
            <div class="max-w-md mx-auto">
                <!-- Icon -->
                <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 mb-6">
                    <svg class="w-12 h-12 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>

                <!-- Text -->
                <h3 class="text-xl font-bold text-slate-900 mb-3">Belum Ada Session AHP</h3>
                <p class="text-sm text-slate-600 mb-8 leading-relaxed">
                    Mulai dengan membuat session baru untuk melakukan perbandingan berpasangan kriteria menggunakan metode AHP
                </p>

                <!-- Action -->
                <a href="{{ route('ahp.create') }}" class="inline-flex items-center justify-center px-6 py-3.5 text-sm font-semibold rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white hover:from-indigo-700 hover:to-purple-700 shadow-lg hover:shadow-xl transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Buat Session AHP Pertama
                </a>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
