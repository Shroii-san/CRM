<?php

namespace App\Http\Controllers;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\User\UserResource;
use App\Services\UserService;

class UserController extends Controller
{
    /**
     * List semua user
     */
    public function index(UserService $userService, )
    {
        $users = $userService->getAllUsers()->paginate(10);
        return UserResource::collection($users);
    }



    public function store(StoreUserRequest $request, UserService $userService)
    {
        $data = $request->validated();
        $user = $userService->createUser($data);

        return response()->json([
            'message' => 'User created successfully',
            'data' => UserResource::make($user)
        ], 201);
    }

    public function update(UpdateUserRequest $request, UserService $userService, $id)
    {
        $data = $request->validated();
        $user = $userService->updateUser($id, $data);

        return response()->json([
            'message' => 'User updated successfully',
            'data' => UserResource::make($user)
        ]);
    }

    public function destroy(UserService $userService, $id)
    {
        $userService->deleteUser($id);

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }
}
