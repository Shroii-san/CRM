<?php

namespace App\Services;

use App\Models\Organization;
use App\Models\User;

class OrganizationService
{
    /**
     * Mendapatkan daftar organisasi murni core (list, search, data_scoping).
     */
    public function list(
        ?User $user = null,
        ?string $search = null,
        ?int $industryId = null,
        ?string $tier = null,
        ?bool $isActive = null,
        int $perPage = 50,
        int $page = 1
    ) {
        $query = Organization::query()
            ->with(['industry', 'province', 'regency', 'district', 'village'])
            ->orderBy('name', 'asc');

        if ($user && method_exists(Organization::class, 'scopeForUser')) {
            $query->forUser($user);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        if ($industryId) {
            $query->where('industry_id', $industryId);
        }

        if ($tier) {
            $query->where('tier', $tier);
        }

        if ($isActive !== null) {
            $query->where('is_active', $isActive);
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Membuat organisasi baru.
     */
    public function create(array $data): Organization
    {
        return Organization::create([
            'industry_id' => $data['industry_id'],
            'tier'        => $data['tier'] ?? 'A',
            'name'        => $data['name'],
            'email'       => $data['email'] ?? null,
            'phone'       => $data['phone'] ?? null,
            'website'     => $data['website'] ?? null,
            'description' => $data['description'] ?? null,
            'address'     => $data['address'] ?? null,
            'province_id' => $data['province_id'] ?? null,
            'regency_id'  => $data['regency_id'] ?? null,
            'district_id' => $data['district_id'] ?? null,
            'village_id'  => $data['village_id'] ?? null,
            'is_active'   => $data['is_active'] ?? true,
        ]);
    }

    /**
     * Memperbarui data organisasi.
     */
    public function update(int $id, array $data): Organization
    {
        $organization = Organization::findOrFail($id);
        $organization->update([
            'industry_id' => $data['industry_id'] ?? $organization->industry_id,
            'tier'        => $data['tier'] ?? $organization->tier,
            'name'        => $data['name'] ?? $organization->name,
            'email'       => $data['email'] ?? $organization->email,
            'phone'       => $data['phone'] ?? $organization->phone,
            'website'     => $data['website'] ?? $organization->website,
            'description' => $data['description'] ?? $organization->description,
            'address'     => $data['address'] ?? $organization->address,
            'province_id' => $data['province_id'] ?? $organization->province_id,
            'regency_id'  => $data['regency_id'] ?? $organization->regency_id,
            'district_id' => $data['district_id'] ?? $organization->district_id,
            'village_id'  => $data['village_id'] ?? $organization->village_id,
            'is_active'   => $data['is_active'] ?? $organization->is_active,
        ]);

        return $organization->refresh();
    }

    /**
     * Menghapus organisasi.
     */
    public function delete(int $id): void
    {
        $organization = Organization::findOrFail($id);
        $organization->delete();
    }
}
