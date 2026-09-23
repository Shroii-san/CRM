<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Menu\StoreMenuRequest;
use App\Http\Requests\Menu\UpdateMenuRequest;
use App\Http\Resources\Menu\MenuResource;
use App\Services\MenuService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function __construct(private MenuService $menuService)
    {
    }

    /**
     * Mendapatkan daftar menu dengan pagination & filter pencarian (JSON API).
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $parentOnly = $request->boolean('parent_only');
        $perPage = (int) $request->input('per_page', 50);
        $page = (int) $request->input('page', 1);

        $menus = $this->menuService->list($search, $parentOnly, $perPage, $page);

        return MenuResource::collection($menus)
            ->additional([
                'meta' => [
                    'total' => $menus->total(),
                    'per_page' => $menus->perPage(),
                    'current_page' => $menus->currentPage(),
                    'last_page' => $menus->lastPage(),
                ]
            ])
            ->response();
    }

    /**
     * Mendapatkan struktur pohon menu (Menu Tree).
     */
    public function tree(): JsonResponse
    {
        $tree = $this->menuService->getMenuTree();

        return response()->json([
            'data' => MenuResource::collection($tree)
        ]);
    }

    /**
     * Membuat menu baru.
     */
    public function store(StoreMenuRequest $request): JsonResponse
    {
        $menu = $this->menuService->create($request->validated());

        return response()->json([
            'message' => 'Menu created successfully',
            'data' => MenuResource::make($menu)
        ], 201);
    }

    /**
     * Memperbarui data menu.
     */
    public function update(UpdateMenuRequest $request, int|string $id): JsonResponse
    {
        try {
            $menu = $this->menuService->update((int) $id, $request->validated());

            return response()->json([
                'message' => 'Menu updated successfully',
                'data' => MenuResource::make($menu)
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Menghapus menu.
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->menuService->delete((int) $id);

        return response()->json([
            'message' => 'Menu deleted successfully'
        ]);
    }
}
