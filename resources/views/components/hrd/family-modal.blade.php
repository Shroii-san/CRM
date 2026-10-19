<div id="hrdKeluargaModal" x-data="{ open: false }" x-show="open" x-cloak @keydown.escape.window="closeHrdKeluargaModal()"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
    <div class="w-full max-w-3xl overflow-hidden rounded-xl bg-white shadow-2xl">
        <div class="flex items-center justify-between bg-emerald-700 px-5 py-4 text-white">
            <div>
                <h2 class="font-semibold"><i class="fas fa-people-roof mr-2"></i>Data Keluarga</h2>
                <p id="hrdKeluargaPemilik" class="mt-1 text-xs text-emerald-100"></p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button"
                    @click="closeHrdKeluargaModal(); openHrdKeluargaFormModal()"
                    class="rounded-lg bg-white px-3 py-2 text-sm font-medium text-emerald-700 hover:bg-emerald-50">
                    <i class="fas fa-plus mr-1"></i>Tambah
                </button>
                <button type="button" @click="closeHrdKeluargaModal()"
                    class="h-8 w-8 rounded-full text-xl leading-none hover:bg-white/20">&times;</button>
            </div>
        </div>

        <div id="hrdKeluargaToast" class="mx-5 mt-4 hidden rounded-lg border px-4 py-3 text-sm" role="status"
            aria-live="polite"></div>
        <div id="hrdKeluargaList" class="max-h-[75vh] min-h-0 overflow-y-auto p-5 space-y-2"></div>
    </div>
</div>

<div id="hrdKeluargaFormModal" x-data="{ open: false }" x-show="open" x-cloak @keydown.escape.window="closeHrdKeluargaFormModal()"
    class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 p-4">
    <div class="w-full max-w-2xl rounded-xl bg-white shadow-2xl">
        <div class="flex items-center justify-between bg-emerald-700 px-5 py-4 text-white">
            <h2 id="hrdKeluargaFormTitle" class="font-semibold"><i class="fas fa-plus-circle mr-2"></i>Tambah Anggota
                Keluarga</h2>
            <button type="button" @click="closeHrdKeluargaFormModal()"
                class="h-8 w-8 rounded-full text-xl leading-none hover:bg-white/20">&times;</button>
        </div>

        <form id="hrdKeluargaForm" method="POST" class="space-y-4 p-5">
            @csrf
            <input type="hidden" name="_method" id="hrdKeluargaMethod" value="POST">
            <div class="grid grid-cols-2 gap-3">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Nama *</label>
                    <input id="hrdKeluargaNama" name="nama" required
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tempat lahir *</label>
                    <input id="hrdKeluargaTempat" name="tempat_lahir" required
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal lahir *</label>
                    <input id="hrdKeluargaTanggal" name="tanggal_lahir" type="date" max="{{ now()->format('Y-m-d') }}"
                        required class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Usia</label>
                    <input id="hrdKeluargaUsia" readonly placeholder="Otomatis"
                        class="mt-1 w-full rounded-lg border border-gray-300 bg-gray-100 px-3 py-2 text-gray-600">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Hubungan *</label>
                    <select id="hrdKeluargaStatus" name="status" required
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2">
                        <option value="">Pilih hubungan</option>
                        <option value="1">Orang Tua</option>
                        <option value="2">Pasangan</option>
                        <option value="3">Anak</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" @click="closeHrdKeluargaFormModal()"
                    class="rounded-lg bg-gray-100 px-4 py-2 text-sm text-gray-700">Batal</button>
                <button type="submit"
                    class="rounded-lg bg-emerald-600 px-4 py-2 text-sm text-white hover:bg-emerald-700"><i
                        class="fas fa-save mr-1"></i>Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="hrdKeluargaDeleteModal" x-data="{ open: false }" x-show="open" x-cloak @keydown.escape.window="closeHrdKeluargaDeleteModal()"
    class="fixed inset-0 z-[70] flex items-center justify-center bg-black/60 p-4">
    <div class="w-full max-w-sm rounded-xl bg-white p-5 shadow-2xl">
        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100 text-red-600">
            <i class="fas fa-trash text-lg"></i>
        </div>
        <h2 class="mt-3 text-center text-lg font-semibold text-gray-800">Hapus data keluarga?</h2>
        <p class="mt-1 text-center text-sm text-gray-500">Data yang dihapus tidak dapat dikembalikan.</p>
        <div class="mt-5 flex justify-end gap-2">
            <button type="button" @click="closeHrdKeluargaDeleteModal()"
                class="rounded-lg bg-gray-100 px-4 py-2 text-sm text-gray-700 hover:bg-gray-200">Batal</button>
            <button type="button" onclick="confirmHrdKeluargaDelete()"
                class="rounded-lg bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700"><i
                    class="fas fa-trash mr-1"></i>Hapus</button>
        </div>
    </div>
</div>