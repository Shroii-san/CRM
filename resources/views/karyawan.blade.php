@extends('layout.main')

@section('content')
<div class="pt-20 px-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold">Data Karyawan</h1>
            <p class="text-sm text-gray-500">Hasil konversi dari kandidat yang berstatus diterima</p>
        </div>
        <a href="{{ route('hrd.index') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            <i class="fas fa-users mr-1"></i> Rekrutmen
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">No</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Foto</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Nama</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">NIK</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Telepon</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Departemen</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Tgl. Masuk</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">KTP</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">CV</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Keluarga</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Riwayat Pendidikan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($karyawan as $i => $k)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-4">{{ $i + 1 }}</td>

                        {{-- FOTO --}}
                        <td class="px-5 py-4">
                            @if($k->foto)
                                <img src="{{ asset('storage/' . $k->foto) }}"
                                     class="h-12 w-12 rounded-lg object-cover border border-gray-200" alt="Foto">
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>

                        {{-- NAMA --}}
                        <td class="px-5 py-4 font-medium">{{ $k->nama_lengkap }}</td>

                        <td class="px-5 py-4">{{ $k->nik ?? '-' }}</td>
                        <td class="px-5 py-4">{{ $k->no_telepon ?? '-' }}</td>
                        <td class="px-5 py-4">{{ $k->departemen_id ?? '-' }}</td>

                        {{-- TGL MASUK --}}
                        <td class="px-5 py-4 whitespace-nowrap">
                            {{ $k->tanggal_masuk ? \Carbon\Carbon::parse($k->tanggal_masuk)->format('d/m/Y') : '-' }}
                        </td>

                        {{-- STATUS KERJA --}}
                        <td class="px-5 py-4">
                            <span class="px-2 py-1 text-xs rounded-full
                                {{ $k->status_kerja === 'aktif'   ? 'bg-green-100 text-green-800' :
                                   ($k->status_kerja === 'kontrak' ? 'bg-yellow-100 text-yellow-800' :
                                                                     'bg-blue-100 text-blue-800') }}">
                                {{ ucfirst($k->status_kerja) }}
                            </span>
                        </td>

                        {{-- KTP --}}
                        <td class="px-5 py-4">
                            @if($k->scan_ktp)
                                <a href="{{ asset('storage/' . $k->scan_ktp) }}" target="_blank"
                                   class="text-blue-600 hover:underline text-sm">
                                    <i class="fas fa-id-card mr-1"></i>Lihat
                                </a>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>

                        {{-- CV --}}
                        <td class="px-5 py-4">
                            @if($k->cv)
                                <a href="{{ asset('storage/' . $k->cv) }}" target="_blank"
                                   class="text-indigo-600 hover:underline text-sm">
                                    <i class="fas fa-file-pdf mr-1"></i>Lihat
                                </a>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>

                        {{-- KELUARGA --}}
                        <td class="px-5 py-4">
                            <button type="button" onclick="openKeluargaModal(this)"
                                    data-karyawan-id="{{ $k->id }}"
                                    data-karyawan-nama="{{ $k->nama_lengkap }}"
                                    data-keluarga='{{ json_encode($k->keluarga->map(fn ($keluarga) => [
                                        "id" => $keluarga->id,
                                        "nama" => $keluarga->nama,
                                        "tempat_lahir" => $keluarga->tempat_lahir,
                                        "tanggal_lahir" => $keluarga->tanggal_lahir->format("Y-m-d"),
                                        "status" => $keluarga->status,
                                        "usia" => $keluarga->tanggal_lahir->age,
                                    ])->values(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) }}'
                                    class="inline-flex items-center gap-1 rounded-lg bg-emerald-100 px-3 py-1.5 text-xs font-medium text-emerald-700 hover:bg-emerald-200">
                                <i class="fas fa-users"></i> Kelola ({{ $k->keluarga->count() }})
                            </button>
                        </td>

                        {{-- PENDIDIKAN --}}
                        <td class="px-5 py-4">
                            @forelse($k->pendidikan as $p)
                                <div class="text-xs text-gray-600 leading-5">
                                    <span class="font-medium">{{ $p->jenjang }}</span>
                                    {{ $p->nama_sekolah }}
                                    @if($p->tahun_masuk)
                                        <span class="text-gray-400">
                                            ({{ $p->tahun_masuk }}@if($p->tahun_lulus)-{{ $p->tahun_lulus }}@endif)
                                        </span>
                                    @endif
                                </div>
                            @empty
                                <span class="text-gray-400 text-xs">-</span>
                            @endforelse
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="px-5 py-8 text-center text-gray-500">
                            Belum ada data karyawan. Konversi dulu dari halaman Rekrutmen.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ==================== Modal Keluarga ==================== -->
