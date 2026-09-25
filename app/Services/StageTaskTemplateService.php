<?php

namespace App\Services;

use App\Models\StageTaskTemplate;

class StageTaskTemplateService
{
    /**
     * Mendapatkan daftar template tugas per stage.
     */
    public function list(?int $stageId = null, bool $activeOnly = false)
    {
        $query = StageTaskTemplate::query()
            ->with('pipelineStage')
            ->orderBy('id', 'asc');

        if ($stageId) {
            $query->where('stage_id', $stageId);
        }

        if ($activeOnly) {
            $query->where('is_active', true);
        }

        return $query->get();
    }

    /**
     * Mendapatkan detail template tugas berdasarkan ID.
     */
    public function find(int $id): StageTaskTemplate
    {
        return StageTaskTemplate::with('pipelineStage')->findOrFail($id);
    }

    /**
     * Membuat template tugas stage baru.
     */
    public function create(array $data): StageTaskTemplate
    {
        return StageTaskTemplate::create([
            'stage_id'        => $data['stage_id'],
            'name'            => $data['name'],
            'description'     => $data['description'] ?? null,
            'priority'        => $data['priority'] ?? 1,
            'due_offset_days' => $data['due_offset_days'] ?? null,
            'is_required'     => $data['is_required'] ?? false,
            'is_active'       => $data['is_active'] ?? true,
        ]);
    }

    /**
     * Memperbarui template tugas stage.
     */
    public function update(int $id, array $data): StageTaskTemplate
    {
        $template = StageTaskTemplate::findOrFail($id);

        $updateData = [];
        if (array_key_exists('stage_id', $data))        $updateData['stage_id']        = $data['stage_id'];
        if (array_key_exists('name', $data))            $updateData['name']            = $data['name'];
        if (array_key_exists('description', $data))     $updateData['description']     = $data['description'];
        if (array_key_exists('priority', $data))        $updateData['priority']        = (int) $data['priority'];
        if (array_key_exists('due_offset_days', $data)) $updateData['due_offset_days'] = $data['due_offset_days'];
        if (array_key_exists('is_required', $data))     $updateData['is_required']     = (bool) $data['is_required'];
        if (array_key_exists('is_active', $data))       $updateData['is_active']       = (bool) $data['is_active'];

        if (!empty($updateData)) {
            $template->update($updateData);
        }

        return $template->refresh();
    }

    /**
     * Menghapus template tugas stage.
     */
    public function delete(int $id): void
    {
        $template = StageTaskTemplate::findOrFail($id);
        $template->delete();
    }
}
