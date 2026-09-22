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
    ) {
        $query = User::query()
            ->select('id', 'role_id', 'name', 'email', 'phone', 'is_active', 'created_at')
            ->with(['role:id,name'])
            ->orderBy('created_at', 'desc');

        // Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($roleId !== null) {
            $query->where('role_id', $roleId);
        }

        // Filter by status
        if ($isActive !== null) {
            $query->where('is_active', $isActive);
        }

        // Pagination (backend)
        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function create(array $data)
    {
        if (isset($data['password'])) {
            $data['password_hash'] = Hash::make($data['password']);
            unset($data['password']);
        }

        return User::create($data);
    }

    public function update(int $id, array $data)
    {
        $user = User::findOrFail($id);

        if (isset($data['password'])) {
            $data['password_hash'] = Hash::make($data['password']);
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
