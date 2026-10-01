<?php

namespace App\Services;

use App\Models\Note;
use App\Models\User;

class NoteService
{
    /**
     * Mendapatkan daftar catatan dengan filter, search, sort, dan pagination.
     */
    public function list(
        ?User $user = null,
        ?int $clientId = null,
        ?int $dealId = null,
        ?string $noteType = null,
        ?string $search = null,
        int $perPage = 50,
        int $page = 1,
        ?string $sortBy = 'created_at',
        ?string $sortDir = 'desc'
    ) {
        $params = array_filter([
            'client_id' => $clientId,
            'deal_id'   => $dealId,
            'note_type' => $noteType,
            'search'    => $search,
            'sort_by'   => $sortBy,
            'sort_dir'  => $sortDir,
        ], fn ($val) => $val !== null);

        return Note::query()
            ->with(['client', 'deal', 'createdBy'])
            ->applyFilters(
                params: $params,
                searchable: ['content'],
                filterable: ['client_id', 'deal_id', 'note_type', 'created_by'],
                sortable: ['id', 'note_type', 'created_at', 'updated_at'],
                defaultSortField: 'created_at',
                defaultSortDir: 'desc'
            )
            ->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Detail catatan berdasarkan ID.
     */
    public function find(int $id): Note
    {
        return Note::with(['client', 'deal', 'createdBy'])->findOrFail($id);
    }

    /**
     * Membuat catatan baru.
     */
    public function create(array $data, User $creator): Note
    {
        return Note::create([
            'client_id'  => $data['client_id'] ?? null,
            'deal_id'    => $data['deal_id'] ?? null,
            'content'    => $data['content'],
            'note_type'  => $data['note_type'] ?? 'general',
            'created_by' => $creator->id,
        ]);
    }

    /**
     * Memperbarui catatan.
     */
    public function update(int $id, array $data): Note
    {
        $note = Note::findOrFail($id);

        $updateData = [];
        if (array_key_exists('client_id', $data)) $updateData['client_id'] = $data['client_id'];
        if (array_key_exists('deal_id', $data))   $updateData['deal_id']   = $data['deal_id'];
        if (array_key_exists('content', $data))   $updateData['content']   = $data['content'];
        if (array_key_exists('note_type', $data)) $updateData['note_type'] = $data['note_type'];

        if (!empty($updateData)) {
            $note->update($updateData);
        }

        return $note->refresh();
    }

    /**
     * Menghapus catatan.
     */
    public function delete(int $id): void
    {
        $note = Note::findOrFail($id);
        $note->delete();
    }
}
