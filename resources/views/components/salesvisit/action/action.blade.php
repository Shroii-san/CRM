@props(['currentMenuId', 'salesUsers', 'provinces', 'types' => []])

<!-- Tambah Kunjungan Modal -->
<div id="visitModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 overflow-y-auto" style="height: 100vh; width: 100vw; max-height: 100vh; max-width: 100vw;">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[95vh] overflow-hidden animate-fadeIn">
        
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-200" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #3b82f6 100%);">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-route text-white text-lg"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-white">Tambah Kunjungan Sales</h2>
                </div>
                <button onclick="closeVisitModal()" class="text-white hover:text-gray-200 transition-colors p-2">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <form action="{{ route('salesvisit.store') }}" method="POST" class="overflow-y-auto max-h-[calc(95vh-140px)]" id="visitForm">
            @csrf

            @php
                $userRole = strtolower(auth()->user()->role->role_name ?? '');
                $isSales = $userRole === 'sales';
            @endphp

            <div class="px-4 py-4 space-y-4">
                <!-- Basic Information -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-id-card text-blue-500 mr-2"></i>
                        Informasi Dasar
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <!-- Sales -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                Sales <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user-tie text-gray-400 text-xs"></i>
                                </div>
                                @if($isSales)
                                    <input type="text" 
                                        value="{{ auth()->user()->username }} - {{ auth()->user()->email }}"
                                        class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed"
                                        readonly>
                                    <input type="hidden" name="sales_id" value="{{ auth()->user()->user_id }}">
                                @else
                                    <select name="sales_id" id="visit-sales"
                                        class="w-full pl-9 pr-8 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all appearance-none bg-white"
                                        required>
                                        <option value="">-- Pilih Sales --</option>
                                        @foreach($salesUsers as $sales)
                                            <option value="{{ $sales->user_id }}">{{ $sales->username }} - {{ $sales->email }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Visit Date -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                Visit Date <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-calendar text-gray-400 text-xs"></i>
                                </div>
                                <input type="date" name="visit_date"
                                    class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    required>
                            </div>
                        </div>

                        <!-- Company with Dropdown + Add New -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                Company
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                    <i class="fas fa-building text-gray-400 text-xs"></i>
                                </div>
                                
                                <!-- Hidden input untuk company_id yang akan dikirim ke server -->
                                <input type="hidden" name="company_id" id="create-company-id">
                                
                                <!-- Input search untuk dropdown -->
                                <input type="text" 
                                    id="create-company-search" 
                                    placeholder="Ketik atau pilih company..."
                                    autocomplete="off"
                                    class="w-full pl-9 pr-20 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                
                                <!-- Dropdown list -->
                                <div id="create-company-dropdown" 
                                    class="hidden absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                    <div id="create-company-options" class="py-1"></div>
                                </div>
                            </div>
                        </div>

                        <!-- PIC Name with Dropdown (Muncul setelah Company dipilih) -->
                        <div id="pic-input-container" class="hidden">
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                PIC Name <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                    <i class="fas fa-user text-gray-400 text-xs"></i>
                                </div>
                                
                                <!-- Hidden inputs -->
                                <input type="hidden" name="pic_id" id="create-pic-id">
                                <input type="hidden" name="pic_name" id="create-pic-name-hidden">
                                
                                <!-- Input search untuk dropdown -->
                                <input type="text" 
                                    id="create-pic-search" 
                                    placeholder="Ketik atau pilih PIC..."
                                    autocomplete="off"
                                    class="w-full pl-9 pr-20 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                
                                <!-- Dropdown list -->
                                <div id="create-pic-dropdown" 
                                    class="hidden absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                    <div id="create-pic-options" class="py-1"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Address Section with Collapsible -->
                <div class="bg-gradient-to-br bg-blue-50 from-blue-50 to-indigo-50 rounded-lg border border-blue-200 overflow-hidden">
                    <!-- Header - Always Visible -->
                    <div class="p-3 cursor-pointer hover:bg-blue-100 transition-colors" onclick="toggleAddressSection()">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-semibold text-gray-800 flex items-center">
                                <i class="fas fa-map-marker-alt text-indigo-600 mr-2"></i>
                                Informasi Lokasi
                            </h4>
                            <div class="flex items-center gap-2">
                                <span id="address-status" class="text-xs text-gray-500">Belum diisi</span>
                                <i id="address-toggle-icon" class="fas fa-chevron-down text-gray-600 transition-transform duration-300"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Collapsible Content -->
                    <div id="address-content" class="hidden">
                        <div class="px-3 pb-3 space-y-3">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                        Province <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-map text-gray-400 text-xs"></i>
                                        </div>
                                        <select name="province_id" id="create-province"
                                            class="w-full pl-9 pr-8 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all appearance-none bg-white"
                                            onchange="checkAddressCompletion()"
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

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Regency</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-city text-gray-400 text-xs"></i>
                                        </div>
                                        <select name="regency_id" id="create-regency"
                                            class="w-full pl-9 pr-8 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all appearance-none bg-white"
                                            onchange="checkAddressCompletion()">
                                            <option value="">-- Pilih Kabupaten/Kota --</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1.5">District</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-map-signs text-gray-400 text-xs"></i>
                                        </div>
                                        <select name="district_id" id="create-district"
                                            class="w-full pl-9 pr-8 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all appearance-none bg-white"
                                            onchange="checkAddressCompletion()">
                                            <option value="">-- Pilih Kecamatan --</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Village</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-home text-gray-400 text-xs"></i>
                                        </div>
                                        <select name="village_id" id="create-village"
                                            class="w-full pl-9 pr-8 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all appearance-none bg-white"
                                            onchange="checkAddressCompletion()">
                                            <option value="">-- Pilih Kelurahan/Desa --</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Address</label>
                                    <div class="relative">
                                        <div class="absolute top-2 left-3 pointer-events-none">
                                            <i class="fas fa-map-marked-alt text-gray-400 text-xs"></i>
                                        </div>
                                        <textarea name="address" id="create-address" rows="2"
                                            class="w-full pl-9 pr-3 py-2 text-sm bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all resize-none"
                                            placeholder="Contoh: Jl. Merdeka No. 123, RT 01/RW 02"
                                            oninput="checkAddressCompletion()"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Visit Details -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-cog text-blue-500 mr-2"></i>
                        Detail Kunjungan
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div class="md:col-span-3">
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                <i class="fas fa-bullseye mr-1"></i>Purpose <span class="text-red-500">*</span>
                            </label>
                            <textarea name="visit_purpose" rows="2"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none"
                                placeholder="Masukkan tujuan kunjungan..." required></textarea>
                        </div>

                        <div class="md:col-span-1">
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                <i class="fas fa-tasks mr-1"></i>Follow Up
                            </label>
                            <div class="flex gap-2">
                                <label class="relative flex-1 cursor-pointer group">
                                    <input type="radio" name="is_follow_up" value="1" class="peer sr-only">
                                    <div class="w-full px-2 py-2 text-xs font-medium text-gray-600 bg-white border border-gray-300 rounded-lg transition-all peer-checked:bg-blue-500 peer-checked:border-blue-500 peer-checked:text-white group-hover:border-blue-400 flex items-center justify-center gap-1">
                                        <i class="fas fa-check text-[10px]"></i>
                                        <span>Ya</span>
                                    </div>
                                </label>
                                <label class="relative flex-1 cursor-pointer group">
                                    <input type="radio" name="is_follow_up" value="0" class="peer sr-only" checked>
                                    <div class="w-full px-2 py-2 text-xs font-medium text-gray-600 bg-white border border-gray-300 rounded-lg transition-all peer-checked:bg-red-500 peer-checked:border-red-500 peer-checked:text-white group-hover:border-red-400 flex items-center justify-center gap-1">
                                        <i class="fas fa-times text-[10px]"></i>
                                        <span>Tidak</span>
                                    </div>
                                </label>
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
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>


<!-- Add Company Modal - FIXED VERSION -->
<div id="addCompanyModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[60] p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-hidden animate-modal-in">
        <div style="background: linear-gradient(to right, #4f46e5, #7c3aed); padding: 1rem 1.25rem;">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-white">Tambah Perusahaan Baru</h3>
                    <p class="text-xs text-indigo-100 mt-0.5">Lengkapi formulir berikut untuk menambahkan perusahaan</p>
                </div>
                <button type="button" onclick="closeAddCompanyModal()" 
                    class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>

        <div class="overflow-y-auto max-h-[calc(90vh-120px)]" style="background-color: #f3f4f6; padding: 1rem;">
            <!-- ✅ IMPORTANT: No action attribute, handled by JavaScript -->
            <form id="addCompanyForm" class="space-y-4">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-2">
                            Nama Perusahaan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="company_name" 
                            class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                            placeholder="Masukkan nama perusahaan" required>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-2">
                            Jenis Perusahaan <span class="text-red-500">*</span>
                        </label>
                        <select name="company_type_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm appearance-none bg-white"
                                required>
                            <option value="">-- Pilih Jenis --</option>
                            @foreach($types as $type)
                                <option value="{{ $type->company_type_id }}">{{ $type->type_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-2">Tier</label>
                        <select name="tier" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm appearance-none bg-white">
                            <option value="">-- Pilih Tier --</option>
                            <option value="A">Tier A</option>
                            <option value="B">Tier B</option>
                            <option value="C">Tier C</option>
                            <option value="D">Tier D</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm appearance-none bg-white">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-2">Deskripsi</label>
                        <textarea name="description" rows="3" 
                                  class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm resize-none"
                                  placeholder="Tambahkan keterangan tentang perusahaan..."></textarea>
                    </div>
                </div>
                
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                    <button type="submit" 
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 transition-colors flex items-center gap-2 shadow-lg shadow-indigo-500/30">
                        <i class="fas fa-save"></i>
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



<!-- Add PIC Modal - FIXED VERSION -->
<div id="addPICModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[70] p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden animate-modal-in">
        <div style="background: linear-gradient(to right, #4f46e5, #7c3aed); padding: 1rem 1.25rem;">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-white">Tambah PIC Baru</h3>
                    <p class="text-xs text-indigo-100 mt-0.5">Lengkapi formulir berikut untuk menambahkan PIC</p>
                </div>
                <button type="button" onclick="closeAddPICModal()" 
                    class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>

        <div class="overflow-y-auto max-h-[calc(90vh-120px)]" style="background-color: #f3f4f6; padding: 1rem;">
            <!-- ✅ IMPORTANT: No action attribute, handled by JavaScript -->
            <form id="addPICForm" class="space-y-4">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="company_id" id="pic-form-company-id">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-2">
                            Nama PIC <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="pic_name" 
                            class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                            placeholder="Masukkan nama PIC" required>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-2">Posisi PIC</label>
                        <input type="text" name="pic_position" 
                            class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                            placeholder="Contoh: Manager">
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-2">Telepon</label>
                        <input type="text" name="pic_phone" 
                            class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                            placeholder="Contoh: 08123456789">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="pic_email" 
                            class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                            placeholder="Contoh: pic@company.com">
                    </div>
                </div>
                
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                    <!-- ✅ IMPORTANT: type="button" to prevent form submission -->
                    <button type="button" onclick="closeAddPICModal()" 
                            class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <!-- ✅ type="submit" will be handled by JavaScript preventDefault -->
                    <button type="submit" 
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-blue-700 transition-colors flex items-center gap-2 shadow-lg shadow-blue-500/30">
                        <i class="fas fa-save"></i>
                        Simpan PIC
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
@keyframes modal-in {
    from { opacity: 0; transform: scale(0.95) translateY(-20px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}
.animate-modal-in { animation: modal-in 0.3s ease-out; }
</style>
<script>
    // Fungsi buka modal tambah PIC
function openAddPICModal(companyId, companyName = '') {
    if (!companyId) {
        alert('Pilih perusahaan terlebih dahulu!');
        return;
    }
    
    // Set company_id di form
    document.getElementById('pic-form-company-id').value = companyId;
    
    // Reset form
    document.getElementById('addPICForm').reset();
    
    // Tampilkan modal
    document.getElementById('addPICModal').classList.remove('hidden');
    
    console.log('Membuka modal tambah PIC untuk company:', companyId, companyName);
}

// Fungsi tutup modal tambah PIC
function closeAddPICModal() {
    document.getElementById('addPICModal').classList.add('hidden');
    document.getElementById('addPICForm').reset();
}



// Fungsi notifikasi
//function showNotification(type, message) {
    // Anda bisa menggunakan notifikasi library atau custom
    // Contoh sederhana dengan alert
    //if (type === 'success') {
        //alert('✅ ' + message);
    //} else {
        //alert('❌ ' + message);
    //}
    
    // Atau jika menggunakan Toast/Alert library:
    // Toastify({ text: message, className: type }).showToast();
//}
</script>