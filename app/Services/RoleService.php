<?php

namespace App\Services;

use App\Models\Role;
use App\Models\Menu;
use Illuminate\Support\Facades\DB;

class RoleService
{
    /**
     * Mendapatkan daftar role dengan pagination & filter pencarian.
     */
    public function list(
        ?string $search = null,
        int $perPage = 50,
        int $page = 1
    ) {
        $query = Role::query()
            ->with(['permissions'])
            ->orderBy('name', 'asc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Membuat role baru.
     */
    public function create(array $data): Role
    {
        return Role::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);
    }

    /**
     * Memperbarui role yang ada.
     */
    public function update(int $id, array $data): Role
    {
        $role = Role::findOrFail($id);
        $role->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        return $role->refresh();
    }

    /**
     * Menghapus role.
     */
    public function delete(int $id): void
    {
        $role = Role::findOrFail($id);

        if ($role->name === 'superadmin') {
            throw new \InvalidArgumentException('Role superadmin tidak dapat dihapus.');
        }

        $role->delete();
    }

    /**
     * Memperbarui/Menugaskan matriks permission menu ke role.
     */
    public function assignPermissions(int $roleId, array $permissions): Role
    {
        $role = Role::findOrFail($roleId);

        DB::transaction(function () use ($role, $permissions) {
            $syncData = [];
            foreach ($permissions as $perm) {
                if (!isset($perm['menu_id'])) {
                    continue;
                }

                $syncData[$perm['menu_id']] = [
                    'can_view'   => (bool) ($perm['can_view'] ?? false),
                    'can_create' => (bool) ($perm['can_create'] ?? false),
                    'can_update' => (bool) ($perm['can_update'] ?? false),
                    'can_delete' => (bool) ($perm['can_delete'] ?? false),
                    'can_assign' => (bool) ($perm['can_assign'] ?? false),
                ];
            }

            $role->permissions()->sync($syncData);
        });

        return $role->load('permissions');
    }
}
