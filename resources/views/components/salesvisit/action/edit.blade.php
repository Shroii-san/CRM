@props(['currentMenuId', 'salesUsers', 'provinces', 'types'])

<!-- EDIT SALES VISIT MODAL -->
<div id="editVisitModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 overflow-y-auto" style="height: 100vh; width: 100vw; max-height: 100vh; max-width: 100vw;">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[95vh] overflow-hidden animate-fadeIn">

        
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-200" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #3b82f6 100%);">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-edit text-white text-lg"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-white">Edit Kunjungan Sales</h2>
                </div>
                <button onclick="closeEditVisitModal()" class="text-white hover:text-gray-200 transition-colors p-2">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <form id="editVisitForm" method="POST" class="overflow-y-auto max-h-[calc(95vh-140px)]">
            @csrf
            @method('PUT')
            
            <input type="hidden" id="editVisitId" name="visit_id">
            <input type="hidden" id="editIsSalesRole" value="{{ strtolower(auth()->user()->role->role_name ?? '') === 'sales' ? '1' : '0' }}">

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
                                <select name="sales_id" id="editSalesId"
                                    class="w-full pl-9 pr-8 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all appearance-none bg-white"
                                    required>
                                    <option value="">-- Pilih Sales --</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                                </div>
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
                                <input type="date" name="visit_date" id="editVisitDate"
                                    class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    required>
                            </div>
                        </div>

                        <!-- Company with Dropdown -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                Company
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                    <i class="fas fa-building text-gray-400 text-xs"></i>
                                </div>
                                
                                <!-- Hidden input untuk company_id -->
                                <input type="hidden" name="company_id" id="edit-company-id">
                                
                                <!-- Input search untuk dropdown -->
                                <input type="text" 
                                    id="edit-company-search" 
                                    placeholder="Ketik atau pilih company..."
                                    autocomplete="off"
                                    class="w-full pl-9 pr-20 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                
                                <!-- Dropdown list -->
                                <div id="edit-company-dropdown" 
                                    class="hidden absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                    <div id="edit-company-options" class="py-1"></div>
                                </div>
                            </div>
                        </div>

                        <!-- PIC Name with Dropdown -->
                        <div id="edit-pic-input-container" class="hidden">
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                PIC Name <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                    <i class="fas fa-user text-gray-400 text-xs"></i>
                                </div>
                                
                                <!-- Hidden inputs -->
                                <input type="hidden" name="pic_id" id="edit-pic-id">
                                <input type="hidden" name="pic_name" id="edit-pic-name-hidden">
                                
                                <!-- Input search untuk dropdown -->
                                <input type="text" 
                                    id="edit-pic-search" 
                                    placeholder="Ketik atau pilih PIC..."
                                    autocomplete="off"
                                    class="w-full pl-9 pr-20 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                
                                <!-- Dropdown list -->
                                <div id="edit-pic-dropdown" 
                                    class="hidden absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                    <div id="edit-pic-options" class="py-1"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Address Section with Collapsible -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg border border-blue-200 overflow-hidden">
                    <!-- Header - Always Visible -->
                    <div class="p-3 cursor-pointer hover:bg-blue-100 transition-colors" onclick="toggleEditAddressSection()">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-semibold text-gray-800 flex items-center">
                                <i class="fas fa-map-marker-alt text-indigo-600 mr-2"></i>
                                Informasi Lokasi
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
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                        Province <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-map text-gray-400 text-xs"></i>
                                        </div>
                                        <select name="province_id" id="edit-province"
                                            class="w-full pl-9 pr-8 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all appearance-none bg-white"
                                            onchange="checkEditAddressCompletion()">
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
                                        <select name="regency_id" id="edit-regency"
                                            class="w-full pl-9 pr-8 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all appearance-none bg-white"
                                            onchange="checkEditAddressCompletion()">
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
                                        <select name="district_id" id="edit-district"
                                            class="w-full pl-9 pr-8 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all appearance-none bg-white"
                                            onchange="checkEditAddressCompletion()">
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
                                        <select name="village_id" id="edit-village"
                                            class="w-full pl-9 pr-8 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all appearance-none bg-white"
                                            onchange="checkEditAddressCompletion()">
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
                                        <textarea name="address" id="editAddress" rows="2"
                                            class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all resize-none"
                                            placeholder="Contoh: Jl. Merdeka No. 123, RT 01/RW 02"
                                            oninput="checkEditAddressCompletion()"></textarea>
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
                            <textarea name="visit_purpose" id="editPurpose" rows="2"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none"
                                placeholder="Masukkan tujuan kunjungan..." required></textarea>
                        </div>

                        <div class="md:col-span-1">
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                <i class="fas fa-tasks mr-1"></i>Follow Up
                            </label>
                            <div class="flex gap-2">
                                <label class="relative flex-1 cursor-pointer group">
                                    <input type="radio" name="is_follow_up" id="editFollowUpYes" value="1" class="peer sr-only">
                                    <div class="w-full px-2 py-2 text-xs font-medium text-gray-600 bg-white border border-gray-300 rounded-lg transition-all peer-checked:bg-green-500 peer-checked:border-green-500 peer-checked:text-white group-hover:border-green-400 flex items-center justify-center gap-1">
                                        <i class="fas fa-check text-[10px]"></i>
                                        <span>Ya</span>
                                    </div>
                                </label>
                                <label class="relative flex-1 cursor-pointer group">
                                    <input type="radio" name="is_follow_up" id="editFollowUpNo" value="0" class="peer sr-only">
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
                    Update Data
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
</style>