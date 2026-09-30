<!-- Sidebar Overlay -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 sidebar-overlay opacity-0 pointer-events-none transition-all duration-300"></div>

<!-- Sidebar -->
<div id="sidebar" class="fixed left-0 top-0 h-full w-80 bg-white shadow-2xl z-40 sidebar closed flex flex-col pt-16">
    <!-- Scrollable Menu Area -->
    <div class="flex-1 overflow-y-auto p-6 pb-0">
        <!-- Sidebar Header -->
        <div class="flex items-center justify-between mb-8">
            <button id="sidebarClose" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <nav class="space-y-2 pb-6">
            @forelse($sidebarMenus as $menu)
                @php
                    $hasChildren = $menu->children && $menu->children->isNotEmpty();
                    $isParentActive = $hasChildren && $menu->children->contains(
                        fn($c) => $c->route && request()->routeIs($c->route)
                    );
                @endphp

                @if($hasChildren)
                    <!-- Parent Menu dengan Children -->
                    <div class="relative" x-data="{ open: {{ $isParentActive ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                                class="flex items-center justify-between w-full space-x-3 {{ $isParentActive ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600 hover:bg-indigo-50' }} rounded-lg px-3 py-2 transition-all duration-200">
                            <div class="flex items-center space-x-3">
                                <i class="{{ $menu->icon?->class_name ?? 'fas fa-circle' }} w-5"></i>
                                <span class="font-medium">{{ $menu->name }}</span>
                            </div>
                            <i class="fas fa-chevron-down transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                        </button>

                        <!-- Submenu -->
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2"
                             class="ml-6 mt-2 space-y-1">
                            @foreach($menu->children as $child)
                                <a href="{{ $child->route && Route::has($child->route) ? route($child->route) : '#' }}"
                                   class="flex items-center space-x-3 {{ request()->routeIs($child->route) ? 'text-indigo-600 bg-indigo-50 border-l-2 border-indigo-600' : 'text-gray-600 hover:text-indigo-600 hover:bg-indigo-50' }} rounded-lg px-3 py-2 transition-all duration-200">
                                    <i class="{{ $child->icon?->class_name ?? 'fas fa-circle text-xs' }} w-4"></i>
                                    <span class="font-medium">{{ $child->name }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <!-- Single Menu tanpa Children -->
                    <a href="{{ $menu->route && Route::has($menu->route) ? route($menu->route) : '#' }}"
                       class="flex items-center space-x-3 {{ request()->routeIs($menu->route) ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600 hover:bg-indigo-50' }} rounded-lg px-3 py-2 transition-all duration-200">
                        <i class="{{ $menu->icon?->class_name ?? 'fas fa-circle' }} w-5"></i>
                        <span class="font-medium">{{ $menu->name }}</span>
                    </a>
                @endif
            @empty
                <p class="text-gray-500 text-sm">Tidak ada menu tersedia</p>
            @endforelse
        </nav>
    </div>

    <!-- Sidebar Footer - Logout -->
    <div class="flex-shrink-0 p-6 bg-gray-50 border-t">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full flex items-center justify-center space-x-2 px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors duration-200">
                <i class="fas fa-sign-out-alt"></i>
                <span class="font-medium">Logout</span>
            </button>
        </form>
    </div>
</div>

<style>
    .sidebar-overlay {
        transition: opacity 0.3s ease-in-out;
    }
    .sidebar {
        transition: transform 0.3s ease-in-out;
    }
    .sidebar.open {
        transform: translateX(0);
    }
    .sidebar.closed {
        transform: translateX(-100%);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarClose = document.getElementById('sidebarClose');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    function openSidebar() {
        sidebar.classList.remove('closed');
        sidebar.classList.add('open');
        sidebarOverlay.classList.remove('opacity-0', 'pointer-events-none');
        sidebarOverlay.classList.add('opacity-100');
        document.body.classList.add('overflow-hidden');
    }

    function closeSidebar() {
        sidebar.classList.remove('open');
        sidebar.classList.add('closed');
        sidebarOverlay.classList.remove('opacity-100');
        sidebarOverlay.classList.add('opacity-0', 'pointer-events-none');
        document.body.classList.remove('overflow-hidden');
    }

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', openSidebar);
    }

    if (sidebarClose) {
        sidebarClose.addEventListener('click', closeSidebar);
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeSidebar);
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeSidebar();
        }
    });
});
</script>
