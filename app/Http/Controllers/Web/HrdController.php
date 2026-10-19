<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\Hrd\StoreCandidateRequest;
use App\Http\Requests\Hrd\UpdateCandidateRequest;
use App\Models\Candidate;
use App\Services\HrdService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;
use App\Http\Controllers\Controller;

class HrdController extends Controller
{
    public function __construct(protected HrdService $hrdService)
    {
    }

    public function index()
    {
        $candidates = $this->hrdService->index();
        $divisions = $this->hrdService->getFilterOptions()['division'] ?? [];

        return view('pages.hrd', compact('candidates', 'divisions'));
    }

    public function store(StoreCandidateRequest $request): RedirectResponse
    {
        try {
            $data = array_merge($request->validated(), $request->allFiles());
            $this->hrdService->create($data);

            return redirect()->route('hrd.index')->with('success', 'Calon karyawan berhasil ditambahkan!');
        } catch (Throwable $e) {
            Log::error('Failed to store candidate', [
                'error' => $e->getMessage(),
            ]);

            return back()->withInput()->with('error', 'Data kandidat gagal disimpan: ' . $e->getMessage());
        }
    }

    public function update(UpdateCandidateRequest $request, Candidate|int|string $candidate): RedirectResponse
    {
        $candidate = $candidate instanceof Candidate ? $candidate : Candidate::findOrFail((int) $candidate);

        try {
            $data = array_merge($request->validated(), $request->allFiles());
            $this->hrdService->update($candidate, $data);

            if ((int) $candidate->fresh()->status === Candidate::STATUS_ACCEPTED) {
                $this->hrdService->promoteCandidateToEmployee($candidate);
            }

            return redirect()->route('hrd.index')->with('success', 'Data kandidat berhasil diupdate!');
        } catch (Throwable $e) {
            Log::error('Failed to update candidate', [
                'candidate_id' => $candidate->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withInput()->with('error', 'Data kandidat gagal diperbarui: ' . $e->getMessage());
        }
    }

    public function destroy(Candidate|int|string $candidate): RedirectResponse
    {
        $candidate = $candidate instanceof Candidate ? $candidate : Candidate::findOrFail((int) $candidate);

        try {
            $this->hrdService->delete($candidate);

            return redirect()->route('hrd.index')->with('success', 'Data kandidat berhasil dihapus!');
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (Throwable $e) {
            Log::error('Failed to delete candidate', [
                'candidate_id' => $candidate->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Data kandidat gagal dihapus.');
        }
    }

    public function promoteToEmployee(Request $request, Candidate|int|string $candidate): RedirectResponse
    {
        $candidate = $candidate instanceof Candidate ? $candidate : Candidate::findOrFail((int) $candidate);

        $validated = $request->validate([
            'department_id' => ['nullable', 'integer'],
            'join_date' => ['nullable', 'date'],
        ]);

        if ((int) $candidate->status !== 2) {
            return back()->with('error', 'Kandidat harus berstatus diterima dulu.');
        }

        try {
            $this->hrdService->promoteCandidateToEmployee($candidate, $validated);

            return redirect()->route('employees.index')->with('success', 'Kandidat berhasil dijadikan karyawan.');
        } catch (Throwable $e) {
            Log::error('Failed to promote candidate to employee', [
                'candidate_id' => $candidate->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Kandidat gagal dijadikan karyawan.');
        }
    }
}
