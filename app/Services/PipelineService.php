<?php

namespace App\Services;

use App\Models\Pipeline;
use App\Models\PipelineStage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PipelineService
{
    /**
     * Mendapatkan daftar pipeline beserta stage-nya.
     */
    public function list(?string $search = null, bool $activeOnly = false)
    {
        $query = Pipeline::query()
            ->with(['stages' => function ($q) {
                $q->orderBy('position', 'asc');
            }])
            ->orderBy('id', 'asc');

        if ($activeOnly) {
            $query->where('is_active', true);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->get();
    }

    /**
     * Mendapatkan detail pipeline berdasarkan ID.
     */
    public function find(int $id): Pipeline
    {
        return Pipeline::with(['stages' => function ($q) {
            $q->orderBy('position', 'asc');
        }])->findOrFail($id);
    }

    /**
     * Membuat pipeline baru.
     */
    public function create(array $data): Pipeline
    {
        return Pipeline::create([
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active'   => $data['is_active'] ?? true,
        ]);
    }

    /**
     * Memperbarui data pipeline.
     */
    public function update(int $id, array $data): Pipeline
    {
        $pipeline = Pipeline::findOrFail($id);

        $updateData = [];
        if (array_key_exists('name', $data))        $updateData['name']        = $data['name'];
        if (array_key_exists('description', $data)) $updateData['description'] = $data['description'];
        if (array_key_exists('is_active', $data))   $updateData['is_active']   = (bool) $data['is_active'];

        if (!empty($updateData)) {
            $pipeline->update($updateData);
        }

        return $pipeline->refresh();
    }

    /**
     * Menghapus pipeline.
     */
    public function delete(int $id): void
    {
        $pipeline = Pipeline::findOrFail($id);
        
        if ($pipeline->deals()->exists()) {
            throw new \RuntimeException('Tidak dapat menghapus pipeline yang masih memiliki deal.');
        }

        $pipeline->delete();
    }

    /**
     * Membuat stage baru pada pipeline.
     */
    public function createStage(int $pipelineId, array $data): PipelineStage
    {
        $pipeline = Pipeline::findOrFail($pipelineId);

        $nextPosition = $data['position'] ?? ($pipeline->stages()->max('position') + 1);

        return PipelineStage::create([
            'pipeline_id' => $pipeline->id,
            'name'        => $data['name'],
            'slug'        => $data['slug'] ?? Str::slug($data['name']),
            'description' => $data['description'] ?? null,
            'position'    => $nextPosition,
            'is_terminal' => $data['is_terminal'] ?? false,
            'is_active'   => $data['is_active'] ?? true,
        ]);
    }

    /**
     * Memperbarui data stage.
     */
    public function updateStage(int $stageId, array $data): PipelineStage
    {
        $stage = PipelineStage::findOrFail($stageId);

        $updateData = [];
        if (array_key_exists('name', $data))        $updateData['name']        = $data['name'];
        if (array_key_exists('slug', $data))        $updateData['slug']        = $data['slug'];
        if (array_key_exists('description', $data)) $updateData['description'] = $data['description'];
        if (array_key_exists('position', $data))    $updateData['position']    = (int) $data['position'];
        if (array_key_exists('is_terminal', $data)) $updateData['is_terminal'] = (bool) $data['is_terminal'];
        if (array_key_exists('is_active', $data))   $updateData['is_active']   = (bool) $data['is_active'];

        if (!empty($updateData)) {
            $stage->update($updateData);
        }

        return $stage->refresh();
    }

    /**
     * Menghapus stage.
     */
    public function deleteStage(int $stageId): void
    {
        $stage = PipelineStage::findOrFail($stageId);

        if ($stage->deals()->exists()) {
            throw new \RuntimeException('Tidak dapat menghapus stage yang masih memiliki deal.');
        }

        $stage->delete();
    }

    /**
     * Mengatur ulang urutan position stage dalam satu pipeline.
     */
    public function reorderStages(int $pipelineId, array $stageOrders): void
    {
        DB::transaction(function () use ($pipelineId, $stageOrders) {
            foreach ($stageOrders as $item) {
                PipelineStage::where('id', $item['id'])
                    ->where('pipeline_id', $pipelineId)
                    ->update(['position' => (int) $item['position']]);
            }
        });
    }
}
