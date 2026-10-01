<?php

namespace App\Services;

use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ClientService
{
    /**
     * Mendapatkan daftar client murni core dengan standard query pattern (search, filter, sort, pagination).
     */
    public function list(
        ?User $user = null,
        ?string $search = null,
        ?int $sourceId = null,
        ?bool $isActive = null,
        int $perPage = 50,
        int $page = 1,
        ?string $sortBy = 'created_at',
        ?string $sortDir = 'desc'
    ) {
        $params = array_filter([
            'search'    => $search,
            'source_id' => $sourceId,
            'is_active' => $isActive,
            'sort_by'   => $sortBy,
            'sort_dir'  => $sortDir,
        ], fn ($val) => $val !== null);

        return Client::query()
            ->with(['person', 'organization', 'source'])
            ->applyFilters(
                params: $params,
                searchable: [
                    'person.name',
                    'person.email',
                    'person.phone',
                    'organization.name',
                    'organization.email',
                    'organization.phone',
                ],
                filterable: ['source_id', 'is_active'],
                sortable: ['id', 'created_at', 'updated_at'],
                defaultSortField: 'created_at',
                defaultSortDir: 'desc'
            )
            ->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Membuat data client baru.
     */
    public function create(array $data): Client
    {
        return Client::create([
            'person_id'       => $data['person_id'] ?? null,
            'organization_id' => $data['organization_id'] ?? null,
            'source_id'       => $data['source_id'],
            'is_active'       => $data['is_active'] ?? true,
        ]);
    }

    /**
     * Memperbarui data client.
     */
    public function update(int $id, array $data): Client
    {
        $client = Client::findOrFail($id);

        $clientUpdate = [];
        if (array_key_exists('person_id', $data))       $clientUpdate['person_id']       = $data['person_id'];
        if (array_key_exists('organization_id', $data)) $clientUpdate['organization_id'] = $data['organization_id'];
        if (array_key_exists('source_id', $data))       $clientUpdate['source_id']       = $data['source_id'];
        if (array_key_exists('is_active', $data))       $clientUpdate['is_active']       = (bool) $data['is_active'];

        if (!empty($clientUpdate)) {
            $client->update($clientUpdate);
        }

        return $client->refresh();
    }

    /**
     * Menghapus data client.
     */
    public function delete(int $id): void
    {
        $client = Client::findOrFail($id);
        $client->delete();
    }

    /**
     * Menghapus banyak data client sekaligus (Bulk Delete).
     */
    public function bulkDelete(array $ids): void
    {
        Client::whereIn('id', $ids)->delete();
    }
}