<div id="keluargaModal" class="hidden fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl w-full max-w-3xl max-h-[90vh] flex flex-col shadow-2xl">
        <div class="shrink-0 px-5 py-4 rounded-t-xl text-white flex justify-between items-center bg-emerald-700">
            <div>
                <h2 class="font-semibold"><i class="fas fa-people-roof mr-2"></i>Data Keluarga</h2>
                <p id="keluargaKaryawanNama" class="text-xs text-emerald-100 mt-1"></p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="openTambahKeluarga()"
                        class="rounded-lg bg-white px-3 py-2 text-sm font-medium text-emerald-700 hover:bg-emerald-50">
                    <i class="fas fa-plus mr-1"></i> Tambah
                </button>
                <button type="button" onclick="closeKeluargaModal()"
                        class="w-8 h-8 rounded-full hover:bg-white/20 text-xl leading-none">&times;</button>
            </div>
        </div>

        <div id="keluargaToast" class="hidden mx-5 mt-4 rounded-lg border px-4 py-3 text-sm" role="status" aria-live="polite"></div>
        <div id="keluargaList" class="min-h-0 overflow-y-auto p-5 space-y-2"></div>
    </div>
</div>

<div id="keluargaFormModal" class="hidden fixed inset-0 z-[60] bg-black/60 flex items-center justify-center p-4 opacity-0 transition-opacity duration-200">
    <div id="keluargaFormCard" class="bg-white rounded-xl w-full max-w-2xl shadow-2xl scale-95 opacity-0 transition-all duration-200">
        <div class="px-5 py-4 rounded-t-xl text-white flex justify-between items-center bg-emerald-700">
            <h2 id="keluargaFormTitle" class="font-semibold"><i class="fas fa-plus-circle mr-1"></i>Tambah Anggota Keluarga</h2>
            <button type="button" onclick="closeKeluargaForm()"
                    class="w-8 h-8 rounded-full hover:bg-white/20 text-xl leading-none">&times;</button>
        </div>
        <form id="keluargaForm" action="" method="POST" class="p-5 space-y-3">
            @csrf
            <input type="hidden" name="_method" id="keluargaMethod" value="POST">
            <div class="grid grid-cols-2 gap-3">
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-700">Nama *</label>
                    <input id="keluargaNama" type="text" name="nama" required
                           class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700">Tempat lahir *</label>
                    <input id="keluargaTempatLahir" type="text" name="tempat_lahir" required
                           class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700">Tanggal lahir *</label>
                    <input id="keluargaTanggalLahir" type="date" name="tanggal_lahir" max="{{ now()->format('Y-m-d') }}" required
                           class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700">Usia</label>
                    <input id="keluargaUsia" type="text" readonly placeholder="Otomatis"
                           class="mt-1 w-full bg-gray-100 border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-600">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700">Status *</label>
                    <select id="keluargaStatus" name="status" required
                            class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="">Pilih status</option>
                        <option value="Ayah">Ayah</option>
                        <option value="Ibu">Ibu</option>
                        <option value="Suami">Suami</option>
                        <option value="Istri">Istri</option>
                        <option value="Anak">Anak</option>
                        <option value="Orang Tua">Orang Tua</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeKeluargaForm()"
                    class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm hover:bg-emerald-700">
                    <i class="fas fa-save mr-1"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<div id="keluargaDeleteModal" class="hidden fixed inset-0 z-[70] bg-black/60 flex items-center justify-center p-4 opacity-0 transition-opacity duration-200">
    <div id="keluargaDeleteCard" class="w-full max-w-sm rounded-xl bg-white p-5 shadow-2xl scale-95 opacity-0 transition-all duration-200">
        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100 text-red-600">
            <i class="fas fa-trash text-lg"></i>
        </div>
        <h2 class="mt-3 text-center text-lg font-semibold text-gray-800">Hapus data keluarga?</h2>
        <p class="mt-1 text-center text-sm text-gray-500">Data yang dihapus tidak dapat dikembalikan.</p>
        <div class="mt-5 flex justify-end gap-2">
            <button type="button" onclick="closeKeluargaDelete()" class="rounded-lg bg-gray-100 px-4 py-2 text-sm text-gray-700 hover:bg-gray-200">Batal</button>
            <button type="button" onclick="confirmKeluargaDelete()" class="rounded-lg bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700"><i class="fas fa-trash mr-1"></i>Hapus</button>
        </div>
    </div>
