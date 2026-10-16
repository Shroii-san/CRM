<?php

namespace App\Services;

use App\Models\Interaction;
use App\Models\User;

class InteractionService
{
    /**
     * Mendapatkan daftar interaksi/komunikasi dengan filter, search, sort, dan pagination.
     */
    public function list(
        ?User $user = null,
        ?int $clientId = null,
        ?int $dealId = null,
        ?int $contactId = null,
        ?int $type = null,
        ?int $status = null,
        ?string $search = null,
        int $perPage = 50,
        int $page = 1,
        ?string $sortBy = 'created_at',
        ?string $sortDir = 'desc'
    ) {
        $params = array_filter([
            'client_id'               => $clientId,
            'deal_id'                => $dealId,
            'organization_contact_id' => $contactId,
            'type'                    => $type,
            'status'                  => $status,
            'search'                  => $search,
            'sort_by'                 => $sortBy,
            'sort_dir'                => $sortDir,
        ], fn ($val) => $val !== null);

        return Interaction::query()
            ->with(['client', 'deal', 'organizationContacts'])
            ->applyFilters(
                params: $params,
                searchable: ['subject', 'description', 'summary'],
                filterable: ['client_id', 'deal_id', 'organization_contact_id', 'type', 'status', 'performed_by'],
                sortable: ['id', 'subject', 'start_at', 'end_at', 'created_at', 'updated_at'],
                defaultSortField: 'created_at',
                defaultSortDir: 'desc'
            )
            ->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Detail interaksi berdasarkan ID.
     */
    public function find(int $id): Interaction
    {
        return Interaction::with(['client', 'deal', 'organizationContacts'])->findOrFail($id);
    }

    /**
     * Membuat interaksi baru.
     */
    public function create(array $data, User $performer): Interaction
    {
        return Interaction::create([
            'client_id'               => $data['client_id'],
            'deal_id'                => $data['deal_id'] ?? null,
            'organization_contact_id' => $data['organization_contact_id'] ?? null,
            'type'                    => $data['type'] ?? 1, // 1 = chat
            'subject'                 => $data['subject'],
            'description'            => $data['description'] ?? null,
            'summary'                 => $data['summary'] ?? null,
            'status'                  => $data['status'] ?? 1, // 1 = scheduled
            'start_at'                => $data['start_at'] ?? null,
            'end_at'                  => $data['end_at'] ?? null,
            'performed_by'            => $data['performed_by'] ?? $performer->id,
            'external_reference'      => $data['external_reference'] ?? null,
        ]);
    }

    /**
     * Memperbarui data interaksi.
     */
    public function update(int $id, array $data): Interaction
    {
        $interaction = Interaction::findOrFail($id);

        $updateData = [];
        if (array_key_exists('client_id', $data))               $updateData['client_id']               = $data['client_id'];
        if (array_key_exists('deal_id', $data))                 $updateData['deal_id']                 = $data['deal_id'];
        if (array_key_exists('organization_contact_id', $data)) $updateData['organization_contact_id'] = $data['organization_contact_id'];
        if (array_key_exists('type', $data))                    $updateData['type']                    = (int) $data['type'];
        if (array_key_exists('subject', $data))                 $updateData['subject']                 = $data['subject'];
        if (array_key_exists('description', $data))             $updateData['description']             = $data['description'];
        if (array_key_exists('summary', $data))                 $updateData['summary']                 = $data['summary'];
        if (array_key_exists('status', $data))                  $updateData['status']                  = (int) $data['status'];
        if (array_key_exists('start_at', $data))                $updateData['start_at']                = $data['start_at'];
        if (array_key_exists('end_at', $data))                  $updateData['end_at']                  = $data['end_at'];
        if (array_key_exists('performed_by', $data))            $updateData['performed_by']            = $data['performed_by'];
        if (array_key_exists('external_reference', $data))      $updateData['external_reference']      = $data['external_reference'];

        if (!empty($updateData)) {
            $interaction->update($updateData);
        }

        return $interaction->refresh();
    }

    /**
     * Menghapus interaksi.
     */
    public function delete(int $id): void
    {
        $interaction = Interaction::findOrFail($id);
        $interaction->delete();
    }
}
