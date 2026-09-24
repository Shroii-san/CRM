<?php

namespace App\Services;

use App\Models\ClientSource;

class ClientSourceService
{
    /**
     * Mendapatkan daftar sumber client.
     */
    public function list(?string $search = null, ?bool $activeOnly = false, int $perPage = 50, int $page = 1)
    {
        $query = ClientSource::query()->orderBy('name', 'asc');

        if ($activeOnly) {
            $query->where('is_active', true);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Membuat sumber client baru.
     */
    public function create(array $data): ClientSource
    {
        return ClientSource::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    /**
     * Memperbarui sumber client.
     */
    public function update(int $id, array $data): ClientSource
    {
        $source = ClientSource::findOrFail($id);
        $source->update([
            'name' => $data['name'] ?? $source->name,
            'description' => $data['description'] ?? $source->description,
            'is_active' => $data['is_active'] ?? $source->is_active,
        ]);

        return $source->refresh();
    }

    /**
     * Menghapus sumber client.
     */
    public function delete(int $id): void
    {
        $source = ClientSource::findOrFail($id);
        $source->delete();
    }
}
