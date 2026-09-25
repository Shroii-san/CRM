<?php

namespace App\Services;

use App\Models\Note;
use App\Models\User;

class NoteService
{
    /**
     * Mendapatkan daftar catatan dengan filter dan pagination.
     */
    public function list(
        ?User $user = null,
        ?int $clientId = null,
        ?int $dealId = null,
        ?string $noteType = null,
        ?string $search = null,
        int $perPage = 50,
        int $page = 1
    ) {
        $query = Note::query()
            ->with(['client', 'deal', 'createdBy'])
            ->orderBy('created_at', 'desc');

        if ($clientId) {
            $query->where('client_id', $clientId);
        }

        if ($dealId) {
            $query->where('deal_id', $dealId);
        }

        if ($noteType) {
            $query->where('note_type', $noteType);
        }

        if ($search) {
            $query->where('content', 'like', "%{$search}%");
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
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
