<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Services\PermissionService;

class SidebarComposer
{
    public function __construct(private PermissionService $permissionService)
    {
    }

    public function compose(View $view): void
    {
        $user = Auth::user();

        if (!$user) {
            $view->with('sidebarMenus', collect());
            return;
        }

        $menus = $this->permissionService->getSidebarMenus($user);
        $view->with('sidebarMenus', $menus);
    }
}
