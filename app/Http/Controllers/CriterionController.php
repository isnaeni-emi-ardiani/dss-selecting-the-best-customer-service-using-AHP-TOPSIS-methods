<?php

namespace App\Http\Controllers;

use App\Models\{Criterion, AhpSession};
use Illuminate\Http\Request;

class CriterionController extends Controller
{
    public function index()
    {
        $criteria = Criterion::all();
        $activeSession = AhpSession::getActiveSession();
        return view('criteria.index', compact('criteria', 'activeSession'));
    }

    public function create()
    {
        return view('criteria.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                // Unique tapi abaikan soft deleted records
                \Illuminate\Validation\Rule::unique('criteria', 'name')->whereNull('deleted_at')
            ],
            'description' => 'nullable|string',
            'type' => 'required|in:benefit,cost',
        ], [
            'name.required' => 'Nama kriteria harus diisi.',
            'name.unique' => 'Kriteria dengan nama ini sudah ada dan masih aktif. Silakan gunakan nama lain atau restore kriteria yang sudah dihapus.',
            'name.max' => 'Nama kriteria tidak boleh lebih dari 255 karakter.',
            'type.required' => 'Tipe kriteria harus dipilih.',
            'type.in' => 'Tipe kriteria harus Benefit atau Cost.',
        ]);

        try {
            // Code akan di-generate otomatis oleh model
            $criterion = Criterion::create($validated);

            // Auto-deactivate active session yang tidak kompatibel
            $this->deactivateIncompatibleSessions();

            return redirect()->route('criteria.index')
                ->with('success', 'Kriteria berhasil ditambahkan dengan kode ' . $criterion->code . '. Session AHP aktif telah dinonaktifkan karena jumlah kriteria berubah.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menambahkan kriteria: ' . $e->getMessage());
        }
    }

    public function edit(Criterion $criterion)
    {
        return view('criteria.edit', compact('criterion'));
    }

    public function update(Request $request, Criterion $criterion)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                // Unique tapi abaikan soft deleted records dan record saat ini
                \Illuminate\Validation\Rule::unique('criteria', 'name')
                    ->ignore($criterion->id)
                    ->whereNull('deleted_at')
            ],
            'description' => 'nullable|string',
            'type' => 'required|in:benefit,cost',
        ], [
            'name.required' => 'Nama kriteria harus diisi.',
            'name.unique' => 'Kriteria dengan nama ini sudah ada dan masih aktif. Silakan gunakan nama lain.',
            'name.max' => 'Nama kriteria tidak boleh lebih dari 255 karakter.',
            'type.required' => 'Tipe kriteria harus dipilih.',
            'type.in' => 'Tipe kriteria harus Benefit atau Cost.',
        ]);

        try {
            // Code tidak boleh diupdate
            $criterion->update($validated);

            return redirect()->route('criteria.index')
                ->with('success', 'Kriteria berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui kriteria: ' . $e->getMessage());
        }
    }

    public function destroy(Criterion $criterion)
    {
        $criterion->delete();

        // Auto-deactivate active session yang tidak kompatibel
        $this->deactivateIncompatibleSessions();

        return redirect()->route('criteria.index')
            ->with('success', 'Kriteria berhasil dihapus. Session AHP aktif telah dinonaktifkan karena jumlah kriteria berubah.');
    }

    /**
     * Show deleted criteria
     */
    public function deleted()
    {
        $deletedCriteria = Criterion::onlyTrashed()->orderBy('deleted_at', 'desc')->get();
        return view('criteria.deleted', compact('deletedCriteria'));
    }

    /**
     * Restore deleted criterion
     */
    public function restore($id)
    {
        $criterion = Criterion::withTrashed()->findOrFail($id);

        if (!$criterion->trashed()) {
            return redirect()->route('criteria.deleted')
                ->with('error', 'Kriteria ini tidak dalam status dihapus.');
        }

        $criterion->restore();

        // Auto-deactivate active session yang tidak kompatibel
        $this->deactivateIncompatibleSessions();

        return redirect()->route('criteria.deleted')
            ->with('success', 'Kriteria "' . $criterion->name . '" berhasil direstore. Session AHP aktif telah dinonaktifkan karena jumlah kriteria berubah.');
    }

    /**
     * Permanently delete criterion
     */
    public function forceDelete($id)
    {
        $criterion = Criterion::withTrashed()->findOrFail($id);

        if (!$criterion->trashed()) {
            return redirect()->route('criteria.deleted')
                ->with('error', 'Hanya kriteria yang sudah dihapus yang bisa dihapus permanen.');
        }

        $name = $criterion->name;
        $criterion->forceDelete();

        return redirect()->route('criteria.deleted')
            ->with('success', 'Kriteria "' . $name . '" berhasil dihapus permanen.');
    }

    /**
     * Deactivate AHP session yang tidak kompatibel dengan jumlah kriteria saat ini
     */
    protected function deactivateIncompatibleSessions()
    {
        $currentCount = Criterion::count();
        $activeSession = AhpSession::getActiveSession();

        if ($activeSession) {
            $sessionCount = count($activeSession->criteria_ids ?? []);

            // Jika jumlah kriteria berbeda, nonaktifkan session
            if ($sessionCount !== $currentCount) {
                $activeSession->update(['is_active' => false]);
            }
        }
    }
}
