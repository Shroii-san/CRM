{{-- <!-- Modal Edit Customer -->
<div id="editCustomerModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden animate-modal-in">

        <!-- Header -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-semibold text-white">Edit Customer</h2>
                    <p class="text-indigo-100 text-sm mt-1">Perbarui data pelanggan di formulir berikut</p>
                </div>
                <button onclick="closeEditCustomerModal()" class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>

        <!-- Body -->
        <div class="overflow-y-auto max-h-[calc(90vh-140px)] p-6">
            <form id="editCustomerForm" action="#" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_customer_id" name="customer_id">

                <!-- Nama Customer -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Customer <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i class="fas fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="edit_customer_name" name="customer_name"
                            class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-3 
                                   focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                            required>
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="email" id="edit_customer_email" name="email"
                            class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-3 
                                   focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                    </div>
                </div>

                <!-- Telepon -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Telepon</label>
                    <div class="relative">
                        <i class="fas fa-phone absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="edit_customer_phone" name="phone"
                            class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-3 
                                   focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                    </div>
                </div>

                <!-- Address Section -->
                <div class="mb-6">
                    <h4 class="text-lg font-medium text-gray-900 mb-4 flex items-center border-b pb-2">
                        <i class="fas fa-map-marker-alt text-indigo-500 mr-2"></i>
                        Address Information
                    </h4>
                    
                    <div class="grid md:grid-cols-2 gap-4">
                        <!-- Provinsi -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Provinsi <span class="text-red-500">*</span></label>
                            <select id="edit-province" name="province_id" class="cascade-province w-full border border-gray-300 rounded-lg px-3 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all" required>
                                <option value="">-- Pilih Provinsi --</option>
                                @foreach($provinces as $province)
                                    <option value="{{ $province->id }}">{{ $province->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Kabupaten/Kota -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kabupaten/Kota <span class="text-red-500">*</span></label>
                            <select id="edit-regency" name="regency_id" class="cascade-regency w-full border border-gray-300 rounded-lg px-3 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all" required>
                                <option value="">-- Pilih Kabupaten/Kota --</option>
                            </select>
                        </div>

                        <!-- Kecamatan -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kecamatan <span class="text-red-500">*</span></label>
                            <select id="edit-district" name="district_id" class="cascade-district w-full border border-gray-300 rounded-lg px-3 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all" required>
                                <option value="">-- Pilih Kecamatan --</option>
                            </select>
                        </div>

                        <!-- Kelurahan/Desa -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kelurahan/Desa <span class="text-red-500">*</span></label>
                            <select id="edit-village" name="village_id" class="cascade-village w-full border border-gray-300 rounded-lg px-3 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all" required>
                                <option value="">-- Pilih Kelurahan/Desa --</option>
                            </select>
                        </div>

                        <!-- Detail Alamat (full width) -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Detail Alamat</label>
                            <div class="relative">
                                <i class="fas fa-home absolute left-3 top-3 text-gray-400"></i>
                                <textarea id="editAlamat" name="address" rows="3" placeholder="Contoh: Jl. Merdeka No. 123, RT 01/RW 02" 
                                        class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all resize-none"></textarea>
                            </div>
                            <small class="text-gray-500">Isi dengan detail alamat seperti nama jalan, nomor rumah, RT/RW</small>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="edit_customer_status" name="status"
                        class="w-full border border-gray-300 rounded-lg py-3 focus:ring-2 
                               focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <!-- Tombol Aksi -->
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 mt-6">
                    <button type="button" onclick="closeEditCustomerModal()" 
                        class="px-6 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50 
                               transition-colors font-medium">
                        Batal
                    </button>
                    <button type="submit" 
                        class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 
                               transition-all duration-200 transform hover:scale-105 font-medium shadow-md hover:shadow-lg">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script -->
<script>
function openEditCustomerModal(id, name, email, phone, address, status) {
    document.getElementById('edit_customer_id').value = id;
    document.getElementById('edit_customer_name').value = name;
    document.getElementById('edit_customer_email').value = email ?? '';
    document.getElementById('edit_customer_phone').value = phone ?? '';
    document.getElementById('edit_customer_address').value = address ?? '';
    document.getElementById('edit_customer_status').value = status ?? 'inactive';

    document.getElementById('editCustomerModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeEditCustomerModal() {
    document.getElementById('editCustomerModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    document.getElementById('editCustomerForm').reset();
}
</script> --}}


{{-- pake nya nanti --}}