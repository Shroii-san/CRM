@extends('layout.main')

@section('content')
<div class="pt-20 px-6 pb-10">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Data Keluarga</h1>
            <p class="text-sm text-gray-500">Kelola data keluarga melalui jendela CRUD.</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="openCrudModal()" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                <i class="fas fa-users-cog"></i> Kelola Keluarga
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
    @endif

<div id="crudModal" class="hidden fixed inset-0 z-30 items-center justify-center bg-black/60 p-4" role="dialog" aria-modal="true">
    <div class="w-full max-w-6xl overflow-hidden rounded-xl bg-white shadow-2xl">
        <div class="flex items-center justify-between bg-emerald-700 px-5 py-4 text-white">
            <div>
                <h2 class="font-semibold"><i class="fas fa-users-cog mr-2"></i>CRUD Data Keluarga</h2>
                <p class="mt-1 text-xs text-emerald-100">Daftar anggota keluarga dan aksi CRUD.</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="openCreateModal()" class="rounded-lg bg-white px-3 py-2 text-sm font-medium text-emerald-700 hover:bg-emerald-50"><i class="fas fa-plus mr-1"></i>Tambah</button>
                <button type="button" onclick="closeCrudModal()" class="h-8 w-8 rounded-full hover:bg-white/20" aria-label="Tutup">&times;</button>
            </div>
        </div>
        <div class="max-h-[75vh] overflow-y-auto p-4">
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">Nama</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">Hubungan</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">Tempat, Tanggal Lahir</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">Pemilik Data</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($keluarga as $member)
                        <tr class="cursor-pointer hover:bg-emerald-50" onclick="openDetailModal({{ $member->id }})">
                            <td class="px-5 py-4 font-medium text-gray-800">{{ $member->nama }}</td>
                            <td class="px-5 py-4">
                                <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-800">{{ $member->status }}</span>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600">
                                {{ $member->tempat_lahir }}, {{ optional($member->tanggal_lahir)->format('d/m/Y') }}
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600">
                                {{ $member->karyawan?->nama_lengkap ?? $member->kandidat?->nama_lengkap ?? '-' }}
                            </td>
                            <td class="px-5 py-4 text-right">
                                <button type="button" onclick="event.stopPropagation(); openDetailModal({{ $member->id }})" class="text-emerald-600 hover:text-emerald-800" title="Lihat detail">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" onclick="event.stopPropagation(); openEditModal({{ $member->id }})" class="ml-3 text-blue-600 hover:text-blue-800" title="Edit data">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('keluarga.destroy', $member) }}" method="POST" class="inline" onclick="event.stopPropagation()" onsubmit="return confirm('Hapus data keluarga ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ml-3 text-red-600 hover:text-red-800" title="Hapus"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-sm text-gray-500">Belum ada data keluarga.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
        </div>
    </div>
</div>

<div id="createModal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/60 p-4" role="dialog" aria-modal="true">
    <div class="w-full max-w-lg rounded-xl bg-white shadow-2xl">
        <div class="flex items-center justify-between rounded-t-xl bg-emerald-700 px-5 py-4 text-white">
            <h2 class="font-semibold"><i class="fas fa-plus mr-2"></i>Tambah Data Keluarga</h2>
            <button type="button" onclick="closeCreateModal()" class="h-8 w-8 rounded-full hover:bg-white/20">&times;</button>
        </div>
        <form method="POST" action="{{ route('keluarga.store-page') }}" class="space-y-4 p-5">
            @csrf
            <select name="pemilik" required class="w-full rounded-lg border border-gray-300 px-3 py-2">
                <option value="">Pilih pemilik data</option>
                @foreach($karyawan as $person)<option value="karyawan:{{ $person->id }}">Karyawan: {{ $person->nama_lengkap }}</option>@endforeach
                @foreach($kandidat as $person)<option value="kandidat:{{ $person->id }}">Kandidat: {{ $person->nama_lengkap }}</option>@endforeach
            </select>
            <input name="nama" required placeholder="Nama lengkap" class="w-full rounded-lg border border-gray-300 px-3 py-2">
            <input name="tempat_lahir" required placeholder="Tempat lahir" class="w-full rounded-lg border border-gray-300 px-3 py-2">
            <div class="space-y-2">
                <input type="date" name="tanggal_lahir" id="createTanggalLahir" max="{{ now()->format('Y-m-d') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2">
                <input type="text" id="createUsia" readonly placeholder="Usia otomatis" class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-600">
            </div>
            <select name="status" required class="w-full rounded-lg border border-gray-300 px-3 py-2"><option value="">Pilih hubungan</option><option>Ayah</option><option>Ibu</option><option>Suami</option><option>Istri</option><option>Anak</option><option>Orang Tua</option></select>
            <div class="flex justify-end gap-2"><button type="button" onclick="closeCreateModal()" class="rounded-lg bg-gray-100 px-4 py-2 text-sm">Batal</button><button class="rounded-lg bg-emerald-600 px-4 py-2 text-sm text-white">Simpan</button></div>
        </form>
    </div>
