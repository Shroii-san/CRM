<?php

namespace App\Services;

use App\Models\Industry;

class IndustryService
{
    /**
     * Mendapatkan daftar industri dengan filter, search, sort, dan pagination.
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

        return Industry::query()
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
     * Membuat industri baru.
     */
    public function create(array $data): Industry
    {
        return Industry::create([
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active'   => $data['is_active'] ?? true,
        ]);
    }

    /**
     * Memperbarui data industri.
     */
    public function update(int $id, array $data): Industry
    {
        $industry = Industry::findOrFail($id);
        $industry->update([
            'name'        => $data['name'] ?? $industry->name,
            'description' => $data['description'] ?? $industry->description,
            'is_active'   => $data['is_active'] ?? $industry->is_active,
        ]);

        return $industry->refresh();
    }

    /**
     * Menghapus industri.
     */
    public function delete(int $id): void
    {
        $industry = Industry::findOrFail($id);
        $industry->delete();
    }
}
