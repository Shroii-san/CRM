@props(['divisions' => [], 'placeholder' => 'Cari nama, NIK, atau divisi...'])

@include('components.hrd.candidate-form-body', [
    'modalId' => 'modal',
    'formId' => 'addForm',
    'action' => route('hrd.store'),
    'title' => 'Tambah Calon Karyawan',
    'icon' => 'fa-user-plus',
    'submitText' => 'Simpan calon karyawan',
    'isEdit' => false,
    'isUpdate' => false,
])

@include('components.hrd.candidate-form-body', [
    'modalId' => 'editModal',
    'formId' => 'editForm',
    'action' => '',
    'title' => 'Edit Calon Karyawan',
    'icon' => 'fa-user-edit',
    'submitText' => 'Simpan Perubahan',
    'isEdit' => true,
    'isUpdate' => true,
])

<div id="karyawanModal" x-data="{ open: false, action: '', nama: '' }" x-show="open" x-cloak
    @keydown.escape.window="open = false" @click.self="open = false"
    @open-karyawan-modal.window="nama = $event.detail.nama; action = $event.detail.url; open = true"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-md rounded-xl bg-white shadow-2xl">
        <div class="flex items-center justify-between rounded-t-xl px-5 py-4 text-white"
            style="background: linear-gradient(to right, #16a34a, #2563eb);">
            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/20">
                    <i class="fas fa-user-check text-sm text-white"></i>
                </div>
                <h2 class="text-sm font-semibold">Jadikan Karyawan</h2>
            </div>
            <button type="button" @click="open = false"
                class="flex h-7 w-7 items-center justify-center rounded-full text-xl leading-none text-white transition-colors hover:bg-white/20">&times;
            </button>
        </div>

        <form id="karyawanForm" :action="action" method="POST" class="space-y-4 p-5">
            @csrf
            <p class="text-sm text-gray-600">Kandidat: <strong x-text="nama" id="namaKandidat"></strong></p>

            <div>
                <label class="block text-sm font-medium text-gray-700">ID Departemen</label>
                <input type="number" name="department_id" placeholder="Kosongkan kalau belum ada"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Tanggal Masuk</label>
                <input type="date" name="join_date" value="{{ now()->format('Y-m-d') }}"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" @click="open = false"
                    class="rounded-lg bg-gray-200 px-4 py-2 text-sm">Batal</button>
                <button type="submit" class="rounded-lg bg-green-600 px-4 py-2 text-sm text-white">Jadikan
                    Karyawan</button>
            </div>
        </form>
    </div>
</div>