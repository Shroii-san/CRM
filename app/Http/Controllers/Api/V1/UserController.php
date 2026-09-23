<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\IndexUserRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\User\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(private UserService $userService)
    {
    }

    /**
     * Mendapatkan daftar user dengan pagination & filter (JSON API).
     */
    public function index(IndexUserRequest $request): JsonResponse
    {
        $users = $this->userService->list(
            search: $request->search,
            roleId: $request->role_id,
            isActive: $request->is_active,
            perPage: $request->per_page ?? 50,
            page: $request->page ?? 1,
        );

        return UserResource::collection($users)
            ->additional([
                'meta' => [
                    'total' => $users->total(),
                    'per_page' => $users->perPage(),
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                ]
            ])
            ->response();
    }

    /**
     * Membuat data user baru.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->create($request->validated());

        return response()->json([
            'message' => 'User created successfully',
            'data' => UserResource::make($user)
        ], 201);
    }

    /**
     * Memperbarui data user.
     */
    public function update(UpdateUserRequest $request, int|string $id): JsonResponse
    {
        $user = $this->userService->update((int) $id, $request->validated());

        return response()->json([
            'message' => 'User updated successfully',
            'data' => UserResource::make($user)
        ]);
    }

    /**
     * Menghapus data user.
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->userService->delete((int) $id);

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }
}
