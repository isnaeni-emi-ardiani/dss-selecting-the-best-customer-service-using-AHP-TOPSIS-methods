@extends('layouts.app')

@section('title', 'Data Customer Service')
@section('header', 'Data Customer Service')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">Daftar Customer Service</h2>
            <p class="mt-1 text-sm text-slate-600">Kelola data CS yang akan dievaluasi</p>
        </div>
        <a href="{{ route('alternatives.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm hover:shadow-md transition-all duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah CS
        </a>
    </div>

    @if($alternatives->count() > 0)
    <!-- Bulk Action Bar (Hidden by default) - Sticky bottom with prominent styling -->
    <div id="bulkActionBar" class="hidden fixed bottom-6 left-0 right-0 z-50 flex justify-center pointer-events-none">
        <div class="bg-slate-900 text-white rounded-2xl shadow-2xl px-6 py-4 flex items-center gap-6 pointer-events-auto animate-slide-up">
            <!-- Selection count with icon -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-500 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <div>
                    <span class="text-lg font-bold"><span id="selectedCount">0</span></span>
                    <span class="text-slate-300 ml-1">CS dipilih</span>
                </div>
            </div>

            <!-- Divider -->
            <div class="w-px h-10 bg-slate-700"></div>

            <!-- Action buttons -->
            <div class="flex items-center gap-3">
                <button type="button" onclick="bulkUpdateStatus(true)" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold rounded-xl bg-emerald-500 text-white hover:bg-emerald-400 transition-all duration-200 shadow-lg shadow-emerald-500/25">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Aktifkan
                </button>
                <button type="button" onclick="bulkUpdateStatus(false)" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold rounded-xl bg-slate-600 text-white hover:bg-slate-500 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                    Nonaktifkan
                </button>
            </div>

            <!-- Divider -->
            <div class="w-px h-10 bg-slate-700"></div>

            <!-- Cancel button -->
            <button type="button" onclick="clearSelection()" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-slate-700 text-slate-300 hover:bg-slate-600 hover:text-white transition-all duration-200" title="Batalkan pilihan">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Modern Table Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-4 text-center w-12">
                            <input type="checkbox" id="selectAll" class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500 cursor-pointer" onchange="toggleSelectAll(this)">
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider w-24">Kode</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Nama CS</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Deskripsi</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-slate-700 uppercase tracking-wider w-32">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider w-48">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($alternatives as $alternative)
                    <tr class="hover:bg-slate-50 transition-colors" data-id="{{ $alternative->id }}">
                        <!-- Checkbox -->
                        <td class="px-4 py-4 text-center">
                            <input type="checkbox" class="row-checkbox w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500 cursor-pointer" value="{{ $alternative->id }}" onchange="updateSelection()">
                        </td>

                        <!-- Code Badge -->
                        <td class="px-6 py-4">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-indigo-100 text-indigo-700">
                                <span class="text-base font-bold">{{ $alternative->code }}</span>
                            </div>
                        </td>

                        <!-- Name -->
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-slate-900">{{ $alternative->name }}</div>
                        </td>

                        <!-- Description -->
                        <td class="px-6 py-4">
                            @if($alternative->description)
                            <p class="text-sm text-slate-600 line-clamp-2 max-w-lg">{{ $alternative->description }}</p>
                            @else
                            <p class="text-sm text-slate-400 italic">Tidak ada deskripsi</p>
                            @endif
                        </td>

                        <!-- Status Badge -->
                        <td class="px-6 py-4 text-center">
                            @if($alternative->is_active)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200 whitespace-nowrap">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Aktif
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-50 text-slate-700 border border-slate-200 whitespace-nowrap">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                Non-aktif
                            </span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('alternatives.edit', $alternative) }}" class="inline-flex items-center justify-center px-3 py-2 text-xs font-medium rounded-xl bg-white text-slate-700 border border-slate-300 hover:bg-slate-50 transition-all duration-200">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Edit
                                </a>
                                <form action="{{ route('alternatives.destroy', $alternative) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus CS ini?')">
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
                <span>Total: <span class="font-semibold text-slate-900">{{ $alternatives->count() }}</span> customer service</span>
                <span>Aktif: <span class="font-semibold text-emerald-600">{{ $alternatives->where('is_active', true)->count() }}</span> CS</span>
            </div>
        </div>
    </div>
    @else
    <!-- Empty State -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 text-center py-12">
        <svg class="mx-auto h-16 w-16 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <h3 class="mt-4 text-base font-semibold text-slate-900">Belum ada data CS</h3>
        <p class="mt-2 text-sm text-slate-600">Mulai dengan menambahkan customer service pertama</p>
        <div class="mt-6">
            <a href="{{ route('alternatives.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm hover:shadow-md transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah CS Pertama
            </a>
        </div>
    </div>
    @endif
