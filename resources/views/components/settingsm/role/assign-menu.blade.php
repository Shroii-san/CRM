<!-- Assign Menu Modal -->
<div id="assignMenuModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 overflow-y-auto">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-5xl max-h-[95vh] overflow-hidden animate-fadeIn">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-200" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #3b82f6 100%);">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-key text-white text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-white">Assign Permissions</h3>
                        <p class="text-sm text-white text-opacity-90 mt-0.5">Role: <span id="roleName" class="font-medium"></span></p>
                    </div>
                </div>
                <button onclick="closeAssignMenuModal()" class="text-white hover:text-gray-200 transition-colors p-2">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <form id="assignMenuForm" method="POST" class="overflow-y-auto max-h-[calc(95vh-140px)]">
            @csrf
            <input type="hidden" id="roleId" name="role_id">
            
            <div class="px-4 py-4">
                <!-- Permissions Table -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider sticky left-0 bg-gray-50 z-10">
                                        Menu
                                    </th>
                                    <th class="px-3 py-2 text-center text-xs font-semibold text-gray-700 uppercase">
                                        <i class="fas fa-eye text-blue-500 mb-1"></i>
                                        <div>View</div>
                                    </th>
                                    <th class="px-3 py-2 text-center text-xs font-semibold text-gray-700 uppercase">
                                        <i class="fas fa-plus-circle text-green-500 mb-1"></i>
                                        <div>Create</div>
                                    </th>
                                    <th class="px-3 py-2 text-center text-xs font-semibold text-gray-700 uppercase">
                                        <i class="fas fa-link text-purple-500 mb-1"></i>
                                        <div>Assign</div>
                                    </th>
                                    <th class="px-3 py-2 text-center text-xs font-semibold text-gray-700 uppercase">
                                        <i class="fas fa-edit text-orange-500 mb-1"></i>
                                        <div>Edit</div>
                                    </th>
                                    <th class="px-3 py-2 text-center text-xs font-semibold text-gray-700 uppercase">
                                        <i class="fas fa-trash text-red-500 mb-1"></i>
                                        <div>Delete</div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($menus as $menu)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 sticky left-0 bg-white hover:bg-gray-50 z-10">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-bars text-gray-400 text-xs"></i>
                                            <span class="text-sm font-medium text-gray-900">{{ $menu->nama_menu }}</span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="menus[{{ $menu->menu_id }}][]" value="view" 
                                                class="h-4 w-4 text-blue-600 focus:ring-2 focus:ring-blue-500 border-gray-300 rounded cursor-pointer transition-all">
                                        </label>
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="menus[{{ $menu->menu_id }}][]" value="create" 
                                                class="h-4 w-4 text-green-600 focus:ring-2 focus:ring-green-500 border-gray-300 rounded cursor-pointer transition-all">
                                        </label>
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="menus[{{ $menu->menu_id }}][]" value="assign" 
                                                class="h-4 w-4 text-purple-600 focus:ring-2 focus:ring-purple-500 border-gray-300 rounded cursor-pointer transition-all">
                                        </label>
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="menus[{{ $menu->menu_id }}][]" value="edit" 
                                                class="h-4 w-4 text-orange-600 focus:ring-2 focus:ring-orange-500 border-gray-300 rounded cursor-pointer transition-all">
                                        </label>
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="menus[{{ $menu->menu_id }}][]" value="delete" 
                                                class="h-4 w-4 text-red-600 focus:ring-2 focus:ring-red-500 border-gray-300 rounded cursor-pointer transition-all">
                                        </label>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Helper Text -->
                <div class="mt-3 flex items-start gap-2 text-xs text-gray-600 bg-blue-50 p-3 rounded-lg">
                    <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                    <p>Centang checkbox untuk memberikan permission yang sesuai untuk role ini. Pastikan sudah memilih permission dengan benar sebelum menyimpan.</p>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 flex justify-between items-center">
                <div class="flex items-center gap-2 text-xs text-gray-600">
                    <i class="fas fa-shield-alt text-green-500"></i>
                    <span>Total Menu: <strong>{{ count($menus) }}</strong></span>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closeAssignMenuModal()" 
                        class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-100 transition-colors flex items-center gap-2">
                        <i class="fas fa-times"></i>
                        Batal
                    </button>
                    <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg text-xs font-medium hover:bg-blue-700 transition-colors flex items-center gap-2 shadow-lg shadow-green-500/30">
                        <i class="fas fa-save"></i>
                        Simpan Permissions
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
@keyframes fadeIn { 
    from { 
        opacity: 0; 
        transform: scale(0.95) translateY(-10px); 
    } 
    to { 
        opacity: 1; 
        transform: scale(1) translateY(0); 
    } 
}

.animate-fadeIn { 
    animation: fadeIn 0.3s ease-out; 
}

.overflow-y-auto::-webkit-scrollbar,
.overflow-x-auto::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track,
.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb,
.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover,
.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

input:focus, select:focus, textarea:focus {
    outline: none;
}

/* Sticky column shadow effect */
.sticky.left-0::after {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    width: 10px;
    background: linear-gradient(to right, rgba(0,0,0,0.05), transparent);
    pointer-events: none;
}
</style>

<script>
function openAssignMenuModal(roleId, roleName) {
    const modal = document.getElementById('assignMenuModal');
    if (!modal) {
        console.error('assignMenuModal not found!');
        alert('Modal tidak ditemukan. Pastikan assign-menu.blade.php sudah di-include.');
        return;
    }

    // Set role info
    document.getElementById('roleId').value = roleId;
    document.getElementById('roleName').textContent = roleName;
    
    // Load existing permissions
    loadRolePermissions(roleId);
    
    // Update form action
    const form = document.getElementById('assignMenuForm');
    if (form) {
        form.action = `/roles/${roleId}/assign-menu`;
    }
    
    // Show modal
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeAssignMenuModal() {
    const modal = document.getElementById('assignMenuModal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

function loadRolePermissions(roleId) {
    // Reset all checkboxes
    document.querySelectorAll('#assignMenuForm input[type="checkbox"]').forEach(cb => {
        cb.checked = false;
    });
    
    // Load from global data
    if (window.rolePermissions && window.rolePermissions[roleId]) {
        const permissions = window.rolePermissions[roleId];
        
        permissions.forEach(perm => {
            const checkbox = document.querySelector(
                `input[name="menus[${perm.menu_id}][]"][value="${perm.permission_type}"]`
            );
            if (checkbox) {
                checkbox.checked = true;
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const assignModal = document.getElementById('assignMenuModal');
    if (assignModal) {
        assignModal.addEventListener('click', function(e) {
            if (e.target === this) closeAssignMenuModal();
        });
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const assignModal = document.getElementById('assignMenuModal');
        if (assignModal && !assignModal.classList.contains('hidden')) {
            closeAssignMenuModal();
        }
    }
});
</script>