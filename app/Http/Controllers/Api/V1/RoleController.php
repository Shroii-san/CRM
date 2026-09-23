<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Http\Requests\Role\AssignPermissionsRoleRequest;
use App\Http\Resources\Role\RoleResource;
use App\Services\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct(private RoleService $roleService)
    {
    }

    /**
     * Mendapatkan daftar role dengan pagination & filter pencarian (JSON API).
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $perPage = (int) $request->input('per_page', 50);
        $page = (int) $request->input('page', 1);

        $roles = $this->roleService->list($search, $perPage, $page);

        return RoleResource::collection($roles)
            ->additional([
                'meta' => [
                    'total' => $roles->total(),
                    'per_page' => $roles->perPage(),
                    'current_page' => $roles->currentPage(),
                    'last_page' => $roles->lastPage(),
                ]
            ])
            ->response();
    }

    /**
     * Membuat role baru.
     */
    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = $this->roleService->create($request->validated());

        return response()->json([
            'message' => 'Role created successfully',
            'data' => RoleResource::make($role)
        ], 201);
    }

    /**
     * Memperbarui data role.
     */
    public function update(UpdateRoleRequest $request, int|string $id): JsonResponse
    {
        $role = $this->roleService->update((int) $id, $request->validated());

        return response()->json([
            'message' => 'Role updated successfully',
            'data' => RoleResource::make($role)
        ]);
    }

    /**
     * Menghapus role.
     */
    public function destroy(int|string $id): JsonResponse
    {
        try {
            $this->roleService->delete((int) $id);

            return response()->json([
                'message' => 'Role deleted successfully'
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Penugasan matriks hak akses permission menu ke role.
     */
    public function assignPermissions(AssignPermissionsRoleRequest $request, int|string $id): JsonResponse
    {
        $role = $this->roleService->assignPermissions((int) $id, $request->validated()['permissions']);

        return response()->json([
            'message' => 'Role permissions assigned successfully',
            'data' => RoleResource::make($role)
        ]);
    }
}
