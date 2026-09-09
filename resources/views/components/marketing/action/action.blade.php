<!-- Modal Add Sales -->
<div id="addSalesModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 overflow-y-auto">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl max-h-[95vh] overflow-hidden animate-fadeIn">
        
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-200" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #3b82f6 100%);">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-plus text-white text-lg"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-white">Tambah Sales Baru</h3>
                </div>
                <button onclick="closeAddSalesModal()" class="text-white hover:text-gray-200 transition-colors p-2">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <form id="addSalesForm" action="{{ route('marketing.sales.store') }}" method="POST" class="overflow-y-auto max-h-[calc(95vh-140px)]">
            @csrf
            
            <div class="px-4 py-4 space-y-4">
                <!-- Basic Information -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-id-card text-blue-500 mr-2"></i>
                        Informasi Dasar
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <!-- Nama -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400 text-xs"></i>
                                </div>
                                <input type="text" name="username"
                                    class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    placeholder="Masukkan nama lengkap" required>
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
                                <input type="email" name="email"
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
                                <input type="text" name="phone"
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
                                <input type="date" name="birth_date"
                                    class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    max="{{ date('Y-m-d') }}">
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400 text-xs"></i>
                                </div>
                                <input type="password" name="password"
                                    class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    placeholder="Minimal 6 karakter" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Address Section with Collapsible -->
                <div class="bg-gradient-to-br bg-blue-50 from-blue-50 to-indigo-50 rounded-lg border border-blue-200 overflow-hidden">
                    <!-- Header - Always Visible -->
                    <div class="p-3 cursor-pointer hover:bg-blue-100 transition-colors" onclick="toggleSalesAddressSection()">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-semibold text-gray-800 flex items-center">
                                <i class="fas fa-map-marker-alt text-indigo-600 mr-2"></i>
                                Informasi Alamat
                            </h4>
                            <div class="flex items-center gap-2">
                                <span id="sales-address-status" class="text-xs text-gray-500">Belum diisi</span>
                                <i id="sales-address-toggle-icon" class="fas fa-chevron-down text-gray-600 transition-transform duration-300"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Collapsible Content -->
                    <div id="sales-address-content" class="hidden">
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
                                        <select id="create-province" name="province_id" 
                                            class="cascade-province w-full pl-9 pr-8 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all appearance-none bg-white"
                                            onchange="checkSalesAddressCompletion()"
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
                                        <select id="create-regency" name="regency_id" 
                                            class="cascade-regency w-full pl-9 pr-8 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all appearance-none bg-white"
                                            onchange="checkSalesAddressCompletion()"
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
                                        <select id="create-district" name="district_id" 
                                            class="cascade-district w-full pl-9 pr-8 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all appearance-none bg-white"
                                            onchange="checkSalesAddressCompletion()"
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
                                        <select id="create-village" name="village_id" 
                                            class="cascade-village w-full pl-9 pr-8 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all appearance-none bg-white"
                                            onchange="checkSalesAddressCompletion()"
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
                                        <textarea id="address" name="address" rows="2"
                                            class="w-full pl-9 pr-3 py-2 text-sm bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all resize-none"
                                            placeholder="Contoh: Jl. Merdeka No. 123, RT 01/RW 02"
                                            oninput="checkSalesAddressCompletion()"></textarea>
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
                    Simpan Data
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

/* Loading state */
.btn-loading {
    position: relative;
    pointer-events: none;
}
.btn-loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 16px;
    height: 16px;
    border: 2px solid transparent;
    border-top: 2px solid currentColor;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}
@keyframes spin {
    to { transform: translate(-50%, -50%) rotate(360deg); }
}

/* Form validation styling */
.error {
    border-color: #ef4444 !important;
}
.error:focus {
    border-color: #ef4444 !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
}
.success {
    border-color: #10b981;
}
.error-message {
    color: #ef4444;
    font-size: 0.75rem;
    margin-top: 0.25rem;
}
</style>

<script>
// ========== ADDRESS SECTION TOGGLE ==========
function toggleSalesAddressSection() {
    const content = document.getElementById('sales-address-content');
    const icon = document.getElementById('sales-address-toggle-icon');
    
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
    } else {
        content.classList.add('hidden');
        icon.style.transform = 'rotate(0deg)';
    }
}

