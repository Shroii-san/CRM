<?php

namespace App\Services\organization;

use App\Models\{Organization, Industry, Province, Regency, User};
use App\Services\BaseService;
use App\Services\AttachmentService;
use Illuminate\Http\UploadedFile;

class OrganizationService extends BaseService
{
    public function __construct(private AttachmentService $attachmentService)
    {
    }

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
            'province_id' => 'equal',
            'regency_id' => 'equal',
        ];
    }

    protected function getWithRelations(): array
    {
        return ['industry', 'logo'];
    }

    protected function getSortableColumns(): array
    {
        return ['id', 'name', 'created_at', 'tier', 'is_active', 'industry_id', 'industry_name', 'province_id', 'province_name', 'regency_id', 'regency_name',];
    }

    protected function applySorting(\Illuminate\Database\Eloquent\Builder $query, ?string $sortBy, ?string $sortDir): void
    {
        $sortDir = strtolower($sortDir ?? 'asc') === 'desc' ? 'desc' : 'asc';

        if ($sortBy === 'industry_name') {
            $query->orderBy(
                Industry::select('name')->whereColumn('industries.id', 'organizations.industry_id'),
                $sortDir
            );
            return;
        }
        if ($sortBy === 'province_name') {
            $query->orderBy(
                Province::select('name')->whereColumn('provinces.id', 'organizations.province_id'),
                $sortDir
            );
            return;
        }
        if ($sortBy === 'regency_name') {
            $query->orderBy(
                Regency::select('name')->whereColumn('regencies.id', 'organizations.regency_id'),
                $sortDir
            );
            return;
        }

        parent::applySorting($query, $sortBy, $sortDir);
    }

    public function getFilterOptions(): array
    {
        return [
            'industry_id' => Industry::pluck('name', 'id'),
            'tier' => ['A', 'B', 'C', 'D'],
            'is_active' => [true => 'Active', false => 'Inactive'],
            'province_id' => Province::pluck('name', 'id'),
            'regency_id' => Regency::pluck('name', 'id'),
        ];
    }

    /**
     * Mengambil detail satu organisasi beserta seluruh relasinya.
     */
    public function findWithDetails(int $id): Organization
    {
        return Organization::with([
            'industry',
            'province',
            'regency',
            'district',
            'village',
            'contacts.person',
            'socialProfiles',
            'logo',
        ])->findOrFail($id);
    }

    /**
     * Membuat organisasi baru beserta logo jika diunggah.
     */
    public function create(array $data, ?UploadedFile $logoFile = null, ?User $uploader = null): Organization
    {
        $organization = Organization::create([
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

        if ($logoFile && $uploader) {
            $this->attachmentService->create([
                'attachable_type' => Organization::class,
                'attachable_id'   => $organization->id,
                'attachment_type' => 'logo',
                'description'     => 'Logo ' . $organization->name,
            ], $logoFile, $uploader);
        }

        return $organization->refresh()->load('logo');
    }

    /**
     * Memperbarui data organisasi beserta logo jika diunggah.
     */
    public function update(int $id, array $data, ?UploadedFile $logoFile = null, ?User $uploader = null): Organization
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

        if ($logoFile && $uploader) {
            // Hapus logo lama dan simpan logo baru yang telah terkompresi
            $this->attachmentService->deleteForAttachable(Organization::class, $organization->id, 'logo');
            $this->attachmentService->create([
                'attachable_type' => Organization::class,
                'attachable_id'   => $organization->id,
                'attachment_type' => 'logo',
                'description'     => 'Logo ' . $organization->name,
            ], $logoFile, $uploader);
        }

        return $organization->refresh()->load('logo');
    }

    /**
     * Menghapus organisasi beserta seluruh lampirannya dari storage.
     */
    public function delete(int $id): void
    {
        $organization = Organization::findOrFail($id);
        $this->attachmentService->deleteForAttachable(Organization::class, $organization->id);
        $organization->delete();
    }
}
