<?php

namespace App\Http\Controllers;

use App\Models\Alternative;
use Illuminate\Http\Request;

class AlternativeController extends Controller
{
    public function index()
    {
        $alternatives = Alternative::all();
        return view('alternatives.index', compact('alternatives'));
    }

    public function create()
    {
        return view('alternatives.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                // Unique tapi abaikan soft deleted records
                \Illuminate\Validation\Rule::unique('alternatives', 'name')->whereNull('deleted_at')
            ],
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ], [
            'name.required' => 'Nama CS harus diisi.',
            'name.unique' => 'CS dengan nama ini sudah ada dan masih aktif. Silakan gunakan nama lain atau restore CS yang sudah dihapus.',
            'name.max' => 'Nama CS tidak boleh lebih dari 255 karakter.',
        ]);

        try {
            // Pastikan is_active ada (dari hidden input + checkbox)
            if (!array_key_exists('is_active', $validated)) {
                $validated['is_active'] = true; // Default true untuk CS baru
            }

            // Code akan di-generate otomatis oleh model
            $alternative = Alternative::create($validated);

            return redirect()->route('alternatives.index')
                ->with('success', 'Data CS berhasil ditambahkan dengan kode ' . $alternative->code . '.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menambahkan CS: ' . $e->getMessage());
        }
    }

    public function edit(Alternative $alternative)
    {
        return view('alternatives.edit', compact('alternative'));
    }

    public function update(Request $request, Alternative $alternative)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                // Unique tapi abaikan soft deleted records dan record saat ini
                \Illuminate\Validation\Rule::unique('alternatives', 'name')
                    ->ignore($alternative->id)
                    ->whereNull('deleted_at')
            ],
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ], [
            'name.required' => 'Nama CS harus diisi.',
            'name.unique' => 'CS dengan nama ini sudah ada dan masih aktif. Silakan gunakan nama lain.',
            'name.max' => 'Nama CS tidak boleh lebih dari 255 karakter.',
        ]);

        try {
            // Pastikan is_active ada di validated data (dari hidden input + checkbox)
            // Laravel validator 'boolean' accepts: true, false, 1, 0, "1", "0"
            if (!array_key_exists('is_active', $validated)) {
                $validated['is_active'] = false;
            }

            // Code tidak bisa diubah
            $alternative->update($validated);

            return redirect()->route('alternatives.index')
                ->with('success', 'Data CS berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui CS: ' . $e->getMessage());
        }
    }

    public function destroy(Alternative $alternative)
    {
        $alternative->delete();

        return redirect()->route('alternatives.index')
            ->with('success', 'Data CS berhasil dihapus.');
    }

    /**
     * Bulk update status is_active untuk multiple alternatives
     */
    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:alternatives,id',
            'status' => 'required|boolean',
        ], [
            'ids.required' => 'Pilih minimal satu CS.',
            'ids.min' => 'Pilih minimal satu CS.',
            'status.required' => 'Status harus dipilih.',
        ]);

        try {
            $count = Alternative::whereIn('id', $validated['ids'])
                ->update(['is_active' => $validated['status']]);

            $statusText = $validated['status'] ? 'diaktifkan' : 'dinonaktifkan';

            return response()->json([
                'success' => true,
                'message' => "{$count} CS berhasil {$statusText}.",
                'count' => $count,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