</div>

<script>
const keluargaState = { karyawanId: null, records: [] };

function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, char => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    })[char]);
}

function statusClass(status) {
    return status === 'Suami' ? 'bg-blue-100 text-blue-800' :
        status === 'Istri' ? 'bg-pink-100 text-pink-800' :
        status === 'Ayah' || status === 'Ibu' || status === 'Orang Tua' ? 'bg-amber-100 text-amber-800' :
        'bg-emerald-100 text-emerald-800';
}

function normalisasiTanggal(value) {
    const tanggal = String(value || '').trim();
    if (!tanggal) return '';
    if (/^\d{4}-\d{2}-\d{2}$/.test(tanggal)) return tanggal;

    const cocok = tanggal.match(/^(\d{1,2})[\/-](\d{1,2})[\/-](\d{4})$/);
    if (!cocok) return '';

    const [, hari, bulan, tahun] = cocok;
    const hasil = `${tahun}-${bulan.padStart(2, '0')}-${hari.padStart(2, '0')}`;
    const date = new Date(`${hasil}T00:00:00`);

    return date.getFullYear() === Number(tahun) &&
        date.getMonth() + 1 === Number(bulan) &&
        date.getDate() === Number(hari)
        ? hasil : '';
}

function hitungUsia(value) {
    const tanggal = normalisasiTanggal(value);
    if (!tanggal) return '';

    const today = new Date();
    const birth = new Date(`${tanggal}T00:00:00`);
    let years = today.getFullYear() - birth.getFullYear();
    const birthdayPassed = today.getMonth() > birth.getMonth() ||
        (today.getMonth() === birth.getMonth() && today.getDate() >= birth.getDate());

    if (!birthdayPassed) years--;
    return years >= 0 ? `${years} tahun` : '';
}

function formatTanggalDisplay(value) {
    const tanggal = normalisasiTanggal(value);
    return tanggal ? tanggal.split('-').reverse().join('/') : (value || '-');
}

function renderKeluargaList() {
    const list = document.getElementById('keluargaList');
    if (keluargaState.records.length === 0) {
        list.innerHTML = '<div class="rounded-lg border border-dashed border-gray-300 p-5 text-center text-sm text-gray-500">Belum ada data keluarga.</div>';
        return;
    }

    list.innerHTML = keluargaState.records.map(record => `
        <div class="flex items-center justify-between gap-3 rounded-lg border border-gray-200 bg-white p-3 shadow-sm">
            <div class="min-w-0">
                <div class="font-medium text-sm text-gray-800">${escapeHtml(record.nama)}</div>
                <div class="text-xs text-gray-500">${escapeHtml(record.tempat_lahir)}, ${formatTanggalDisplay(record.tanggal_lahir)} &middot; ${hitungUsia(record.tanggal_lahir) || '0 tahun'}</div>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <span class="rounded-full px-2 py-1 text-xs font-medium ${statusClass(record.status)}">${escapeHtml(record.status)}</span>
                <button type="button" onclick="editKeluarga(${record.id})" class="text-blue-600 hover:text-blue-800" title="Edit"><i class="fas fa-edit"></i></button>
                <form action="/keluarga/${record.id}" method="POST" class="inline" onsubmit="openKeluargaDelete(event, this)">
                    @csrf
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus"><i class="fas fa-trash"></i></button>
                </form>
            </div>
        </div>`).join('');
}

