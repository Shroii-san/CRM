<?php

namespace App\Services;

use App\Models\Industry;

class IndustryService
{
    /**
     * Mendapatkan daftar industri.
     */
    public function list(?string $search = null, ?bool $activeOnly = false, int $perPage = 50, int $page = 1)
    {
        $query = Industry::query()->orderBy('name', 'asc');

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
     * Membuat industri baru.
     */
    public function create(array $data): Industry
    {
        return Industry::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    /**
     * Memperbarui data industri.
     */
    public function update(int $id, array $data): Industry
    {
        $industry = Industry::findOrFail($id);
        $industry->update([
            'name' => $data['name'] ?? $industry->name,
            'description' => $data['description'] ?? $industry->description,
            'is_active' => $data['is_active'] ?? $industry->is_active,
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
