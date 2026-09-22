<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\IndexUserRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\User\UserResource;
use App\Services\UserService;

class UserController extends Controller
{
    public function __construct(private UserService $userService)
    {
    }

    public function index(IndexUserRequest $request)
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
            ]);
    }

    public function store(StoreUserRequest $request)
    {
        $user = $this->userService->create($request->validated());

        return response()->json([
            'message' => 'User created successfully',
            'data' => UserResource::make($user)
        ], 201);
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $user = $this->userService->update($id, $request->validated());

        return response()->json([
            'message' => 'User updated successfully',
            'data' => UserResource::make($user)
        ]);
    }

    public function destroy($id)
    {
        $this->userService->delete($id);

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }
}
