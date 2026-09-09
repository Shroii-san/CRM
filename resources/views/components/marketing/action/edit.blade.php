<!-- Modal Edit User -->
<div id="editUserModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 overflow-y-auto">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl max-h-[95vh] overflow-hidden animate-fadeIn">
        
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-200" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #3b82f6 100%);">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-edit text-white text-lg"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-white">Edit User</h3>
                </div>
                <button onclick="closeEditSalesModal()" class="text-white hover:text-gray-200 transition-colors p-2">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <form id="editUserForm" action="#" method="POST" class="overflow-y-auto max-h-[calc(95vh-140px)]">
            @csrf
            @method('PUT')
            <input type="hidden" id="editUserId" name="user_id">
            
            <div class="px-4 py-4 space-y-4">
                <!-- Basic Information -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-id-card text-blue-500 mr-2"></i>
                        Informasi Dasar
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <!-- Username -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                Username <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400 text-xs"></i>
                                </div>
                                <input type="text" id="editUsername" name="username"
                                    class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    placeholder="Masukkan username" required>
                            </div>
                        </div>
                        
                        <!-- Email -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-gray-400 text-xs"></i>
                                </div>
                                <input type="email" id="editEmail" name="email"
                                    class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    placeholder="email@example.com" required>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                No. Telepon
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-phone text-gray-400 text-xs"></i>
                                </div>
                                <input type="text" id="editPhone" name="phone"
                                    class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    placeholder="08xxxxxxxxxx">
                            </div>
                        </div>

                        <!-- Birth Date -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                Tanggal Lahir
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-calendar text-gray-400 text-xs"></i>
                                </div>
                                <input type="date" id="editBirthDate" name="birth_date"
                                    class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    max="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Address Section with Collapsible -->
                <div class="bg-gradient-to-br bg-blue-50 from-blue-50 to-indigo-50 rounded-lg border border-blue-200 overflow-hidden">
                    <!-- Header - Always Visible -->
                    <div class="p-3 cursor-pointer hover:bg-blue-100 transition-colors" onclick="toggleEditAddressSection()">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-semibold text-gray-800 flex items-center">
                                <i class="fas fa-map-marker-alt text-indigo-600 mr-2"></i>
                                Informasi Alamat
                            </h4>
                            <div class="flex items-center gap-2">
                                <span id="edit-address-status" class="text-xs text-gray-500">Belum diisi</span>
                                <i id="edit-address-toggle-icon" class="fas fa-chevron-down text-gray-600 transition-transform duration-300"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Collapsible Content -->
                    <div id="edit-address-content" class="hidden">
                        <div class="px-3 pb-3 space-y-3">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <!-- Provinsi -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                        Provinsi <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-map text-gray-400 text-xs"></i>
                                        </div>
                                        <select id="edit-province" name="province_id"
                                            class="w-full pl-9 pr-8 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all appearance-none bg-white"
                                            onchange="checkEditAddressCompletion()"
                                            required>
                                            <option value="">-- Pilih Provinsi --</option>
                                            @foreach($provinces as $province)
                                                <option value="{{ $province->id }}">{{ $province->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Kabupaten/Kota -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                        Kabupaten/Kota <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-city text-gray-400 text-xs"></i>
                                        </div>
                                        <select id="edit-regency" name="regency_id"
                                            class="w-full pl-9 pr-8 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all appearance-none bg-white"
                                            onchange="checkEditAddressCompletion()"
                                            required disabled>
                                            <option value="">-- Pilih Kabupaten/Kota --</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Kecamatan -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                        Kecamatan <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-map-signs text-gray-400 text-xs"></i>
                                        </div>
                                        <select id="edit-district" name="district_id"
                                            class="w-full pl-9 pr-8 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all appearance-none bg-white"
                                            onchange="checkEditAddressCompletion()"
                                            required disabled>
                                            <option value="">-- Pilih Kecamatan --</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Kelurahan/Desa -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                        Kelurahan/Desa <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-home text-gray-400 text-xs"></i>
                                        </div>
                                        <select id="edit-village" name="village_id"
                                            class="w-full pl-9 pr-8 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all appearance-none bg-white"
                                            onchange="checkEditAddressCompletion()"
                                            required disabled>
                                            <option value="">-- Pilih Kelurahan/Desa --</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Detail Alamat -->
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                        Detail Alamat
                                    </label>
                                    <div class="relative">
                                        <div class="absolute top-2 left-3 pointer-events-none">
                                            <i class="fas fa-map-marked-alt text-gray-400 text-xs"></i>
                                        </div>
                                        <textarea id="editAlamat" name="address" rows="2"
                                            class="w-full pl-9 pr-3 py-2 text-sm border bg-white border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all resize-none"
                                            placeholder="Contoh: Jl. Merdeka No. 123, RT 01/RW 02"
                                            oninput="checkEditAddressCompletion()"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                <button type="submit" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg text-xs font-medium hover:bg-blue-700 transition-colors flex items-center gap-2 shadow-lg shadow-blue-500/30">
                    <i class="fas fa-save"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<style>
@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.95) translateY(-20px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}
.animate-fadeIn { animation: fadeIn 0.3s ease-out; }

select::-ms-expand { display: none; }
input:focus, select:focus, textarea:focus { outline: none; }
select:disabled {
    background-color: #f3f4f6;
    cursor: not-allowed;
    opacity: 0.6;
}

/* Custom Scrollbar */
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
</style>

<script>
// ========== EDIT MODAL SCRIPT - FIXED VERSION ==========

let editCascadeInstance = null;

// ========== ADDRESS SECTION TOGGLE ==========
function toggleEditAddressSection() {
    const content = document.getElementById('edit-address-content');
    const icon = document.getElementById('edit-address-toggle-icon');
    
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
    } else {
        content.classList.add('hidden');
        icon.style.transform = 'rotate(0deg)';
    }
}