</div>
</div>

{{-- Modal level 1: detail --}}
<div id="detailModal" class="hidden fixed inset-0 z-40 items-center justify-center bg-black/50 p-4" role="dialog" aria-modal="true">
    <div class="w-full max-w-lg rounded-xl bg-white shadow-2xl">
        <div class="flex items-center justify-between rounded-t-xl bg-emerald-700 px-5 py-4 text-white">
            <h2 class="font-semibold"><i class="fas fa-id-card mr-2"></i>Detail Keluarga</h2>
            <button type="button" onclick="closeDetailModal()" class="h-8 w-8 rounded-full hover:bg-white/20" aria-label="Tutup">&times;</button>
        </div>
        <div class="space-y-3 p-5 text-sm">
            <div class="grid grid-cols-2 gap-3"><span class="text-gray-500">Nama</span><strong id="detailNama"></strong></div>
            <div class="grid grid-cols-2 gap-3"><span class="text-gray-500">Hubungan</span><strong id="detailStatus"></strong></div>
            <div class="grid grid-cols-2 gap-3"><span class="text-gray-500">Tempat lahir</span><strong id="detailTempat"></strong></div>
            <div class="grid grid-cols-2 gap-3"><span class="text-gray-500">Tanggal lahir</span><strong id="detailTanggal"></strong></div>
            <div class="grid grid-cols-2 gap-3"><span class="text-gray-500">Pemilik data</span><strong id="detailPemilik"></strong></div>
        </div>
        <div class="flex justify-end gap-2 border-t border-gray-100 px-5 py-4">
            <button type="button" onclick="closeDetailModal()" class="rounded-lg bg-gray-100 px-4 py-2 text-sm text-gray-700 hover:bg-gray-200">Tutup</button>
        </div>
    </div>
</div>

{{-- Modal level 2: berada di atas modal detail --}}
<div id="editModal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/60 p-4" role="dialog" aria-modal="true">
    <div class="w-full max-w-lg rounded-xl bg-white shadow-2xl">
        <div class="flex items-center justify-between rounded-t-xl bg-slate-800 px-5 py-4 text-white">
            <h2 class="font-semibold"><i class="fas fa-pen mr-2"></i>Edit Data Keluarga</h2>
            <button type="button" onclick="closeEditModal()" class="h-8 w-8 rounded-full hover:bg-white/20" aria-label="Tutup">&times;</button>
        </div>
        <form id="editForm" method="POST" class="space-y-4 p-5">
            @csrf
            @method('PUT')
            <div><label class="mb-1 block text-sm font-medium text-gray-700">Nama</label><input id="editNama" name="nama" required class="w-full rounded-lg border border-gray-300 px-3 py-2"></div>
            <div><label class="mb-1 block text-sm font-medium text-gray-700">Tempat lahir</label><input id="editTempat" name="tempat_lahir" required class="w-full rounded-lg border border-gray-300 px-3 py-2"></div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Tanggal lahir</label>
                <div class="space-y-2">
                    <input id="editTanggal" type="date" name="tanggal_lahir" max="{{ now()->format('Y-m-d') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2">
                    <input id="editUsia" type="text" readonly placeholder="Usia otomatis" class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-600">
                </div>
            </div>
            <div><label class="mb-1 block text-sm font-medium text-gray-700">Hubungan</label><select id="editStatus" name="status" required class="w-full rounded-lg border border-gray-300 px-3 py-2"><option value="Ayah">Ayah</option><option value="Ibu">Ibu</option><option value="Suami">Suami</option><option value="Istri">Istri</option><option value="Anak">Anak</option><option value="Orang Tua">Orang Tua</option></select></div>
            <div class="flex justify-end gap-2 pt-2"><button type="button" onclick="closeEditModal()" class="rounded-lg bg-gray-100 px-4 py-2 text-sm text-gray-700">Batal</button><button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-sm text-white hover:bg-slate-900"><i class="fas fa-save mr-1"></i>Simpan</button></div>
        </form>
    </div>
</div>

<script>
function normalizeDateToIso(value) {
    const raw = String(value || '').trim();
    if (!raw) return '';

    if (/^\d{4}-\d{2}-\d{2}$/.test(raw)) return raw;

    const match = raw.match(/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/);
    if (!match) return '';

    const [, day, month, year] = match;
    const iso = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
    const date = new Date(`${iso}T00:00:00`);

    if (
        date.getFullYear() !== Number(year) ||
        date.getMonth() + 1 !== Number(month) ||
        date.getDate() !== Number(day)
    ) {
        return '';
    }

    return iso;
}

function formatDateForInput(value) {
    const iso = normalizeDateToIso(value);
    if (!iso) return '';

    const [year, month, day] = iso.split('-');
    return `${day}/${month}/${year}`;
}

