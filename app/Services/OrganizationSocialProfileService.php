<?php

namespace App\Services;

use App\Models\OrganizationSocialProfiles;

class OrganizationSocialProfileService
{
    /**
     * Mendapatkan daftar profil sosial media organisasi.
     */
    public function list(?int $organizationId = null)
    {
        $query = OrganizationSocialProfiles::query()
            ->with(['organization'])
            ->orderBy('platform', 'asc');

        if ($organizationId) {
            $query->where('organization_id', $organizationId);
        }

        return $query->get();
    }

    /**
     * Membuat profil sosial media baru untuk organisasi.
     */
    public function create(array $data): OrganizationSocialProfiles
    {
        return OrganizationSocialProfiles::create([
            'organization_id' => $data['organization_id'],
            'platform'        => $data['platform'],
            'username'        => $data['username'],
            'url'             => $data['url'] ?? null,
        ]);
    }

    /**
     * Memperbarui profil sosial media organisasi.
     */
    public function update(int $id, array $data): OrganizationSocialProfiles
    {
        $profile = OrganizationSocialProfiles::findOrFail($id);
        $profile->update([
            'organization_id' => $data['organization_id'] ?? $profile->organization_id,
            'platform'        => $data['platform'] ?? $profile->platform,
            'username'        => $data['username'] ?? $profile->username,
            'url'             => $data['url'] ?? $profile->url,
        ]);

        return $profile->refresh();
    }

    /**
     * Menghapus profil sosial media organisasi.
     */
    public function delete(int $id): void
    {
        $profile = OrganizationSocialProfiles::findOrFail($id);
        $profile->delete();
    }
}
