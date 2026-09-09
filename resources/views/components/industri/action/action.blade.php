<!-- Quick Actions Section -->
<div class="fade-in">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Quick Actions</h3>
            <div class="flex gap-3">
                <button onclick="openIndustriModal()"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg">
                    <i class="fas fa-industry"></i>
                    Tambah Industri
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Tambah Industri Modal -->
<div id="industriModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden animate-modal-in">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-semibold text-white">Tambah Industri Baru</h2>
                    <p class="text-indigo-100 text-sm mt-1">Lengkapi formulir untuk menambahkan data industri</p>
                </div>
                <button onclick="closeIndustriModal()" class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <div class="overflow-y-auto max-h-[calc(90vh-140px)] p-6">
            <form method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Industri <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i class="fas fa-building absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="nama_industri" name="nama_industri"
                            class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                            placeholder="Masukkan nama industri" required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Bidang Usaha <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i class="fas fa-briefcase absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="bidang_usaha" name="bidang_usaha"
                            class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                            placeholder="Contoh: Teknologi, Manufaktur, Otomotif..." required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Pemilik / Direktur</label>
                    <div class="relative">
                        <i class="fas fa-user-tie absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="nama_pemilik" name="nama_pemilik"
                            class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                            placeholder="Masukkan nama pemilik atau direktur">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">No. Telepon</label>
                    <div class="relative">
                        <i class="fas fa-phone absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="no_telepon" name="no_telepon"
                            class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                            placeholder="+62 812-xxxx-xxxx">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap</label>
                    <div class="relative">
                        <i class="fas fa-map-marker-alt absolute left-3 top-4 text-gray-400"></i>
                        <textarea id="alamat" name="alamat" rows="3"
                            class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none transition-all"
                            placeholder="Masukkan alamat lengkap industri..."></textarea>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email (Opsional)</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="email" id="email" name="email"
                            class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                            placeholder="contoh@email.com">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Website (Opsional)</label>
                    <div class="relative">
                        <i class="fas fa-globe absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="url" id="website" name="website"
                            class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                            placeholder="https://contoh.co.id">
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 mt-6">
                    <button type="button" onclick="closeIndustriModal()" class="px-6 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-medium">Batal</button>
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-all duration-200 transform hover:scale-105 font-medium shadow-md hover:shadow-lg">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Modal control
function openIndustriModal() {
    document.getElementById('industriModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    setTimeout(() => document.getElementById('nama_industri').focus(), 300);
}
function closeIndustriModal() {
    document.getElementById('industriModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    document.querySelector('#industriModal form').reset();
}

// ESC to close
document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && !document.getElementById('industriModal').classList.contains('hidden')) {
        closeIndustriModal();
    }
});

// Click backdrop to close
document.getElementById('industriModal').addEventListener('click', e => {
    if (e.target === e.currentTarget) closeIndustriModal();
});
</script>
