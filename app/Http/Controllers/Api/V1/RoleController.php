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

        return $this->paginatedResponse($roles, RoleResource::class, 'Roles retrieved successfully');
    }

    /**
     * Membuat role baru.
     */
    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = $this->roleService->create($request->validated());

        return $this->successResponse(RoleResource::make($role), 'Role created successfully', 201);
    }

    /**
     * Memperbarui data role.
     */
    public function update(UpdateRoleRequest $request, int|string $id): JsonResponse
    {
        $role = $this->roleService->update((int) $id, $request->validated());

        return $this->successResponse(RoleResource::make($role), 'Role updated successfully');
    }

    /**
     * Menghapus role.
     */
    public function destroy(int|string $id): JsonResponse
    {
        try {
            $this->roleService->delete((int) $id);

            return $this->successResponse(null, 'Role deleted successfully');
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    /**
     * Penugasan matriks hak akses permission menu ke role.
     */
    public function assignPermissions(AssignPermissionsRoleRequest $request, int|string $id): JsonResponse
    {
        $role = $this->roleService->assignPermissions((int) $id, $request->validated()['permissions']);

        return $this->successResponse(RoleResource::make($role), 'Role permissions assigned successfully');
    }
}