function checkSalesAddressCompletion() {
    const province = document.getElementById('create-province').value;
    const address = document.getElementById('address').value.trim();
    const statusText = document.getElementById('sales-address-status');
    const content = document.getElementById('sales-address-content');
    const icon = document.getElementById('sales-address-toggle-icon');
    
    if (province && address) {
        statusText.textContent = 'Sudah diisi';
        statusText.classList.remove('text-gray-500');
        statusText.classList.add('text-green-600', 'font-medium');
        
        // Auto collapse setelah 800ms
        setTimeout(() => {
            if (!content.classList.contains('hidden')) {
                content.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }, 800);
    } else {
        statusText.textContent = 'Belum diisi';
        statusText.classList.remove('text-green-600', 'font-medium');
        statusText.classList.add('text-gray-500');
    }
}

// ========== MODAL FUNCTIONS ==========
function openAddSalesModal() {
    document.getElementById('addSalesModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    setTimeout(() => {
        const firstInput = document.querySelector('#addSalesModal input[name="username"]');
        if (firstInput) firstInput.focus();
    }, 300);
}

function closeAddSalesModal() {
    document.getElementById('addSalesModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    
    document.getElementById('addSalesForm').reset();
    clearValidation();
    
    // Reset address collapse state
    const content = document.getElementById('sales-address-content');
    const icon = document.getElementById('sales-address-toggle-icon');
    const statusText = document.getElementById('sales-address-status');
    
    content.classList.add('hidden');
    icon.style.transform = 'rotate(0deg)';
    statusText.textContent = 'Belum diisi';
    statusText.classList.remove('text-green-600', 'font-medium');
    statusText.classList.add('text-gray-500');
}

// ========== FORM VALIDATION ==========
function validateForm() {
    const form = document.getElementById('addSalesForm');
    const inputs = form.querySelectorAll('input[required], select[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            showFieldError(input, 'Field ini wajib diisi');
            isValid = false;
        } else {
            clearFieldError(input);
        }
    });
    
    // Email validation
    const email = document.querySelector('#addSalesModal input[name="email"]');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (email && email.value && !emailRegex.test(email.value)) {
        showFieldError(email, 'Format email tidak valid');
        isValid = false;
    }
    
    // Password validation
    const password = document.querySelector('#addSalesModal input[name="password"]');
    if (password && password.value && password.value.length < 6) {
        showFieldError(password, 'Password minimal 6 karakter');
        isValid = false;
    }
    
    return isValid;
}

function showFieldError(field, message) {
    field.classList.add('error');
    field.classList.remove('success');
    
    const existingError = field.parentNode.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.textContent = message;
    field.parentNode.appendChild(errorDiv);
}

function clearFieldError(field) {
    field.classList.remove('error');
    field.classList.add('success');
    
    const errorMessage = field.parentNode.querySelector('.error-message');
    if (errorMessage) {
        errorMessage.remove();
    }
}

function clearValidation() {
    const fields = document.querySelectorAll('#addSalesModal input, #addSalesModal textarea, #addSalesModal select');
    fields.forEach(field => {
        field.classList.remove('error', 'success');
    });
    
    const errorMessages = document.querySelectorAll('#addSalesModal .error-message');
    errorMessages.forEach(msg => msg.remove());
}

// ========== FORM SUBMISSION ==========
document.getElementById('addSalesForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!validateForm()) {
        return;
    }
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalHTML = submitBtn.innerHTML;
    
    submitBtn.classList.add('btn-loading');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Menyimpan...';
    submitBtn.disabled = true;
    
    // Submit the form (or use your AJAX logic here)
    this.submit();
});

// ========== EVENT LISTENERS ==========
// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !document.getElementById('addSalesModal').classList.contains('hidden')) {
        closeAddSalesModal();
    }
});

// Close modal on backdrop click
document.getElementById('addSalesModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAddSalesModal();
    }
});

// Real-time validation
document.addEventListener('input', function(e) {
    if (e.target.matches('#addSalesModal input, #addSalesModal textarea, #addSalesModal select')) {
        clearFieldError(e.target);
    }
});
</script>