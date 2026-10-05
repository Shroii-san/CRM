<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseService
{
    /**
     * Return model class name
     * Contoh: return Organization::class;
     */
    abstract protected function getModel(): string;

    /**
     * Kolom yang boleh di-search
     * Contoh: return ['name', 'email', 'phone'];
     */
    abstract protected function getSearchableColumns(): array;

    /**
     * Define filter dengan tipe filternya
     * Contoh:
     * return [
     *     'industry_id' => 'equal',
     *     'status' => 'equal',
     *     'created_at' => 'between'
     * ];
     */
    abstract protected function getFilterDefinitions(): array;

    /**
     * Relations yang di-load otomatis
     * Contoh: return ['industry', 'province'];
     */
    protected function getWithRelations(): array
    {
        return [];
    }

    protected function getSortableColumns(): array
    {
        return ['id', 'created_at', 'updated_at', 'name'];
    }

    /**
     * Main list method - standard untuk semua service
     *
     * @param string|null $search
     * @param array|null $filters
     * @param int $perPage
     * @param int $page
     * @param string|null $sortBy
     * @param string|null $sortDir
     * @return LengthAwarePaginator
     */
    public function list(
        ?string $search = null,
        ?array $filters = null,
        int $perPage = 50,
        int $page = 1,
        ?string $sortBy = 'id',
        ?string $sortDir = 'asc'
    ): LengthAwarePaginator {

        $query = $this->buildQuery($search, $filters, $sortBy, $sortDir);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Build query dengan search + filter + sort
     */
    private function buildQuery(
        ?string $search,
        ?array $filters,
        ?string $sortBy,
        ?string $sortDir
    ): Builder {

        $query = $this->getModel()::query();

        if ($relations = $this->getWithRelations()) {
            $query->with($relations);
        }

        if ($search) {
            $query = $this->applySearch($query, $search);
        }

        if ($filters && is_array($filters)) {
            $query = $this->applyFilters($query, $filters);
        }

        $this->applySorting($query, $sortBy, $sortDir);

        return $query;
    }

    private function applySearch(Builder $query, string $keyword): Builder
    {
        return $query->where(function (Builder $q) use ($keyword) {
            $searchColumns = $this->getSearchableColumns();

            foreach ($searchColumns as $column) {
                $q->orWhere($column, 'like', "%{$keyword}%");
            }
        });
    }

    private function applyFilters(Builder $query, array $filters): Builder
    {
        $definitions = $this->getFilterDefinitions();

        foreach ($filters as $key => $value) {
            // Skip jika filter tidak ada di definitions atau value kosong
            if (!isset($definitions[$key]) || $value === null || $value === '') {
                continue;
            }

            $filterType = $definitions[$key];

            match ($filterType) {
                'equal' => $query->where($key, $value),
                'like' => $query->where($key, 'like', "%{$value}%"),
                'in' => $query->whereIn($key, (array) $value),
                'between' => $query->whereBetween($key, (array) $value),
                'exists' => $value ? $query->whereNotNull($key) : $query->whereNull($key),
                default => null
            };
        }

        return $query;
    }

    private function applySorting(Builder $query, ?string $sortBy, ?string $sortDir): void
    {
        $sortableColumns = $this->getSortableColumns();
        $sortDir = strtolower($sortDir ?? 'asc') === 'desc' ? 'desc' : 'asc';

        if ($sortBy && in_array($sortBy, $sortableColumns)) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $query->orderBy('id', 'asc');
        }
    }

    public function getFilterOptions(): array
    {
        return [];
    }
}
