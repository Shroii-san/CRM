<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ClientService extends BaseService
{

    protected function getModel(): string
    {
        return Client::class;
    }

    protected function getSearchableColumns(): array
    {
        return ['person.name', 'organization.name'];
    }

    protected function getFilterDefinitions(): array
    {
        return [
            'source_id' => 'equal',
            'is_active' => 'equal',
        ];
    }

    protected function getWithRelations(): array
    {
        return ['person', 'organization', 'source'];
    }

    protected function getSortableColumns(): array
    {
        return ['id', 'created_at', 'person.name', 'organization.name'];
    }

    /**
     * Override applyFilters untuk menangani filter 'client_type' secara custom.
     * Kolom client_type tidak ada di DB; tipe ditentukan dari relasi:
     *   - 'person'       → person_id IS NOT NULL
     *   - 'organization' → organization_id IS NOT NULL
     */
    protected function applyFilters(\Illuminate\Database\Eloquent\Builder $query, array $filters): \Illuminate\Database\Eloquent\Builder
    {
        if (isset($filters['client_type']) && $filters['client_type'] !== '') {
            $type = $filters['client_type'];

            if ($type === 'person') {
                $query->whereNotNull('person_id');
            } elseif ($type === 'organization') {
                $query->whereNotNull('organization_id');
            }

            // Hapus dari array agar tidak diteruskan ke parent yang tidak tahu filter ini
            unset($filters['client_type']);
        }

        return parent::applyFilters($query, $filters);
    }

    protected function applySorting(\Illuminate\Database\Eloquent\Builder $query, ?string $sortBy, ?string $sortDir): void
    {
        $sortDir = strtolower($sortDir ?? 'asc') === 'desc' ? 'desc' : 'asc';

        if ($sortBy === 'clients_name') {
            $query->orderBy(
                Client::select('name')->whereColumn('persons.id', 'clients.person_id'),
                $sortDir
            );
            return;
        }

        if ($sortBy === 'organizations_name') {
            $query->orderBy(
                Organization::select('name')->whereColumn('organizations.id', 'clients.organization_id'),
                $sortDir
            );
            return;
        }

        parent::applySorting($query, $sortBy, $sortDir);
    }

    /**
     * Membuat data client baru.
     */
    public function create(array $data): Client
    {
        return Client::create([
            'person_id' => $data['person_id'] ?? null,
            'organization_id' => $data['organization_id'] ?? null,
            'source_id' => $data['source_id'],
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    /**
     * Memperbarui data client.
     */
    public function update(int $id, array $data): Client
    {
        $client = Client::findOrFail($id);

        $clientUpdate = [];
        if (array_key_exists('person_id', $data))
            $clientUpdate['person_id'] = $data['person_id'];
        if (array_key_exists('organization_id', $data))
            $clientUpdate['organization_id'] = $data['organization_id'];
        if (array_key_exists('source_id', $data))
            $clientUpdate['source_id'] = $data['source_id'];
        if (array_key_exists('is_active', $data))
            $clientUpdate['is_active'] = (bool) $data['is_active'];

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
