<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function createUser($data): User
    {
        $data['password_hash'] = Hash::make($data['password']);
        $data['is_active'] = $data['is_active'] ?? true;

        return User::create($data);
    }

    public function updateUser($id, $data): User
    {
        $user = User::findOrFail($id);

        if (!empty($data['password'])) {
            $data['password_hash'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['is_active'] = isset($data['is_active']) ? 1 : 0;

        $user->update($data);
        return $user;
    }

    public function deleteUser($id): bool
    {
        $user = User::findOrFail($id);
        return $user->delete();
    }

    public function getUser($id): User
    {
        return User::findOrFail($id);
    }

    public function getAllUsers()
    {
        return User::orderBy('name', 'asc')->get();
    }
}
