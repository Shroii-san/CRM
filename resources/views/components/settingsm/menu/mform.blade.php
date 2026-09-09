<!-- Add Menu Modal -->
<div id="addMenuModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 overflow-y-auto">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl max-h-[95vh] overflow-hidden animate-fadeIn">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-200" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #3b82f6 100%);">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-bars text-white text-lg"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-white">Tambah Menu</h3>
                </div>
                <button onclick="closeAddMenuModal()" class="text-white hover:text-gray-200 transition-colors p-2">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <form id="addMenuForm" action="{{ route('menus.store') }}" method="POST" class="overflow-y-auto max-h-[calc(95vh-140px)]">
            @csrf
            
            <div class="px-4 py-4 space-y-4">
                <!-- Menu Information -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                        Informasi Menu
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <!-- Menu Name -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                Nama Menu <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-tag text-gray-400 text-xs"></i>
                                </div>
                                <input type="text" id="addMenuName" name="nama_menu"
                                    class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    placeholder="Masukkan nama menu" required>
                            </div>
                        </div>

                        <!-- Route -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                Route
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-route text-gray-400 text-xs"></i>
                                </div>
                                <input type="text" id="addMenuRoute" name="route"
                                    class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    placeholder="e.g., users.index">
                            </div>
                        </div>

                        <!-- Order -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                Order
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-sort-numeric-down text-gray-400 text-xs"></i>
                                </div>
                                <input type="number" id="addMenuOrder" name="order"
                                    class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    placeholder="Urutan menu">
                            </div>
                        </div>

                        <!-- Parent Menu -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                Parent Menu
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-sitemap text-gray-400 text-xs"></i>
                                </div>
                                <select name="parent_id" id="editMenuParent"
                                    class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all appearance-none">                         
                                    <option value="">-- No Parent (Main Menu) --</option>
                                    @foreach(App\Models\Menu::whereNull('parent_id')->get() as $menu)
                                        <option value="{{ $menu->menu_id }}">{{ $menu->nama_menu }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Icon -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                Icon
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-icons text-gray-400 text-xs"></i>
                                </div>
                                <input type="text" id="addMenuIcon" name="icon"
                                    class="w-full pl-9 pr-12 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    placeholder="e.g., fas fa-users">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i id="addIconPreview" class="text-gray-400 text-sm"></i>
                                </div>
                            </div>
                            <div class="mt-2 p-2 bg-blue-50 rounded border border-blue-200">
                                <p class="text-xs text-blue-800 flex items-start">
                                    <i class="fas fa-info-circle mr-2 mt-0.5"></i>
                                    <span>Gunakan class FontAwesome (contoh: <code class="bg-blue-100 px-1 rounded">fas fa-users</code>)</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                <button type="button" onclick="closeAddMenuModal()" 
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

<!-- Styles -->
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

/* Custom scrollbar */
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

/* Smooth transitions */
input:focus, select:focus, textarea:focus {
    outline: none;
}

/* Responsive */
@media (max-width: 768px) {
    .grid-cols-2 {
        grid-template-columns: 1fr !important;
    }
}
</style>

<script>
function openMenuModal() {
    document.getElementById('addMenuModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeAddMenuModal() {
    document.getElementById('addMenuModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    document.getElementById('addMenuForm').reset();
    document.getElementById('addIconPreview').className = 'text-gray-400 text-sm';
}

// Icon preview untuk add modal
document.addEventListener('DOMContentLoaded', function() {
    const addIconInput = document.getElementById('addMenuIcon');
    if (addIconInput) {
        addIconInput.addEventListener('input', function(e) {
            const iconPreview = document.getElementById('addIconPreview');
            const iconValue = e.target.value.trim();
            iconPreview.className = iconValue ? `${iconValue} text-sm` : 'text-gray-400 text-sm';
        });
    }
});

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    const modal = document.getElementById('addMenuModal');
    if (e.target === modal) {
        closeAddMenuModal();
    }
});

// Close modal with ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('addMenuModal');
        if (modal && !modal.classList.contains('hidden')) {
            closeAddMenuModal();
        }
    }
});
</script>