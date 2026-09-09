<!-- Role Management Table with Fixed Actions -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" style="margin: 0;">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Role Management</h3>
            <p class="text-sm text-gray-600 mt-1">Manage roles and their assigned permissions</p>
        </div>
        @if(auth()->user()->canAccess($currentMenuId, 'create'))
        <button onclick="openRoleModal()" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors flex items-center space-x-2">
            <i class="fas fa-plus"></i>
            <span>Add New Role</span>
        </button>
        @endif
    </div>

    <!-- Notifikasi -->
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
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Role Name</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Assigned Users</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($roles as $index => $role)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $roles->firstItem() + $loop->index }}</td>
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $role->role_name }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $role->description ?? 'No description' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">
                        {{ $role->users->count() }} Users
                    </td>
                    <td class="px-4 py-3 text-sm font-medium">
                        <div class="flex items-center space-x-2">
                            @if($role->role_name !== 'superadmin')
                                <!-- Edit Button -->
                                @if(auth()->user()->canAccess($currentMenuId, 'edit'))
                                <button onclick="openEditRoleModal('{{ $role->role_id }}', '{{ $role->role_name }}', '{{ $role->description }}')" 
                                        class="text-blue-600 hover:text-blue-900 p-1.5 rounded-lg hover:bg-blue-50 transition-colors" 
                                        title="Edit Role">
                                    <i class="fas fa-edit text-sm"></i>
                                </button>
                                @endif
                                
                                <!-- Assign Permissions Button -->
                                @if(auth()->user()->canAccess($currentMenuId, 'assign'))
                                <button onclick="openAssignMenuModal({{ $role->role_id }}, '{{ $role->role_name }}')"
                                        class="text-green-600 hover:text-green-900 p-1.5 rounded-lg hover:bg-green-50 transition-colors" 
                                        title="Assign Permissions">
                                    <i class="fas fa-shield-alt text-sm"></i>
                                </button>
                                @endif

                                <!-- Delete Button -->
                                @if(auth()->user()->canAccess($currentMenuId, 'delete'))
                                <form action="{{ route('roles.destroy', $role->role_id) }}" method="POST" class="inline-flex">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 p-1.5 rounded-lg hover:bg-red-50 transition-colors" 
                                            title="Delete Role" onclick="return confirm('Are you sure you want to delete this role?')">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </form>
                                @endif
                            @else
                                <!-- SuperAdmin - Protected -->
                                <span class="text-xs text-gray-500 italic px-3 py-2">🛡️ Protected Role</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>