function openKeluargaModal(button) {
    keluargaState.karyawanId = button.dataset.karyawanId;
    try { keluargaState.records = JSON.parse(button.dataset.keluarga || '[]'); }
    catch (error) { keluargaState.records = []; }

    document.getElementById('keluargaKaryawanNama').textContent = `Karyawan: ${button.dataset.karyawanNama}`;
    resetKeluargaForm();
    renderKeluargaList();
    document.getElementById('keluargaModal').classList.remove('hidden');
}

function openTambahKeluarga() {
    resetKeluargaForm();
    openKeluargaFormModal();
}

function openKeluargaFormModal() {
    const modal = document.getElementById('keluargaFormModal');
    const card = document.getElementById('keluargaFormCard');
    modal.classList.remove('hidden');
    requestAnimationFrame(() => {
        modal.classList.remove('opacity-0');
        card.classList.remove('scale-95', 'opacity-0');
    });
}

function editKeluarga(id) {
    const record = keluargaState.records.find(item => item.id === id);
    if (!record) return;

    document.getElementById('keluargaForm').action = `/keluarga/${id}`;
    document.getElementById('keluargaMethod').value = 'PUT';
    document.getElementById('keluargaFormTitle').innerHTML = '<i class="fas fa-edit mr-1"></i> Edit Anggota Keluarga';
    document.getElementById('keluargaNama').value = record.nama;
    document.getElementById('keluargaTempatLahir').value = record.tempat_lahir;
    document.getElementById('keluargaTanggalLahir').value = normalisasiTanggal(record.tanggal_lahir);
    document.getElementById('keluargaStatus').value = record.status;
    document.getElementById('keluargaUsia').value = hitungUsia(record.tanggal_lahir);
    openKeluargaFormModal();
}

function resetKeluargaForm() {
    const form = document.getElementById('keluargaForm');
    form.reset();
    form.action = `/karyawan/${keluargaState.karyawanId}/keluarga`;
    document.getElementById('keluargaMethod').value = 'POST';
    document.getElementById('keluargaFormTitle').innerHTML = '<i class="fas fa-plus-circle mr-1"></i> Tambah Anggota Keluarga';
    document.getElementById('keluargaUsia').value = '';
}

function closeKeluargaModal() {
    document.getElementById('keluargaModal').classList.add('hidden');
    closeKeluargaForm();
}

function closeKeluargaForm() {
    const modal = document.getElementById('keluargaFormModal');
    const card = document.getElementById('keluargaFormCard');
    modal.classList.add('opacity-0');
    card.classList.add('scale-95', 'opacity-0');
    setTimeout(() => modal.classList.add('hidden'), 200);
}

function showKeluargaToast(message, type = 'success') {
    const toast = document.getElementById('keluargaToast');
    toast.textContent = message;
    toast.className = type === 'success'
        ? 'mx-5 mt-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 opacity-0 -translate-y-2 transition-all duration-300'
        : 'mx-5 mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 opacity-0 -translate-y-2 transition-all duration-300';
    toast.classList.remove('hidden');
    requestAnimationFrame(() => toast.classList.remove('opacity-0', '-translate-y-2'));
    clearTimeout(window.keluargaToastTimer);
    window.keluargaToastTimer = setTimeout(() => {
        toast.classList.add('opacity-0', '-translate-y-2');
        setTimeout(() => toast.classList.add('hidden'), 300);
    }, 3500);
}

async function readKeluargaResponse(response, fallback) {
    let result = {};
    try { result = await response.json(); } catch (error) {}
    if (response.status === 401) throw new Error('Sesi login sudah berakhir. Silakan login kembali.');
    if (!response.ok) {
        const validation = result.errors ? Object.values(result.errors).flat().join(' ') : '';
        throw new Error(result.message || validation || fallback);
    }
    return result;
}

