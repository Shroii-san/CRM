<?php

namespace App\Providers;

use App\Models\Menu;
use App\Policies\MenuPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Menu::class => MenuPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
