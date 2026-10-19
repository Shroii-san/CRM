<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\FamilyMember\StoreFamilyMemberRequest;
use App\Http\Requests\FamilyMember\UpdateFamilyMemberRequest;
use App\Models\Candidate;
use App\Models\Employee;
use App\Models\FamilyMember;
use App\Services\FamilyMemberService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FamilyMemberController extends Controller
{
    public function __construct(protected FamilyMemberService $familyMemberService)
    {
    }

    public function index()
    {
        $familyMembers = $this->familyMemberService->index();
        $employee      = Employee::orderBy('full_name')->get(['id', 'full_name']);
        $candidate     = Candidate::orderBy('full_name')->get(['id', 'full_name']);

        return view('pages.family', compact('familyMembers', 'employee', 'candidate'));
    }

    /**
     * Store a new family member for an employee.
     * Called via AJAX from the HRD keluarga modal — always returns JSON.
     * Route: POST /family/{employee}/family
     */
    public function store(StoreFamilyMemberRequest $request, Employee $employee): JsonResponse
    {
        $familyMember = $this->familyMemberService->store($employee, $request->validated());

        return response()->json([
            'message' => 'Data keluarga berhasil ditambahkan.',
            'data'    => $this->familyMemberData($familyMember),
        ]);
    }

    /**
     * Store a family member from the standalone /family page form (pemilik select).
     * Route: POST /family
     */
    public function storeStandalone(StoreFamilyMemberRequest $request)
    {
        $validated = $request->validated();

        if (!empty($validated['employee_id'])) {
            $employee = Employee::findOrFail($validated['employee_id']);
            $this->familyMemberService->store($employee, $validated);
        } elseif (!empty($validated['candidate_id'])) {
            $candidate = Candidate::findOrFail($validated['candidate_id']);
            $this->familyMemberService->storeForCandidate($candidate, $validated);
        } else {
            return back()->withErrors(['pemilik' => 'Pilih pemilik data (karyawan atau kandidat).'])->withInput();
        }

        return back()->with('success', 'Data keluarga berhasil ditambahkan.');
    }

    /**
     * Update an existing family member.
     * Called via AJAX from the HRD keluarga modal (Accept: application/json) → returns JSON.
     * Called from standalone page form → returns redirect.
     * Route: PUT /family/{family}
     */
    public function update(UpdateFamilyMemberRequest $request, FamilyMember $family)
    {
        $familyMember = $this->familyMemberService->update($family, $request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Data keluarga berhasil diperbarui.',
                'data'    => $this->familyMemberData($familyMember),
            ]);
        }

        return back()->with('success', 'Data keluarga berhasil diperbarui.');
    }

    /**
     * Delete a family member.
     * Called via AJAX from the HRD keluarga modal (Accept: application/json) → returns JSON.
     * Called from standalone page form → returns redirect.
     * Route: DELETE /family/{family}
     */
    public function destroy(Request $request, FamilyMember $family)
    {
        $this->familyMemberService->destroy($family);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Data keluarga berhasil dihapus.',
            ]);
        }

        return back()->with('success', 'Data keluarga berhasil dihapus.');
    }

    /**
     * Serialize a FamilyMember for the JSON response consumed by family.js.
     */
    private function familyMemberData(FamilyMember $member): array
    {
        return [
            'id'            => $member->id,
            'name'          => $member->name,
            'nama'          => $member->name,
            'birth_place'   => $member->birth_place,
            'tempat_lahir'  => $member->birth_place,
            'birth_date'    => $member->birth_date?->format('Y-m-d'),
            'tanggal_lahir' => $member->birth_date?->format('Y-m-d'),
            'status'        => $member->status,
            'status_label'  => $member->status_label,
        ];
    }
}
