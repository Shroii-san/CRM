<?php

namespace App\Services;

use App\Models\Organization;
use App\Models\Industry;

class OrganizationService extends BaseService
{
    protected function getModel(): string
    {
        return Organization::class;
    }

    protected function getSearchableColumns(): array
    {
        return ['name', 'description',];
    }

    protected function getFilterDefinitions(): array
    {
        return [
            'industry_id' => 'equal',
            'tier' => 'equal',
            'is_active' => 'equal',
        ];
    }

    protected function getWithRelations(): array
    {
        return ['industry'];
    }

    protected function getSortableColumns(): array
    {
        return ['id', 'name', 'created_at', 'tier', 'is_active', 'industry_id'];
    }

    public function getFilterOptions(): array
    {
        return [
            'industry_id' => Industry::pluck('name', 'id'),
            'tier' => ['A', 'B', 'C', 'D'],
            'is_active' => [true => 'Active', false => 'Inactive'],
        ];
    }

    /**
     * Membuat organisasi baru.
     */
    public function create(array $data): Organization
    {
        return Organization::create([
            'industry_id' => $data['industry_id'],
            'tier' => $data['tier'] ?? 'A',
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'website' => $data['website'] ?? null,
            'description' => $data['description'] ?? null,
            'address' => $data['address'] ?? null,
            'province_id' => $data['province_id'] ?? null,
            'regency_id' => $data['regency_id'] ?? null,
            'district_id' => $data['district_id'] ?? null,
            'village_id' => $data['village_id'] ?? null,
            'is_active' => $data['is_active'] ?? true,
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
            'tier' => $data['tier'] ?? $organization->tier,
            'name' => $data['name'] ?? $organization->name,
            'email' => $data['email'] ?? $organization->email,
            'phone' => $data['phone'] ?? $organization->phone,
            'website' => $data['website'] ?? $organization->website,
            'description' => $data['description'] ?? $organization->description,
            'address' => $data['address'] ?? $organization->address,
            'province_id' => $data['province_id'] ?? $organization->province_id,
            'regency_id' => $data['regency_id'] ?? $organization->regency_id,
            'district_id' => $data['district_id'] ?? $organization->district_id,
            'village_id' => $data['village_id'] ?? $organization->village_id,
            'is_active' => $data['is_active'] ?? $organization->is_active,
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