function renderAgeFromDate(value) {
    const iso = normalizeDateToIso(value);
    if (!iso) return '';

    const now = new Date();
    const birth = new Date(`${iso}T00:00:00`);
    let age = now.getFullYear() - birth.getFullYear();
    const monthDiff = now.getMonth() - birth.getMonth();
    const dayDiff = now.getDate() - birth.getDate();

    if (monthDiff < 0 || (monthDiff === 0 && dayDiff < 0)) {
        age--;
    }

    return age >= 0 ? `${age} tahun` : '';
}

function bindAutoAge(inputId, ageId) {
    const input = document.getElementById(inputId);
    const ageField = document.getElementById(ageId);
    if (!input || !ageField) return;

    const update = () => {
        ageField.value = renderAgeFromDate(input.value);
    };

    input.addEventListener('input', update);
    update();
}

const familyRecords = [
    @foreach($keluarga as $member)
        {
            id: {{ $member->id }},
            nama: @json($member->nama),
            tempat_lahir: @json($member->tempat_lahir),
            tanggal_lahir: @json($member->tanggal_lahir ? $member->tanggal_lahir->format('Y-m-d') : null),
            status: @json($member->status),
            pemilik: @json($member->karyawan?->nama_lengkap ?? $member->kandidat?->nama_lengkap ?? '-')
        },
    @endforeach
];
let activeFamily = null;


function openCreateModal() {
    document.getElementById('createModal').classList.remove('hidden');
    document.getElementById('createModal').classList.add('flex');
}

function closeCreateModal() {
    document.getElementById('createModal').classList.add('hidden');
    document.getElementById('createModal').classList.remove('flex');
}

function openCrudModal() {
    document.getElementById('crudModal').classList.remove('hidden');
    document.getElementById('crudModal').classList.add('flex');
}

function closeCrudModal() {
    if (!document.getElementById('detailModal').classList.contains('hidden')) return;
    document.getElementById('crudModal').classList.add('hidden');
    document.getElementById('crudModal').classList.remove('flex');
}

function openDetailModal(id) {
    activeFamily = familyRecords.find(member => member.id === id);
    if (!activeFamily) return;
    document.getElementById('detailNama').textContent = activeFamily.nama;
    document.getElementById('detailStatus').textContent = activeFamily.status;
    document.getElementById('detailTempat').textContent = activeFamily.tempat_lahir;
    document.getElementById('detailTanggal').textContent = activeFamily.tanggal_lahir ? activeFamily.tanggal_lahir.split('-').reverse().join('/') : '-';
    document.getElementById('detailPemilik').textContent = activeFamily.pemilik;
    document.getElementById('detailModal').classList.remove('hidden');
    document.getElementById('detailModal').classList.add('flex');
}

function closeDetailModal() {
    if (!document.getElementById('editModal').classList.contains('hidden')) return;
    document.getElementById('detailModal').classList.add('hidden');
    document.getElementById('detailModal').classList.remove('flex');
    activeFamily = null;
}

function openEditModal(id = null) {
    if (id !== null) {
        activeFamily = familyRecords.find(member => member.id === id);
    }
    if (!activeFamily) return;
    document.getElementById('editForm').action = `/keluarga/${activeFamily.id}`;
    document.getElementById('editNama').value = activeFamily.nama;
    document.getElementById('editTempat').value = activeFamily.tempat_lahir;
    document.getElementById('editTanggal').value = normalizeDateToIso(activeFamily.tanggal_lahir);
    document.getElementById('editUsia').value = renderAgeFromDate(activeFamily.tanggal_lahir);
    document.getElementById('editStatus').value = activeFamily.status;
    document.getElementById('editModal').classList.remove('hidden');
    document.getElementById('editModal').classList.add('flex');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
    document.getElementById('editModal').classList.remove('flex');
}

document.addEventListener('keydown', event => {
    if (event.key !== 'Escape') return;
    if (!document.getElementById('createModal').classList.contains('hidden')) closeCreateModal();
    else if (!document.getElementById('editModal').classList.contains('hidden')) closeEditModal();
    else closeDetailModal();
});
document.getElementById('detailModal').addEventListener('click', event => { if (event.target.id === 'detailModal') closeDetailModal(); });
document.getElementById('editModal').addEventListener('click', event => { if (event.target.id === 'editModal') closeEditModal(); });
document.getElementById('createModal').addEventListener('click', event => { if (event.target.id === 'createModal') closeCreateModal(); });
document.getElementById('crudModal').addEventListener('click', event => { if (event.target.id === 'crudModal') closeCrudModal(); });
document.addEventListener('DOMContentLoaded', function () {
    bindAutoAge('createTanggalLahir', 'createUsia');
    bindAutoAge('editTanggal', 'editUsia');
});
</script>
@endsection
