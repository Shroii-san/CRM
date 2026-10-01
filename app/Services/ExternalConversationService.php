<?php

namespace App\Services;

use App\Models\ExternalConversation;

class ExternalConversationService
{
    /**
     * Mendapatkan daftar percakapan eksternal dengan filter, search, sort, dan pagination.
     */
    public function list(
        ?int $clientId = null,
        ?string $platform = null,
        ?string $search = null,
        int $perPage = 50,
        int $page = 1,
        ?string $sortBy = 'last_interaction_at',
        ?string $sortDir = 'desc'
    ) {
        $params = array_filter([
            'client_id' => $clientId,
            'platform'  => $platform,
            'search'    => $search,
            'sort_by'   => $sortBy,
            'sort_dir'  => $sortDir,
        ], fn ($val) => $val !== null);

        return ExternalConversation::query()
            ->with(['client'])
            ->applyFilters(
                params: $params,
                searchable: ['external_conversation_id', 'platform'],
                filterable: ['client_id', 'platform'],
                sortable: ['id', 'platform', 'last_interaction_at', 'created_at'],
                defaultSortField: 'last_interaction_at',
                defaultSortDir: 'desc'
            )
            ->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Detail percakapan eksternal berdasarkan ID.
     */
    public function find(int $id): ExternalConversation
    {
        return ExternalConversation::with(['client', 'attachable'])->findOrFail($id);
    }

    /**
     * Membuat data percakapan eksternal baru.
     */
    public function create(array $data): ExternalConversation
    {
        return ExternalConversation::create([
            'platform'                 => $data['platform'],
            'external_conversation_id' => $data['external_conversation_id'],
            'client_id'                => $data['client_id'],
            'last_interaction_at'      => $data['last_interaction_at'] ?? now(),
            'metadata'                 => $data['metadata'] ?? null,
        ]);
    }

    /**
     * Memperbarui data percakapan eksternal.
     */
    public function update(int $id, array $data): ExternalConversation
    {
        $conversation = ExternalConversation::findOrFail($id);

        $updateData = [];
        if (array_key_exists('platform', $data))                 $updateData['platform']                 = $data['platform'];
        if (array_key_exists('external_conversation_id', $data)) $updateData['external_conversation_id'] = $data['external_conversation_id'];
        if (array_key_exists('client_id', $data))                $updateData['client_id']                = $data['client_id'];
        if (array_key_exists('last_interaction_at', $data))      $updateData['last_interaction_at']      = $data['last_interaction_at'];
        if (array_key_exists('metadata', $data))                 $updateData['metadata']                 = $data['metadata'];

        if (!empty($updateData)) {
            $conversation->update($updateData);
        }

        return $conversation->refresh();
    }

    /**
     * Menghapus data percakapan eksternal.
     */
    public function delete(int $id): void
    {
        $conversation = ExternalConversation::findOrFail($id);
        $conversation->delete();
    }
}
