<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Web\RoleController as WebRoleController;
use App\Http\Controllers\Api\V1\RoleController as ApiRoleController;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Http\Requests\Role\AssignPermissionsRoleRequest;
use App\Services\RoleService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct(
        private RoleService $roleService,
        private WebRoleController $webRoleController,
        private ApiRoleController $apiRoleController
    ) {
    }

    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax() || $request->has('search')) {
            return $this->apiRoleController->index($request);
        }

        return $this->webRoleController->index($request);
    }

    public function store(StoreRoleRequest $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return $this->apiRoleController->store($request);
        }

        $this->roleService->create($request->validated());
        return redirect()->route('role')->with('success', 'Role berhasil ditambahkan');
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return $this->apiRoleController->update($request, (int) $id);
        }

        $this->roleService->update((int) $id, $request->validated());
        return redirect()->route('role')->with('success', 'Role berhasil diperbarui');
    }

    public function destroy($id)
    {
        try {
            $this->roleService->delete((int) $id);
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['message' => 'Role deleted successfully']);
            }
            return redirect()->route('role')->with('success', 'Role berhasil dihapus');
        } catch (\Exception $e) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }
            return redirect()->route('role')->with('error', $e->getMessage());
        }
    }

    public function assignMenu(AssignPermissionsRoleRequest $request, $id)
    {
        $this->roleService->assignPermissions((int) $id, $request->validated()['permissions']);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Permissions assigned successfully']);
        }

        return redirect()->route('role')->with('success', 'Hak akses berhasil diperbarui');
    }

    public function search(Request $request)
    {
        return $this->apiRoleController->index($request);
    }
}