<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\FamilyMember\StoreFamilyMemberRequest;
use App\Http\Requests\FamilyMember\UpdateFamilyMemberRequest;
use App\Models\Employee;
use App\Models\FamilyMember;
use App\Services\FamilyMemberService;
use App\Http\Controllers\Controller;

class FamilyMemberController extends Controller
{
    public function __construct(protected FamilyMemberService $familyMemberService)
    {
    }

    public function index()
    {
        $familyMembers = $this->familyMemberService->index();

        return view('pages.family', [
            'familyMembers' => $familyMembers,
        ]);
    }

    public function store(StoreFamilyMemberRequest $request, Employee $employee)
    {
        $this->familyMemberService->store($employee, $request->validated());

        return back()->with('success', 'Data keluarga berhasil ditambahkan.');
    }

    public function update(UpdateFamilyMemberRequest $request, FamilyMember $familyMember)
    {
        $this->familyMemberService->update($familyMember, $request->validated());

        return back()->with('success', 'Data keluarga berhasil diperbarui.');
    }

    public function destroy(FamilyMember $familyMember)
    {
        $this->familyMemberService->destroy($familyMember);

        return back()->with('success', 'Data keluarga berhasil dihapus.');
    }
}
