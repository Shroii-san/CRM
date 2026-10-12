<?php

namespace App\Services;

use App\Models\Deal;
use App\Models\DealStageHistory;
use App\Models\PipelineStage;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DealService
{
    /**
     * Mendapatkan daftar deal/transaksi dengan filter, search, sort, dan pagination.
     */
    public function list(
        ?User $user = null,
        ?int $pipelineId = null,
        ?int $stageId = null,
        ?int $clientId = null,
        ?string $search = null,
        int $perPage = 50,
        int $page = 1,
        ?string $sortBy = 'created_at',
        ?string $sortDir = 'desc'
    ) {
        $params = array_filter([
            'pipeline_id'      => $pipelineId,
            'current_stage_id' => $stageId,
            'client_id'        => $clientId,
            'search'           => $search,
            'sort_by'          => $sortBy,
            'sort_dir'         => $sortDir,
        ], fn ($val) => $val !== null);

        return Deal::query()
            ->with(['client.person', 'client.organization', 'pipeline', 'currentStage', 'assignedUser'])
            ->applyFilters(
                params: $params,
                searchable: ['name', 'description'],
                filterable: ['pipeline_id', 'current_stage_id', 'client_id', 'assigned_user_id', 'status', 'priority', 'is_active'],
                sortable: ['id', 'name', 'value', 'priority', 'expected_closed_at', 'created_at', 'updated_at'],
                defaultSortField: 'created_at',
                defaultSortDir: 'desc'
            )
            ->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Mendapatkan detail deal berdasarkan ID.
     */
    public function find(int $id): Deal
    {
        return Deal::with([
            'client.person',
            'client.organization',
            'pipeline',
            'currentStage',
            'assignedUser',
            'dealStageHistories.fromStage',
            'dealStageHistories.toStage',
            'dealStageHistories.changedBy'
        ])->findOrFail($id);
    }

    /**
     * Membuat data deal baru dan mencatat riwayat stage pertamanya.
     */
    public function create(array $data, User $creator): Deal
    {
        return DB::transaction(function () use ($data, $creator) {
            $deal = Deal::create([
                'name'               => $data['name'],
                'client_id'          => $data['client_id'],
                'pipeline_id'        => $data['pipeline_id'],
                'current_stage_id'   => $data['current_stage_id'],
                'assigned_user_id'   => $data['assigned_user_id'] ?? $creator->id,
                'description'        => $data['description'] ?? null,
                'currency'           => $data['currency'] ?? 'IDR',
                'value'              => $data['value'] ?? 0,
                'status'             => $data['status'] ?? 1,
                'priority'           => $data['priority'] ?? 1,
                'expected_closed_at' => $data['expected_closed_at'],
                'actual_closed_at'   => $data['actual_closed_at'] ?? null,
                'is_active'          => $data['is_active'] ?? true,
            ]);

            // Catat history stage awal
            DealStageHistory::create([
                'deal_id'       => $deal->id,
                'from_stage_id' => null,
                'to_stage_id'   => $deal->current_stage_id,
                'changed_by'    => $creator->id,
            ]);

            return $deal->load(['client', 'pipeline', 'currentStage', 'assignedUser']);
        });
    }

    /**
     * Memperbarui data deal.
     */
    public function update(int $id, array $data): Deal
    {
        $deal = Deal::findOrFail($id);

        $updateData = [];
        if (array_key_exists('name', $data))               $updateData['name']               = $data['name'];
        if (array_key_exists('client_id', $data))          $updateData['client_id']          = $data['client_id'];
        if (array_key_exists('pipeline_id', $data))        $updateData['pipeline_id']        = $data['pipeline_id'];
        if (array_key_exists('assigned_user_id', $data))   $updateData['assigned_user_id']   = $data['assigned_user_id'];
        if (array_key_exists('description', $data))        $updateData['description']        = $data['description'];
        if (array_key_exists('currency', $data))           $updateData['currency']           = $data['currency'];
        if (array_key_exists('value', $data))              $updateData['value']              = $data['value'];
        if (array_key_exists('status', $data))             $updateData['status']             = (int) $data['status'];
        if (array_key_exists('priority', $data))           $updateData['priority']           = (int) $data['priority'];
        if (array_key_exists('expected_closed_at', $data)) $updateData['expected_closed_at'] = $data['expected_closed_at'];
        if (array_key_exists('actual_closed_at', $data))   $updateData['actual_closed_at']   = $data['actual_closed_at'];
        if (array_key_exists('is_active', $data))          $updateData['is_active']          = (bool) $data['is_active'];

        if (!empty($updateData)) {
            $deal->update($updateData);
        }

        return $deal->refresh();
    }

    /**
     * Memindahkan stage deal dan mencatat ke deal_stage_histories.
     */
    public function moveStage(int $dealId, int $newStageId, User $changedBy): Deal
    {
        return DB::transaction(function () use ($dealId, $newStageId, $changedBy) {
            $deal = Deal::findOrFail($dealId);
            $oldStageId = $deal->current_stage_id;

            if ($oldStageId === $newStageId) {
                return $deal;
            }

            $newStage = PipelineStage::findOrFail($newStageId);

            $dealUpdate = [
                'current_stage_id' => $newStageId,
            ];

            // Jika pindah ke terminal stage, update tanggal aktual penutupan
            if ($newStage->is_terminal && !$deal->actual_closed_at) {
                $dealUpdate['actual_closed_at'] = now()->toDateString();
            }

            $deal->update($dealUpdate);

            // Catat ke riwayat
            DealStageHistory::create([
                'deal_id'       => $deal->id,
                'from_stage_id' => $oldStageId,
                'to_stage_id'   => $newStageId,
                'changed_by'    => $changedBy->id,
            ]);

            return $deal->refresh()->load(['client', 'pipeline', 'currentStage', 'assignedUser', 'dealStageHistories']);
        });
    }

    /**
     * Menghapus deal.
     */
    public function delete(int $id): void
    {
        $deal = Deal::findOrFail($id);
        $deal->delete();
    }
}
