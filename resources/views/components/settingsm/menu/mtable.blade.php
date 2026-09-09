<!-- Menu Management Table with Fixed Actions -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" style="margin: 0;">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Menu Management</h3>
            <p class="text-sm text-gray-600 mt-1">Manage application menus and navigation</p>
        </div>
        @if(auth()->user()->canAccess($currentMenuId, 'create'))
        <button onclick="openMenuModal()" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors flex items-center space-x-2">
            <i class="fas fa-plus"></i>
            <span>Add New Menu</span>
        </button>
        @endif
    </div>

    <!-- Notification -->
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mx-6 mt-4">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="overflow-x-auto" style="margin: 0; padding: 0;">
        <table class="min-w-full divide-y divide-gray-200" style="margin: 0; border-collapse: collapse;">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">No.</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Menu Name</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Route</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Icon</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Parent</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($menus as $index => $menu)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $menus->firstItem() + $loop->index }}</td>
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $menu->nama_menu }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $menu->route ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">
                        @if($menu->icon)
                            <i class="{{ $menu->icon }}"></i> {{ $menu->icon }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">
                        {{ $menu->parent?->nama_menu ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-sm font-medium">
                        <div class="flex items-center space-x-2">
                            @if(auth()->user()->canAccess($currentMenuId, 'edit'))
                            <button onclick="openEditMenuModal('{{ $menu->menu_id }}', '{{ $menu->nama_menu }}', '{{ $menu->route }}', '{{ $menu->icon }}', '{{ $menu->parent_id }}', '{{ $menu->order }}')" 
                                class="text-blue-600 hover:text-blue-900 p-1.5 rounded-lg hover:bg-blue-50 transition-colors" 
                                title="Edit Menu">
                                <i class="fas fa-edit text-sm"></i>
                            </button>
                            @endif

                            @if(auth()->user()->canAccess($currentMenuId, 'delete'))
                            <form action="{{ route('menus.destroy', $menu->menu_id) }}" method="POST" class="inline-flex">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 p-1.5 rounded-lg hover:bg-red-50 transition-colors" 
                                        title="Delete Menu" onclick="return confirm('Are you sure you want to delete this menu?')">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-20px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
</style>