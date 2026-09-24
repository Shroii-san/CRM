<?php

namespace App\Services;

use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ClientService
{
    /**
     * Mendapatkan daftar client murni core.
     */
    public function list(
        ?User $user = null,
        ?string $search = null,
        ?int $sourceId = null,
        ?bool $isActive = null,
        int $perPage = 50,
        int $page = 1
    ) {
        $query = Client::query()
            ->with(['person', 'organization', 'source'])
            ->orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('person', function ($pq) use ($search) {
                    $pq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                })
                ->orWhereHas('organization', function ($oq) use ($search) {
                    $oq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            });
        }

        if ($sourceId) {
            $query->where('source_id', $sourceId);
        }

        if ($isActive !== null) {
            $query->where('is_active', $isActive);
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
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
