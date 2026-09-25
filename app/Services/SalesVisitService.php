<?php

namespace App\Services;

use App\Models\SalesVisit;
use App\Models\User;

class SalesVisitService
{
    /**
     * Mendapatkan daftar kunjungan sales dengan filter dan pagination.
     */
    public function list(
        ?User $user = null,
        ?int $salesId = null,
        ?int $organizationId = null,
        ?bool $isFollowUp = null,
        ?string $search = null,
        int $perPage = 50,
        int $page = 1
    ) {
        $query = SalesVisit::query()
            ->with(['organization', 'contactPerson.person', 'user'])
            ->orderBy('visit_date', 'desc');

        if ($salesId) {
            $query->where('user_id', $salesId);
        }

        if ($organizationId) {
            $query->where('organization_id', $organizationId);
        }

        if ($isFollowUp !== null) {
            $query->where('is_follow_up', $isFollowUp);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('visit_purpose', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhereHas('organization', function ($oq) use ($search) {
                        $oq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Mendapatkan detail kunjungan sales berdasarkan ID.
     */
    public function find(int $id): SalesVisit
    {
        return SalesVisit::with(['organization', 'contactPerson.person', 'user'])->findOrFail($id);
    }

    /**
     * Membuat kunjungan sales baru.
     */
    public function create(array $data, User $creator): SalesVisit
    {
        return SalesVisit::create([
            'user_id'                 => $data['user_id'] ?? $creator->id,
            'organization_id'         => $data['organization_id'],
            'organization_contact_id' => $data['organization_contact_id'] ?? null,
            'attachment_id'           => $data['attachment_id'] ?? null,
            'visit_date'              => $data['visit_date'],
            'visit_purpose'           => $data['visit_purpose'],
            'is_follow_up'            => $data['is_follow_up'] ?? false,
            'latitude'                => $data['latitude'] ?? null,
            'longitude'               => $data['longitude'] ?? null,
            'address'                 => $data['address'] ?? null,
            'province_id'             => $data['province_id'] ?? null,
            'regency_id'              => $data['regency_id'] ?? null,
            'district_id'             => $data['district_id'] ?? null,
            'village_id'              => $data['village_id'] ?? null,
        ]);
    }

    /**
     * Memperbarui kunjungan sales.
     */
    public function update(int $id, array $data): SalesVisit
    {
        $visit = SalesVisit::findOrFail($id);

        $updateData = [];
        if (array_key_exists('user_id', $data))                 $updateData['user_id']                 = $data['user_id'];
        if (array_key_exists('organization_id', $data))         $updateData['organization_id']         = $data['organization_id'];
        if (array_key_exists('organization_contact_id', $data)) $updateData['organization_contact_id'] = $data['organization_contact_id'];
        if (array_key_exists('attachment_id', $data))           $updateData['attachment_id']           = $data['attachment_id'];
        if (array_key_exists('visit_date', $data))              $updateData['visit_date']              = $data['visit_date'];
        if (array_key_exists('visit_purpose', $data))           $updateData['visit_purpose']           = $data['visit_purpose'];
        if (array_key_exists('is_follow_up', $data))            $updateData['is_follow_up']            = (bool) $data['is_follow_up'];
        if (array_key_exists('latitude', $data))                $updateData['latitude']                = $data['latitude'];
        if (array_key_exists('longitude', $data))               $updateData['longitude']               = $data['longitude'];
        if (array_key_exists('address', $data))                 $updateData['address']                 = $data['address'];
        if (array_key_exists('province_id', $data))             $updateData['province_id']             = $data['province_id'];
        if (array_key_exists('regency_id', $data))              $updateData['regency_id']              = $data['regency_id'];
        if (array_key_exists('district_id', $data))             $updateData['district_id']             = $data['district_id'];
        if (array_key_exists('village_id', $data))              $updateData['village_id']              = $data['village_id'];

        if (!empty($updateData)) {
            $visit->update($updateData);
        }

        return $visit->refresh();
    }

    /**
     * Menghapus kunjungan sales.
     */
    public function delete(int $id): void
    {
        $visit = SalesVisit::findOrFail($id);
        $visit->delete();
    }
}
