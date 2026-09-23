<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Web\MenuController as WebMenuController;
use App\Http\Controllers\Api\V1\MenuController as ApiMenuController;
use App\Http\Requests\Menu\StoreMenuRequest;
use App\Http\Requests\Menu\UpdateMenuRequest;
use App\Services\MenuService;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function __construct(
        private MenuService $menuService,
        private WebMenuController $webMenuController,
        private ApiMenuController $apiMenuController
    ) {
    }

    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax() || $request->has('search')) {
            return $this->apiMenuController->index($request);
        }

        return $this->webMenuController->index($request);
    }

    public function store(StoreMenuRequest $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return $this->apiMenuController->store($request);
        }

        $this->menuService->create($request->validated());
        return redirect()->route('menu')->with('success', 'Menu berhasil ditambahkan');
    }

    public function update(UpdateMenuRequest $request, $id)
    {
        try {
            if ($request->wantsJson() || $request->ajax()) {
                return $this->apiMenuController->update($request, (int) $id);
            }

            $this->menuService->update((int) $id, $request->validated());
            return redirect()->route('menu')->with('success', 'Menu berhasil diperbarui');
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }
            return redirect()->route('menu')->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->menuService->delete((int) $id);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['message' => 'Menu deleted successfully']);
        }

        return redirect()->route('menu')->with('success', 'Menu berhasil dihapus');
    }

    public function search(Request $request)
    {
        return $this->apiMenuController->index($request);
    }
}