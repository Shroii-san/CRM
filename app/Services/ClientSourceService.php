<?php

namespace App\Services;

use App\Models\ClientSource;

class ClientSourceService
{
    /**
     * Mendapatkan daftar sumber client dengan filter, search, sort, dan pagination.
     */
    public function list(
        ?string $search = null,
        ?bool $activeOnly = false,
        int $perPage = 50,
        int $page = 1,
        ?string $sortBy = 'name',
        ?string $sortDir = 'asc'
    ) {
        $params = array_filter([
            'search'    => $search,
            'is_active' => $activeOnly ? true : null,
            'sort_by'   => $sortBy,
            'sort_dir'  => $sortDir,
        ], fn ($val) => $val !== null);

        return ClientSource::query()
            ->applyFilters(
                params: $params,
                searchable: ['name', 'description'],
                filterable: ['is_active'],
                sortable: ['id', 'name', 'created_at'],
                defaultSortField: 'name',
                defaultSortDir: 'asc'
            )
            ->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Membuat sumber client baru.
     */
    public function create(array $data): ClientSource
    {
        return ClientSource::create([
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active'   => $data['is_active'] ?? true,
        ]);
    }

    /**
     * Memperbarui sumber client.
     */
    public function update(int $id, array $data): ClientSource
    {
        $source = ClientSource::findOrFail($id);
        $source->update([
            'name'        => $data['name'] ?? $source->name,
            'description' => $data['description'] ?? $source->description,
            'is_active'   => $data['is_active'] ?? $source->is_active,
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
