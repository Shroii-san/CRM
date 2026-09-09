<!-- Modal Edit Kategori Industri -->
<div id="editIndustryCategoryModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-hidden animate-modal-in">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-semibold text-white">Edit Kategori Industri</h2>
                    <p class="text-indigo-100 text-sm mt-1">Perbarui data kategori industri di formulir berikut</p>
                </div>
                <button onclick="closeEditIndustryCategoryModal()" class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>

        <!-- Body -->
        <div class="overflow-y-auto max-h-[calc(90vh-140px)] p-6">
            <form id="editIndustryCategoryForm" action="" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_category_id" name="category_id">

                <!-- Nama Kategori Industri -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kategori Industri <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i class="fas fa-tags absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="edit_category_name" name="category_name"
                            class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                            required>
                    </div>
                </div>

                <!-- Deskripsi -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <div class="relative">
                        <i class="fas fa-align-left absolute left-3 top-4 text-gray-400"></i>
                        <textarea id="edit_category_description" name="category_description" rows="3"
                            class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none transition-all"></textarea>
                    </div>
                </div>

                <!-- Tombol -->
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 mt-6">
                    <button type="button" onclick="closeEditIndustryCategoryModal()" class="px-6 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-medium">Batal</button>
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 transform hover:scale-105 font-medium shadow-md hover:shadow-lg">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script Modal -->
<script>
function openEditIndustryCategoryModal(id, name, description) {
    document.getElementById('edit_category_id').value = id;
    document.getElementById('edit_category_name').value = name;
    document.getElementById('edit_category_description').value = description ?? '';
    document.getElementById('editIndustryCategoryModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeEditIndustryCategoryModal() {
    document.getElementById('editIndustryCategoryModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    document.getElementById('editIndustryCategoryForm').reset();
}
</script>