</div>

<!-- Toast Notification -->
<div id="toast" class="fixed bottom-4 right-4 z-50 hidden">
    <div class="bg-white rounded-xl shadow-lg border border-slate-200 p-4 max-w-sm">
        <div class="flex items-center gap-3">
            <div id="toastIcon" class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center">
            </div>
            <p id="toastMessage" class="text-sm font-medium text-slate-900"></p>
        </div>
    </div>
</div>

<script>
    let selectedIds = [];

    function updateSelection() {
        const checkboxes = document.querySelectorAll('.row-checkbox:checked');
        selectedIds = Array.from(checkboxes).map(cb => parseInt(cb.value));

        const selectAllCheckbox = document.getElementById('selectAll');
        const allCheckboxes = document.querySelectorAll('.row-checkbox');
        const bulkActionBar = document.getElementById('bulkActionBar');
        const selectedCountEl = document.getElementById('selectedCount');

        if (selectedIds.length === 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        } else if (selectedIds.length === allCheckboxes.length) {
            selectAllCheckbox.checked = true;
            selectAllCheckbox.indeterminate = false;
        } else {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = true;
        }

        if (selectedIds.length > 0) {
            bulkActionBar.classList.remove('hidden');
            selectedCountEl.textContent = selectedIds.length;
        } else {
            bulkActionBar.classList.add('hidden');
        }
    }

    function toggleSelectAll(checkbox) {
        const allCheckboxes = document.querySelectorAll('.row-checkbox');
        allCheckboxes.forEach(cb => cb.checked = checkbox.checked);
        updateSelection();
    }

    function clearSelection() {
        const allCheckboxes = document.querySelectorAll('.row-checkbox');
        allCheckboxes.forEach(cb => cb.checked = false);
        document.getElementById('selectAll').checked = false;
        updateSelection();
    }

    function bulkUpdateStatus(status) {
        if (selectedIds.length === 0) {
            showToast('Pilih minimal satu CS', 'error');
            return;
        }

        const statusText = status ? 'mengaktifkan' : 'menonaktifkan';
        if (!confirm(`Yakin ingin ${statusText} ${selectedIds.length} CS yang dipilih?`)) {
            return;
        }

        fetch('{{ route("alternatives.bulk-status") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                ids: selectedIds,
                status: status
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(data.message || 'Terjadi kesalahan', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Terjadi kesalahan saat memproses permintaan', 'error');
        });
    }

    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        const toastIcon = document.getElementById('toastIcon');
        const toastMessage = document.getElementById('toastMessage');

        toastMessage.textContent = message;

        if (type === 'success') {
            toastIcon.className = 'flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center bg-emerald-100';
            toastIcon.innerHTML = '<svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>';
        } else {
            toastIcon.className = 'flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center bg-red-100';
            toastIcon.innerHTML = '<svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>';
        }

        toast.classList.remove('hidden');
        setTimeout(() => toast.classList.add('hidden'), 3000);
    }
</script>
@endsection
