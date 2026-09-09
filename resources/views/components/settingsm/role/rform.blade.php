<!-- Add Role Modal -->
<div id="addRoleModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 overflow-y-auto">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl max-h-[95vh] overflow-hidden animate-fadeIn">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-200" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #3b82f6 100%);">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-shield text-white text-lg"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-white">Tambah Role</h3>
                </div>
                <button onclick="closeAddRoleModal()" class="text-white hover:text-gray-200 transition-colors p-2">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <form id="addRoleForm" action="{{ route('roles.store') }}" method="POST" class="overflow-y-auto max-h-[calc(95vh-140px)]">
            @csrf
            
            <div class="px-4 py-4 space-y-4">
                <!-- Role Information -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                        Informasi Role
                    </h4>
                    
                    <div class="space-y-3">
                        <!-- Role Name -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                Nama Role <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-tag text-gray-400 text-xs"></i>
                                </div>
                                <input type="text" id="addRoleName" name="role_name"
                                    class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    placeholder="Masukkan nama role" required>
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                Deskripsi
                            </label>
                            <div class="relative">
                                <div class="absolute top-2 left-0 pl-3 flex items-start pointer-events-none">
                                    <i class="fas fa-align-left text-gray-400 text-xs"></i>
                                </div>
                                <textarea id="addRoleDescription" name="description" rows="3"
                                    class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none"
                                    placeholder="Masukkan deskripsi role"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                <button type="button" onclick="closeAddRoleModal()" 
                    class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-100 transition-colors flex items-center gap-2">
                    <i class="fas fa-times"></i>
                    Batal
                </button>
                <button type="submit" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg text-xs font-medium hover:bg-blue-700 transition-colors flex items-center gap-2 shadow-lg shadow-blue-500/30">
                    <i class="fas fa-save"></i>
                    Simpan
                </button>
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

.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

input:focus, select:focus, textarea:focus {
    outline: none;
}
</style>

<script>
function openRoleModal() {
    document.getElementById('addRoleModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeAddRoleModal() {
    document.getElementById('addRoleModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    document.getElementById('addRoleForm').reset();
}

document.addEventListener('DOMContentLoaded', function() {
    const addModal = document.getElementById('addRoleModal');
    if (addModal) {
        addModal.addEventListener('click', function(e) {
            if (e.target === this) closeAddRoleModal();
        });
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const addModal = document.getElementById('addRoleModal');
        if (addModal && !addModal.classList.contains('hidden')) {
            closeAddRoleModal();
        }
    }
});
</script>