function checkEditAddressCompletion() {
    const province = document.getElementById('edit-province')?.value;
    const address = document.getElementById('editAlamat')?.value?.trim();
    const statusText = document.getElementById('edit-address-status');
    
    if (province && address) {
        statusText.textContent = 'Sudah diisi';
        statusText.classList.remove('text-gray-500');
        statusText.classList.add('text-green-600', 'font-medium');
    } else {
        statusText.textContent = 'Belum diisi';
        statusText.classList.remove('text-green-600', 'font-medium');
        statusText.classList.add('text-gray-500');
    }
}

// ========== MODAL FUNCTIONS - IMPROVED ==========
async function openEditSalesModal(user_id, username, email, phone, birth_date, address, provinceId, regencyId, districtId, villageId) {
    console.log('🔓 Opening edit modal with data:', {
        user_id, username, email, phone, birth_date, 
        address, provinceId, regencyId, districtId, villageId
    });
    
    // Fill form fields
    document.getElementById('editUserId').value = user_id;
    document.getElementById('editUsername').value = username;
    document.getElementById('editEmail').value = email;
    document.getElementById('editPhone').value = phone ?? '';
    document.getElementById('editBirthDate').value = birth_date ?? '';
    document.getElementById('editAlamat').value = address ?? '';
    
    // Update form action
    const form = document.getElementById('editUserForm');
    form.action = `/marketing/sales/${user_id}`;
    
    // Show modal
    document.getElementById('editUserModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Initialize cascade dengan data wilayah
    await initEditAddressCascade(provinceId, regencyId, districtId, villageId);
}

async function initEditAddressCascade(provinceId, regencyId, districtId, villageId) {
    console.log('🚀 Initializing EDIT cascade with data:', {
        provinceId, regencyId, districtId, villageId
    });
    
    // Destroy previous instance if exists
    if (editCascadeInstance) {
        console.log('🗑️ Destroying previous cascade instance');
        editCascadeInstance.destroy();
        editCascadeInstance = null;
    }
    
    // Wait a bit for DOM cleanup
    await new Promise(resolve => setTimeout(resolve, 100));
    
    // Create new instance
    editCascadeInstance = new AddressCascade({
        provinceId: 'edit-province',
        regencyId: 'edit-regency',
        districtId: 'edit-district',
        villageId: 'edit-village'
    });
    
    console.log('✅ Cascade instance created');
    
    // Load cascade with values using the improved method
    if (provinceId && provinceId !== 'null' && provinceId !== '') {
        try {
            console.log('📥 Loading cascade with values...');
            await editCascadeInstance.loadWithValues(provinceId, regencyId, districtId, villageId);
            
            // Check address completion after loading
            checkEditAddressCompletion();
            
            console.log('✅ Cascade loaded and values set successfully');
        } catch (error) {
            console.error('❌ Error loading cascade with values:', error);
        }
    } else {
        console.log('ℹ️ No province ID provided, skipping cascade loading');
    }
}

function closeEditSalesModal() {
    console.log('🔒 Closing edit modal');
    
    const modal = document.getElementById('editUserModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    
    // Destroy cascade instance
    if (editCascadeInstance) {
        console.log('🗑️ Destroying cascade on close');
        editCascadeInstance.destroy();
        editCascadeInstance = null;
    }
    
    // Reset form
    document.getElementById('editUserForm').reset();
    
    // Reset dropdowns to initial state
    document.getElementById('edit-regency').innerHTML = '<option value="">-- Pilih Kabupaten/Kota --</option>';
    document.getElementById('edit-regency').disabled = true;
    
    document.getElementById('edit-district').innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
    document.getElementById('edit-district').disabled = true;
    
    document.getElementById('edit-village').innerHTML = '<option value="">-- Pilih Kelurahan/Desa --</option>';
    document.getElementById('edit-village').disabled = true;
    
    // Reset address collapse state
    const content = document.getElementById('edit-address-content');
    const icon = document.getElementById('edit-address-toggle-icon');
    const statusText = document.getElementById('edit-address-status');
    
    content.classList.add('hidden');
    icon.style.transform = 'rotate(0deg)';
    statusText.textContent = 'Belum diisi';
    statusText.classList.remove('text-green-600', 'font-medium');
    statusText.classList.add('text-gray-500');
}

// ========== EVENT LISTENERS ==========
// Close modal when clicking outside
document.addEventListener('click', function(e) {
    const modal = document.getElementById('editUserModal');
    if (e.target === modal) {
        closeEditSalesModal();
    }
});

// Close modal with ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('editUserModal');
        if (modal && !modal.classList.contains('hidden')) {
            closeEditSalesModal();
        }
    }
});

// Form validation
document.getElementById('editUserForm')?.addEventListener('submit', function(e) {
    const username = document.getElementById('editUsername').value.trim();
    const email = document.getElementById('editEmail').value.trim();
    
    if (!username || !email) {
        e.preventDefault();
        alert('Nama dan Email wajib diisi!');
        return false;
    }
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
    }
});

console.log('✅ Edit modal script loaded');
</script>