<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait Filterable
{
    /**
     * Composable scope for search, filtering, and sorting.
     *
     * @param Builder $query
     * @param Request|array $params
     * @param array $searchable Fields to search with LIKE %term% (supports dot notation for relationships)
     * @param array $filterable Allowed exact filter keys (e.g., ['status', 'source_id', 'is_active'])
     * @param array $sortable Allowed sort fields (e.g., ['id', 'name', 'created_at', 'updated_at'])
     * @param string $defaultSortField
     * @param string $defaultSortDir
     */
    public function scopeApplyFilters(
        Builder $query,
        Request|array $params,
        array $searchable = [],
        array $filterable = [],
        array $sortable = [],
        string $defaultSortField = 'created_at',
        string $defaultSortDir = 'desc'
    ): Builder {
        $inputs = $params instanceof Request ? $params->all() : $params;

        // 1. Search Logic
        $search = $inputs['search'] ?? null;
        if (!empty($search) && !empty($searchable)) {
            $query->where(function (Builder $q) use ($search, $searchable) {
                foreach ($searchable as $index => $field) {
                    if (str_contains($field, '.')) {
                        [$relation, $relationField] = explode('.', $field, 2);
                        $method = $index === 0 ? 'whereHas' : 'orWhereHas';
                        $q->$method($relation, function (Builder $rq) use ($relationField, $search) {
                            $rq->where($relationField, 'like', "%{$search}%");
                        });
                    } else {
                        $method = $index === 0 ? 'where' : 'orWhere';
                        $q->$method($field, 'like', "%{$search}%");
                    }
                }
            });
        }

        // 2. Filter Logic
        $filters = $inputs['filter'] ?? [];
        if (is_array($filters)) {
            foreach ($filters as $key => $value) {
                if (in_array($key, $filterable, true) && $value !== null && $value !== '') {
                    if (is_array($value)) {
                        $query->whereIn($key, $value);
                    } elseif ($value === 'true' || $value === 'false') {
                        $query->where($key, filter_var($value, FILTER_VALIDATE_BOOLEAN));
                    } else {
                        $query->where($key, $value);
                    }
                }
            }
        }

        // Direct filter key support (e.g., ?is_active=1 or ?source_id=2)
        foreach ($filterable as $field) {
            if (!isset($filters[$field]) && isset($inputs[$field]) && $inputs[$field] !== null && $inputs[$field] !== '') {
                $val = $inputs[$field];
                if (is_array($val)) {
                    $query->whereIn($field, $val);
                } else {
                    $query->where($field, $val);
                }
            }
        }

        // 3. Sorting Logic
        $sortBy = $inputs['sort_by'] ?? null;
        $sortDir = strtolower($inputs['sort_dir'] ?? '');

        // Support sort=-created_at syntax
        if ($sortBy && str_starts_with($sortBy, '-')) {
            $sortBy = ltrim($sortBy, '-');
            $sortDir = 'desc';
        }

        if (empty($sortDir) || !in_array($sortDir, ['asc', 'desc'], true)) {
            $sortDir = strtolower($defaultSortDir);
        }

        if ($sortBy && (empty($sortable) || in_array($sortBy, $sortable, true))) {
            $query->orderBy($sortBy, $sortDir);
        } elseif (!empty($defaultSortField)) {
            $query->orderBy($defaultSortField, $sortDir);
        }

        return $query;
    }
}
