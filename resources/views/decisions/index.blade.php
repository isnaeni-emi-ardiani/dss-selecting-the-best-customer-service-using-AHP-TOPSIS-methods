@extends('layouts.app')

@section('title', 'Riwayat Keputusan')
@section('header', 'Riwayat Keputusan')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">Hasil Keputusan TOPSIS</h2>
            <p class="mt-1 text-sm text-slate-600">Riwayat perhitungan ranking CS terbaik menggunakan metode TOPSIS</p>
        </div>
    </div>

    <!-- Decisions Grid -->
    @if($decisions->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($decisions as $decision)
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-all duration-200">
            <!-- Card Body -->
            <div class="p-6">
                <!-- Header -->
                <div class="flex items-start gap-3 mb-4">
                    <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 bg-indigo-100 rounded-xl">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-base font-semibold text-slate-900 truncate">{{ $decision->title }}</h3>
                        <p class="text-xs text-slate-500 mt-1">ID: #{{ $decision->id }}</p>
                    </div>
                </div>

                <!-- Description -->
                @if($decision->description)
                <p class="text-sm text-slate-600 mb-4 line-clamp-2">{{ $decision->description }}</p>
                @else
                <p class="text-sm text-slate-400 mb-4 italic">Tidak ada deskripsi</p>
                @endif

                <!-- Info -->
                <div class="space-y-3 mb-4">
                    <!-- CS Count -->
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-600">Jumlah CS</span>
                        <span class="inline-flex items-center px-2.5 py-1 bg-indigo-100 text-indigo-700 rounded-lg text-xs font-medium">
                            {{ $decision->details->count() }} CS
                        </span>
                    </div>

                    <!-- Date -->
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-600">Tanggal</span>
                        <span class="text-xs text-slate-600">{{ $decision->decision_date->format('d M Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions Footer -->
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-2">
                <a href="{{ route('decisions.show', $decision) }}" class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium rounded-lg bg-white text-slate-700 border border-slate-300 hover:bg-slate-50 transition-all duration-200">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Detail
                </a>
                <form action="{{ route('decisions.destroy', $decision) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus keputusan ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium rounded-lg bg-white text-red-600 border border-red-300 hover:bg-red-50 transition-all duration-200">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Hapus
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <!-- Empty State -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
            <div class="flex items-center justify-center w-16 h-16 bg-slate-100 rounded-2xl mb-4">
                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
            </div>
            <h3 class="text-base font-semibold text-slate-900 mb-2">Belum ada keputusan</h3>
            <p class="text-sm text-slate-600 mb-6 max-w-sm">Buat keputusan baru untuk mendapatkan ranking CS terbaik</p>
            <a href="{{ route('decisions.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm hover:shadow-md transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Buat Keputusan Pertama
            </a>
        </div>
    </div>
    @endif
</div>
@endsection