let keluargaDeleteForm = null;

function openKeluargaDelete(event, form) {
    event.preventDefault();
    keluargaDeleteForm = form;
    const modal = document.getElementById('keluargaDeleteModal');
    const card = document.getElementById('keluargaDeleteCard');
    modal.classList.remove('hidden');
    requestAnimationFrame(() => {
        modal.classList.remove('opacity-0');
        card.classList.remove('scale-95', 'opacity-0');
    });
}

function closeKeluargaDelete() {
    const modal = document.getElementById('keluargaDeleteModal');
    const card = document.getElementById('keluargaDeleteCard');
    modal.classList.add('opacity-0');
    card.classList.add('scale-95', 'opacity-0');
    setTimeout(() => modal.classList.add('hidden'), 200);
    keluargaDeleteForm = null;
}

async function confirmKeluargaDelete() {
    const form = keluargaDeleteForm;
    if (!form) return;
    closeKeluargaDelete();

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { Accept: 'application/json' },
        });

        await readKeluargaResponse(response, 'Gagal menghapus data keluarga.');
        keluargaState.records = keluargaState.records.filter(record => `/keluarga/${record.id}` !== form.action);
        renderKeluargaList();
        showKeluargaToast('Data keluarga berhasil dihapus.');
    } catch (error) {
        showKeluargaToast(error.message, 'error');
    }
}

document.getElementById('keluargaTanggalLahir').addEventListener('input', function () {
    document.getElementById('keluargaUsia').value = hitungUsia(this.value);
    this.setCustomValidity(normalisasiTanggal(this.value) ? '' : 'Gunakan format tanggal valid.');
});

document.getElementById('keluargaForm').addEventListener('submit', async function (event) {
    event.preventDefault();

    const input = document.getElementById('keluargaTanggalLahir');
    const tanggal = normalisasiTanggal(input.value);
    input.setCustomValidity(tanggal ? '' : 'Gunakan format tanggal yang valid.');

    if (!tanggal || !this.checkValidity()) {
        this.reportValidity();
        return;
    }

    input.value = tanggal;

    try {
        const response = await fetch(this.action, {
            method: 'POST',
            body: new FormData(this),
            headers: { Accept: 'application/json' },
        });

        const result = await readKeluargaResponse(response, 'Data keluarga gagal disimpan.');
        const saved = result.data;
        const savedRecord = {
            id: saved.id,
            nama: saved.nama,
            tempat_lahir: saved.tempat_lahir,
            tanggal_lahir: saved.tanggal_lahir,
            status: saved.status,
        };

        const existingIndex = keluargaState.records.findIndex(record => record.id === savedRecord.id);
        if (existingIndex >= 0) keluargaState.records[existingIndex] = savedRecord;
        else keluargaState.records.unshift(savedRecord);

        renderKeluargaList();
        closeKeluargaForm();
        showKeluargaToast(existingIndex >= 0 ? 'Data keluarga berhasil diperbarui.' : 'Data keluarga berhasil ditambahkan.');
    } catch (error) {
        showKeluargaToast(error.message, 'error');
    }
});

document.getElementById('keluargaModal').addEventListener('click', event => {
    if (event.target.id === 'keluargaModal') closeKeluargaModal();
});

document.getElementById('keluargaFormModal').addEventListener('click', event => {
    if (event.target.id === 'keluargaFormModal') closeKeluargaForm();
});

document.getElementById('keluargaDeleteModal').addEventListener('click', event => {
    if (event.target.id === 'keluargaDeleteModal') closeKeluargaDelete();
});

document.addEventListener('keydown', event => {
    if (event.key !== 'Escape') return;
    if (!document.getElementById('keluargaFormModal').classList.contains('hidden')) closeKeluargaForm();
    else if (!document.getElementById('keluargaDeleteModal').classList.contains('hidden')) closeKeluargaDelete();
    else if (!document.getElementById('keluargaModal').classList.contains('hidden')) closeKeluargaModal();
});
</script>
@endsection
