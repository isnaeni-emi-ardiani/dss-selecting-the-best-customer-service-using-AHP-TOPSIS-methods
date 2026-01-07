@extends('layouts.app')

@section('title', 'Edit Customer Service')
@section('header', 'Edit Customer Service')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('alternatives.index') }}" class="inline-flex items-center text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors group">
                    <svg class="w-4 h-4 mr-1.5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali
                </a>
            </div>
            <h2 class="text-lg font-semibold text-slate-900">Edit Customer Service</h2>
            <p class="mt-1 text-sm text-slate-600">Update informasi data customer service</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden max-w-3xl">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
            <h3 class="text-base font-bold text-slate-900">Form Edit CS</h3>
        </div>

        <form action="{{ route('alternatives.update', $alternative) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Code (Read Only) -->
            <div>
                <label for="code" class="block text-sm font-medium text-slate-700 mb-2">
                    Kode CS
                </label>
                <input
                    type="text"
                    id="code"
                    value="{{ $alternative->code }}"
                    class="w-full px-4 py-3 border border-slate-300 rounded-xl bg-slate-50 text-slate-500 cursor-not-allowed"
                    readonly
                    disabled
                >
                <p class="mt-1.5 text-xs text-slate-500">Kode CS tidak dapat diubah</p>
            </div>

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-2">
                    Nama CS <span class="text-red-600">*</span>
                </label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    value="{{ old('name', $alternative->name) }}"
                    class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('name') border-red-500 @enderror"
                    placeholder="Masukkan nama lengkap CS"
                    required
                >
                @error('name')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-slate-700 mb-2">
                    Deskripsi
                </label>
                <textarea
                    name="description"
                    id="description"
                    rows="4"
                    class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('description') border-red-500 @enderror"
                    placeholder="Informasi tambahan tentang CS (opsional)"
                >{{ old('description', $alternative->description) }}</textarea>
                @error('description')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Is Active -->
            <div>
                <!-- Hidden input untuk memastikan is_active=0 terkirim saat checkbox tidak dicentang -->
                <input type="hidden" name="is_active" value="0">
                <label class="flex items-center cursor-pointer">
                    <input
                        type="checkbox"
                        name="is_active"
                        id="is_active"
                        value="1"
                        {{ old('is_active', $alternative->is_active) ? 'checked' : '' }}
                        class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-2 focus:ring-indigo-500"
                    >
                    <span class="ml-2 text-sm font-medium text-slate-700">CS Aktif</span>
                </label>
                <p class="mt-1.5 ml-6 text-xs text-slate-500">CS aktif akan disertakan dalam proses evaluasi</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('alternatives.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium rounded-xl bg-white text-slate-700 border border-slate-300 hover:bg-slate-50 transition-all duration-200">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm hover:shadow-md transition-all duration-200">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Update CS
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
