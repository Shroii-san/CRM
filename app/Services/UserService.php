<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function list(
        ?string $search = null,
        ?int $roleId = null,
        ?bool $isActive = null,
        int $perPage = 50,
        int $page = 1,
        ?string $sortBy = 'created_at',
        ?string $sortDir = 'desc'
    ) {
        $params = array_filter([
            'search'    => $search,
            'role_id'   => $roleId,
            'is_active' => $isActive,
            'sort_by'   => $sortBy,
            'sort_dir'  => $sortDir,
        ], fn ($val) => $val !== null);

        return User::query()
            ->select('id', 'role_id', 'name', 'email', 'phone', 'is_active', 'created_at')
            ->with(['role:id,name'])
            ->applyFilters(
                params: $params,
                searchable: ['name', 'email', 'phone'],
                filterable: ['role_id', 'is_active'],
                sortable: ['id', 'name', 'email', 'created_at', 'updated_at'],
                defaultSortField: 'created_at',
                defaultSortDir: 'desc'
            )
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function create(array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
            unset($data['password']);
        }

        return User::create($data);
    }

    public function update(int $id, array $data)
    {
        $user = User::findOrFail($id);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
            unset($data['password']);
        }

        $user->update($data);
        return $user->refresh();
    }

    public function delete(int $id)
    {
        User::findOrFail($id)->delete();
    }
}
