<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\FamilyMember;
use Carbon\Carbon;

class FamilyMemberService
{
    public function index()
    {
        return FamilyMember::query()
            ->with(['employee', 'candidate'])
            ->latest()
            ->get();
    }

    public function store(Employee $employee, array $data): FamilyMember
    {
        return $employee->families()->create([
            'name'        => $data['name'],
            'birth_place' => $data['birth_place'],
            'birth_date'  => $this->normalizeDate($data['birth_date']),
            'status'      => (int) $data['status'],
        ]);
    }

    public function storeForCandidate(\App\Models\Candidate $candidate, array $data): FamilyMember
    {
        return $candidate->families()->create([
            'name'        => $data['name'],
            'birth_place' => $data['birth_place'],
            'birth_date'  => $this->normalizeDate($data['birth_date']),
            'status'      => (int) $data['status'],
        ]);
    }

    public function update(FamilyMember $familyMember, array $data): FamilyMember
    {
        $familyMember->update([
            'name' => $data['name'],
            'birth_place' => $data['birth_place'],
            'birth_date' => $this->normalizeDate($data['birth_date']),
            'status' => (int) $data['status'],
        ]);

        return $familyMember->fresh();
    }

    public function destroy(FamilyMember $familyMember): void
    {
        $familyMember->delete();
    }

    public function normalizeDate(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        $value = trim($value);

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $value;
        }

        try {
            $date = Carbon::createFromFormat('d/m/Y', $value);

            if (!$date || $date->format('d/m/Y') !== $value) {
                return $value;
            }

            return $date->format('Y-m-d');
        } catch (\Throwable $e) {
            return $value;
        }
    }
}
