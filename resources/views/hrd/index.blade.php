@extends('layout.main')

@section('content')

<style>
    .step-button {
        padding: 7px 12px; border-radius: 8px; white-space: nowrap;
        font-size: 13px; background: #f3f4f6; color: #4b5563;
        border: 1px solid transparent; transition: all .15s;
    }
    .step-button.active { background: #2563eb; color: #fff; }
</style>

<div class="pt-20 px-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold">HRD - Rekrutmen Karyawan</h1>
            <p class="text-sm text-gray-500">Data calon karyawan</p>
        </div>
        <div class="flex gap-2">
            <button type="button" onclick="openAddModal()"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                <i class="fas fa-plus"></i> Tambah Calon Karyawan
            </button>
        </div>
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

    @if($errors->any())
        <div class="bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded mb-4">
            <p class="font-medium">Data belum dapat disimpan:</p>
            <ul class="mt-1 list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- ==================== Tabel ==================== -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="flex flex-wrap items-end gap-3 border-b border-gray-200 p-4">
            <div class="min-w-[220px] flex-1">
                <label for="hrdSearch" class="mb-1 block text-xs font-medium text-gray-600">Cari kandidat</label>
                <div class="relative">
                    <i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input id="hrdSearch" type="search" placeholder="Nama, NIK, atau divisi"
                           class="w-full rounded-lg border border-gray-300 py-2 pl-9 pr-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
            </div>
            <div class="w-full sm:w-44">
                <label for="hrdStatusFilter" class="mb-1 block text-xs font-medium text-gray-600">Status</label>
                <select id="hrdStatusFilter" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    <option value="">Semua status</option>
                    <option value="pending">Pending</option>
                    <option value="diterima">Diterima</option>
                    <option value="ditolak">Ditolak</option>
                </select>
            </div>
            <div class="w-full sm:w-44">
                <label for="hrdDivisionFilter" class="mb-1 block text-xs font-medium text-gray-600">Divisi</label>
                <select id="hrdDivisionFilter" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    <option value="">Semua divisi</option>
                    <option value="Direktur">Direktur</option>
                    <option value="Keuangan">Keuangan</option>
                    <option value="Multimedia">Multimedia</option>
                    <option value="IT">IT</option>
                    <option value="Administrasi">Administrasi</option>
                </select>
            </div>
            <button id="hrdFilterReset" type="button" class="rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">
                <i class="fas fa-rotate-left mr-1"></i>Reset
            </button>
            <p id="hrdFilterCount" class="w-full text-xs text-gray-500 sm:w-auto sm:pb-2" aria-live="polite"></p>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500" aria-sort="none">
                        <button type="button" data-sort-column="1" class="inline-flex items-center gap-2 hover:text-gray-800" aria-label="Urutkan berdasarkan nama">
                            Nama <i class="fas fa-sort text-gray-400" aria-hidden="true"></i>
                        </button>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500" aria-sort="none">
                        <button type="button" data-sort-column="2" class="inline-flex items-center gap-2 hover:text-gray-800" aria-label="Urutkan berdasarkan NIK">
                            NIK <i class="fas fa-sort text-gray-400" aria-hidden="true"></i>
                        </button>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500" aria-sort="none">
                        <button type="button" data-sort-column="3" class="inline-flex items-center gap-2 hover:text-gray-800" aria-label="Urutkan berdasarkan divisi">
                            Divisi <i class="fas fa-sort text-gray-400" aria-hidden="true"></i>
                        </button>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Foto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">KTP</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">CV</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500" aria-sort="none">
                        <button type="button" data-sort-column="7" class="inline-flex items-center gap-2 hover:text-gray-800" aria-label="Urutkan berdasarkan status">
                            Status <i class="fas fa-sort text-gray-400" aria-hidden="true"></i>
                        </button>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Keluarga</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($kandidat as $i => $k)
                <tr class="hover:bg-gray-50 hrd-kandidat-row"
                    data-search="{{ strtolower($k->nama_lengkap . ' ' . $k->nik . ' ' . $k->divisi) }}"
                    data-status="{{ strtolower($k->status) }}"
                    data-division="{{ strtolower($k->divisi) }}">
                    <td class="px-6 py-4">{{ $i + 1 }}</td>
                    <td class="px-6 py-4">{{ $k->nama_lengkap }}</td>
                    <td class="px-6 py-4">{{ $k->nik }}</td>
                    <td class="px-6 py-4">{{ $k->divisi }}</td>

                    {{-- FOTO --}}
                    <td class="px-6 py-4">
                        @if($k->foto)
                            <img src="{{ asset('storage/' . $k->foto) }}"
                                 class="h-12 w-12 rounded-lg object-cover border border-gray-200" alt="Foto">
                        @else
                            <span class="text-gray-400 text-xs">-</span>
                        @endif
                    </td>

                    {{-- KTP --}}
                    <td class="px-6 py-4">
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
                    <td class="px-6 py-4">
                        @if($k->cv)
                            <a href="{{ asset('storage/' . $k->cv) }}" target="_blank"
                               class="text-indigo-600 hover:underline text-sm">
                                <i class="fas fa-file-pdf mr-1"></i>Lihat
                            </a>
                        @else
                            <span class="text-gray-400 text-xs">-</span>
                        @endif
                    </td>

                    {{-- STATUS --}}
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full
                            {{ $k->status == 'pending' ? 'bg-yellow-100 text-yellow-800' :
                               ($k->status == 'diterima' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                            {{ ucfirst($k->status) }}
                        </span>
                    </td>

                    {{-- KELUARGA --}}
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($k->karyawan)
                            @php
                                $karyawanKeluargaJson = $k->karyawan->keluarga->map(function ($keluarga) {
                                    return [
                                        'id' => $keluarga->id,
                                        'nama' => $keluarga->nama,
                                        'tempat_lahir' => $keluarga->tempat_lahir,
                                        'tanggal_lahir' => $keluarga->tanggal_lahir ? \Carbon\Carbon::parse($keluarga->tanggal_lahir)->format('Y-m-d') : null,
                                        'status' => $keluarga->status,
                                    ];
                                })->values()->toJson();
                            @endphp
                            <button type="button" onclick="openHrdKeluargaModal(this)"
                                    data-karyawan-id="{{ $k->karyawan->id }}"
                                    data-karyawan-nama="{{ $k->nama_lengkap }}"
                                    data-keluarga="{{ $karyawanKeluargaJson }}"
                                    class="inline-flex items-center gap-1 rounded-lg bg-emerald-100 px-3 py-1.5 text-xs font-medium text-emerald-700 hover:bg-emerald-200"
                                    title="Kelola keluarga {{ $k->nama_lengkap }}">
                                <i class="fas fa-users"></i>
                                Kelola ({{ $k->karyawan->keluarga->count() }})
                            </button>
                        @else
                            <span class="text-xs text-gray-400">Belum menjadi karyawan</span>
                        @endif
                    </td>

                    {{-- AKSI --}}
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{-- Jadikan karyawan --}}
                        @if($k->status === 'diterima')
                            @if($k->karyawan)
                                <span class="text-xs text-green-600 mr-2" title="Sudah jadi karyawan">
                                    <i class="fas fa-check-circle"></i>
                                </span>
                            @else
                                <button type="button" onclick="openKaryawanModal(this)"
                                        data-url="{{ route('hrd.jadikan-karyawan', $k->id) }}"
                                        data-nama="{{ $k->nama_lengkap }}"
                                        class="text-green-600 hover:text-green-800 mr-2"
                                        title="Jadikan karyawan">
                                    <i class="fas fa-user-check"></i>
                                </button>
                            @endif
                        @endif

                        {{-- Edit --}}
                        @php
                            $keluargaJson = $k->keluarga->map(function ($keluarga) {
                                return [
                                    'id' => $keluarga->id,
                                    'nama' => $keluarga->nama,
                                    'tempat_lahir' => $keluarga->tempat_lahir,
                                    'tanggal_lahir' => $keluarga->tanggal_lahir ? \Carbon\Carbon::parse($keluarga->tanggal_lahir)->format('Y-m-d') : null,
                                    'status' => $keluarga->status,
                                ];
                            })->values()->toJson();
                        @endphp
                        <button type="button"
                                onclick="openEditModal(this)"
                                data-update-url="{{ route('hrd.update', $k->id) }}"
                                data-nama_lengkap="{{ $k->nama_lengkap }}"
                                data-nik="{{ $k->nik }}"
                                data-no_telepon="{{ $k->no_telepon }}"
                                data-divisi="{{ $k->divisi }}"
                                data-tgl_lamar="{{ $k->tgl_lamar ? \Carbon\Carbon::parse($k->tgl_lamar)->format('Y-m-d') : '' }}"
                                data-npwp="{{ $k->npwp }}"
                                data-no_bpjs="{{ $k->no_bpjs }}"
                                data-status="{{ $k->status }}"
                                data-foto="{{ $k->foto ? asset('storage/'.$k->foto) : '' }}"
                                data-ktp="{{ $k->scan_ktp ? asset('storage/'.$k->scan_ktp) : '' }}"
                                data-cv="{{ $k->cv ? asset('storage/'.$k->cv) : '' }}"
                                data-pendidikan='@json($k->pendidikan ?? [])'
                                data-keluarga='{{ $keluargaJson }}'
                                class="text-blue-600 hover:text-blue-800 mr-2" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>

                        {{-- Hapus --}}
                        <form action="{{ route('hrd.destroy', $k->id) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800"
                                    onclick="return confirm('Yakin hapus?')" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr class="hrd-no-data-row">
                    <td colspan="10" class="px-6 py-4 text-center text-gray-500">Belum ada data</td>
                </tr>
                @endforelse
                <tr id="hrdFilterEmpty" class="hidden">
                    <td colspan="10" class="px-6 py-8 text-center text-sm text-gray-500">Tidak ada kandidat yang cocok dengan filter.</td>
                </tr>
            </tbody>
        </table>

        <!-- ==================== Pagging dan view n record  ==================== -->
        <div class="flex flex-col gap-3 border-t border-gray-200 px-5 py-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-wrap items-center gap-3 text-xs text-gray-600">
                <label for="hrdPageSize" class="flex items-center gap-2">
                    Tampilkan
                    <select id="hrdPageSize" class="rounded-md border border-gray-300 bg-white px-2 py-1.5 text-sm text-gray-700 focus:border-blue-500 focus:outline-none">
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    data per halaman
                </label>
                <span id="hrdPaginationInfo" aria-live="polite"></span>
            </div>
            <nav id="hrdPagination" class="flex items-center justify-end gap-1" aria-label="Navigasi halaman"></nav>
        </div>
    </div>
</div>

<!-- ==================== Modal CRUD Keluarga ==================== -->
<div id="hrdKeluargaModal" class="hidden fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl w-full max-w-3xl max-h-[90vh] flex flex-col shadow-2xl">
        <div class="shrink-0 px-5 py-4 rounded-t-xl text-white flex justify-between items-center bg-emerald-700">
            <div>
                <h2 class="font-semibold"><i class="fas fa-people-roof mr-2"></i>Data Keluarga</h2>
                <p id="hrdKeluargaPemilik" class="text-xs text-emerald-100 mt-1"></p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="openHrdKeluargaForm()" class="rounded-lg bg-white px-3 py-2 text-sm font-medium text-emerald-700 hover:bg-emerald-50">
                    <i class="fas fa-plus mr-1"></i>Tambah
                </button>
                <button type="button" onclick="closeHrdKeluargaModal()" class="w-8 h-8 rounded-full hover:bg-white/20 text-xl leading-none">&times;</button>
            </div>
        </div>
        <div id="hrdKeluargaToast" class="hidden mx-5 mt-4 rounded-lg border px-4 py-3 text-sm" role="status" aria-live="polite"></div>
        <div id="hrdKeluargaList" class="min-h-0 overflow-y-auto p-5 space-y-2"></div>
    </div>
</div>

<div id="hrdKeluargaFormModal" class="hidden fixed inset-0 z-[60] bg-black/60 flex items-center justify-center p-4 opacity-0 transition-opacity duration-200">
    <div id="hrdKeluargaFormCard" class="bg-white rounded-xl w-full max-w-2xl shadow-2xl scale-95 opacity-0 transition-all duration-200">
        <div class="px-5 py-4 rounded-t-xl text-white flex justify-between items-center bg-emerald-700">
            <h2 id="hrdKeluargaFormTitle" class="font-semibold"><i class="fas fa-plus-circle mr-2"></i>Tambah Anggota Keluarga</h2>
            <button type="button" onclick="closeHrdKeluargaForm()" class="w-8 h-8 rounded-full hover:bg-white/20 text-xl leading-none">&times;</button>
        </div>
        <form id="hrdKeluargaForm" method="POST" class="p-5 space-y-4">
            @csrf
            <input type="hidden" name="_method" id="hrdKeluargaMethod" value="POST">
            <div class="grid grid-cols-2 gap-3">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Nama *</label>
                    <input id="hrdKeluargaNama" name="nama" required class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tempat lahir *</label>
                    <input id="hrdKeluargaTempat" name="tempat_lahir" required class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal lahir *</label>
                    <input id="hrdKeluargaTanggal" name="tanggal_lahir" type="date" max="{{ now()->format('Y-m-d') }}" required class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Usia</label>
                    <input id="hrdKeluargaUsia" readonly placeholder="Otomatis" class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-100 text-gray-600">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Hubungan *</label>
                    <select id="hrdKeluargaStatus" name="status" required class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="">Pilih hubungan</option>
                        <option>Ayah</option><option>Ibu</option><option>Suami</option><option>Istri</option><option>Anak</option><option>Orang Tua</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeHrdKeluargaForm()" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700"><i class="fas fa-save mr-1"></i>Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="hrdKeluargaDeleteModal" class="hidden fixed inset-0 z-[70] bg-black/60 flex items-center justify-center p-4 opacity-0 transition-opacity duration-200">
    <div id="hrdKeluargaDeleteCard" class="w-full max-w-sm rounded-xl bg-white p-5 shadow-2xl scale-95 opacity-0 transition-all duration-200">
        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100 text-red-600">
            <i class="fas fa-trash text-lg"></i>
        </div>
        <h2 class="mt-3 text-center text-lg font-semibold text-gray-800">Hapus data keluarga?</h2>
        <p class="mt-1 text-center text-sm text-gray-500">Data yang dihapus tidak dapat dikembalikan.</p>
        <div class="mt-5 flex justify-end gap-2">
            <button type="button" onclick="closeHrdKeluargaDelete()" class="rounded-lg bg-gray-100 px-4 py-2 text-sm text-gray-700 hover:bg-gray-200">Batal</button>
            <button type="button" onclick="confirmHrdKeluargaDelete()" class="rounded-lg bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700"><i class="fas fa-trash mr-1"></i>Hapus</button>
        </div>
    </div>
</div>

<!-- ==================== Modal Tambah ==================== -->
<div id="modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl w-full max-w-5xl max-h-[94vh] flex flex-col min-h-0">

        {{-- Header --}}
        <div class="shrink-0 text-white px-5 py-3 rounded-t-xl flex justify-between items-center"
             style="background: linear-gradient(to right, #7c3aed, #3b82f6);">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center">
                    <i class="fas fa-user-plus text-white text-sm"></i>
                </div>
                <h2 class="font-semibold text-sm">Tambah Calon Karyawan</h2>
            </div>
            <button type="button" onclick="closeModal('modal')"
                    class="w-7 h-7 flex items-center justify-center rounded-full hover:bg-white/20 transition-colors text-xl leading-none text-white">
                &times;
            </button>
        </div>

        <form id="addForm" action="{{ route('hrd.store') }}" method="POST" enctype="multipart/form-data"
              class="min-h-0 flex-1 flex flex-col" data-step="1">
            @csrf

            {{-- Menu slide --}}
            <div class="shrink-0 flex gap-2 border-b px-4 py-3 overflow-x-auto">
                <button type="button" class="step-button active" data-step-button="1"
                        onclick="wizardGo('addForm', 1)">1. Data Diri</button>
                <button type="button" class="step-button" data-step-button="2"
                        onclick="wizardGo('addForm', 2)">2. Pendidikan</button>
                <button type="button" class="step-button" data-step-button="3"
                        onclick="wizardGo('addForm', 3)">3. Dokumen</button>
            </div>
            {{-- Area isi --}}
            <div class="min-h-0 flex-1 overflow-y-auto p-4">

                {{-- ============ STEP 1 ============ --}}
                <section data-wizard-step="1" class="space-y-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user text-blue-600 mr-1"></i> Informasi Dasar
                        </h3>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Nama lengkap *</label>
                                <input type="text" name="nama_lengkap" placeholder="isi dengan huruf kapital" required
                                       class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">NIK *</label>
                                <input type="text" name="nik" required maxlength="16" placeholder="16 digit"
                                       class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">No. telepon *</label>
                                <input type="text" name="no_telepon" required placeholder="08xxxxxxxxxx"
                                       class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-briefcase text-blue-600 mr-1"></i> Posisi yang Dilamar
                        </h3>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Divisi *</label>
                                <select name="divisi" required
                                        class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                    <option value="">Pilih Divisi</option>
                                    <option>Direktur</option>
                                    <option>Keuangan</option>
                                    <option>Multimedia</option>
                                    <option>IT</option>
                                    <option>Administrasi</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tgl. lamar *</label>
                                <input type="date" name="tgl_lamar" required
                                       class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            </div>
                        </div>
                    </div>
                </section>

                {{-- ============ STEP 2 ============ --}}
                <section data-wizard-step="2" class="hidden space-y-4">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-graduation-cap text-blue-600 mr-1"></i> Riwayat Pendidikan
                        <span class="text-xs text-gray-400 font-normal">(boleh dikosongkan)</span>
                    </h3>

                    <div id="pendidikanRowsAdd" class="space-y-2" data-index="0"></div>

                    <button type="button" onclick="tambahPendidikan('pendidikanRowsAdd')"
                            class="text-sm text-blue-600 hover:text-blue-800">
                        <i class="fas fa-plus mr-1"></i> Tambah Pendidikan
                    </button>
                </section>

                {{-- ============ STEP 3 ============ --}}
                <section data-wizard-step="3" class="hidden space-y-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-file-alt text-blue-600 mr-1"></i> Dokumen
                        </h3>
                        <div class="space-y-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Foto <span class="text-xs font-normal text-gray-400">(JPG, PNG, maksimal 2 MB)</span></label>
                                <input type="file" name="foto" accept="image/*"
                                       class="block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Scan KTP</label>
                                <input type="file" name="scan_ktp" accept="image/*,.pdf"
                                       class="block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">CV</label>
                                <input type="file" name="cv" accept=".pdf,.doc,.docx"
                                       class="block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-cog text-blue-600 mr-1"></i> Opsional
                        </h3>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">NPWP</label>
                                <input type="text" name="npwp"
                                       class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">No. BPJS</label>
                                <input type="text" name="no_bpjs"
                                       class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            </div>
                        </div>
                    </div>
                </section>

            </div>

            {{-- Navigasi --}}
            <div class="shrink-0 flex justify-between items-center gap-2 border-t px-4 py-3">
                <button type="button" data-nav-prev class="invisible px-4 py-2 rounded-lg bg-gray-100 text-gray-600 text-sm"
                        onclick="wizardGo('addForm', Number(document.getElementById('addForm').dataset.step) - 1)">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </button>

                <div class="flex gap-2">
                    <button type="button" data-nav-next
                            class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm hover:bg-blue-700"
                            onclick="wizardNext('addForm')">
                        Berikutnya <i class="fas fa-arrow-right ml-1"></i>
                    </button>

                    <button type="submit" data-nav-submit
                            class="hidden px-4 py-2 rounded-lg bg-blue-600 text-white text-sm hover:bg-blue-700">
                        <i class="fas fa-save mr-1"></i> Simpan calon karyawan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ==================== Modal Edit ==================== -->
<div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl w-full max-w-5xl max-h-[94vh] flex flex-col min-h-0">

        <div class="shrink-0 text-white px-5 py-3 rounded-t-xl flex justify-between items-center"
             style="background: linear-gradient(to right, #7c3aed, #3b82f6);">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center">
                    <i class="fas fa-user-edit text-white text-sm"></i>
                </div>
                <h2 class="font-semibold text-sm">Edit Calon Karyawan</h2>
            </div>
            <button type="button" onclick="closeModal('editModal')"
                    class="w-7 h-7 flex items-center justify-center rounded-full hover:bg-white/20 transition-colors text-xl leading-none text-white">
                &times;
            </button>
        </div>

        <form id="editForm" action="" method="POST" enctype="multipart/form-data"
              class="min-h-0 flex-1 flex flex-col" data-step="1">
            @csrf
            @method('PUT')

            <div class="shrink-0 flex gap-2 border-b px-4 py-3 overflow-x-auto">
                <button type="button" class="step-button active" data-step-button="1"
                        onclick="wizardGo('editForm', 1)">1. Data Diri</button>
                <button type="button" class="step-button" data-step-button="2"
                        onclick="wizardGo('editForm', 2)">2. Pendidikan</button>
                <button type="button" class="step-button" data-step-button="3"
                        onclick="wizardGo('editForm', 3)">3. Dokumen</button>
            </div>

            <div class="min-h-0 flex-1 overflow-y-auto p-4">

                {{-- STEP 1 --}}
                <section data-wizard-step="1" class="space-y-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user text-blue-600 mr-1"></i> Informasi Dasar
                        </h3>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Nama lengkap *</label>
                                <input type="text" name="nama_lengkap" required
                                       class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">NIK *</label>
                                <input type="text" name="nik" required maxlength="16"
                                       class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">No. telepon *</label>
                                <input type="text" name="no_telepon" required
                                       class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-briefcase text-blue-600 mr-1"></i> Posisi yang Dilamar
                        </h3>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Divisi *</label>
                                <select name="divisi" required
                                        class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                    <option value="">Pilih Divisi</option>
                                    <option>Direktur</option>
                                    <option>Keuangan</option>
                                    <option>Multimedia</option>
                                    <option>IT</option>
                                    <option>Administrasi</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tgl. lamar *</label>
                                <input type="date" name="tgl_lamar" required
                                       class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            </div>
                        </div>
                    </div>
                </section>

                {{-- STEP 2 --}}
                <section data-wizard-step="2" class="hidden space-y-4">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-graduation-cap text-blue-600 mr-1"></i> Riwayat Pendidikan
                    </h3>

                    <div id="pendidikanRowsEdit" class="space-y-2" data-index="0"></div>

                    <button type="button" onclick="tambahPendidikan('pendidikanRowsEdit')"
                            class="text-sm text-blue-600 hover:text-blue-800">
                        <i class="fas fa-plus mr-1"></i> Tambah Pendidikan
                    </button>
                </section>

                {{-- STEP 3 --}}
                <section data-wizard-step="3" class="hidden space-y-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-file-alt text-blue-600 mr-1"></i> Dokumen
                            <span class="text-xs text-gray-400 font-normal">(kosongkan kalau tidak diganti)</span>
                        </h3>
                        <div class="space-y-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Foto <span class="text-xs font-normal text-gray-400">(kosongkan kalau tidak diganti, maksimal 2 MB)</span></label>
                                <div id="editFotoWrap" class="hidden mb-1">
                                    <img id="editFotoImg" class="h-14 w-14 rounded-lg object-cover border">
                                </div>
                                <input type="file" name="foto" accept="image/*"
                                       class="block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Scan KTP</label>
                                <a id="editKtpLink" href="#" target="_blank"
                                   class="hidden text-blue-600 text-xs hover:underline">Lihat KTP saat ini</a>
                                <input type="file" name="scan_ktp" accept="image/*,.pdf"
                                       class="block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">CV</label>
                                <a id="editCvLink" href="#" target="_blank"
                                   class="hidden text-indigo-600 text-xs hover:underline">Lihat CV saat ini</a>
                                <input type="file" name="cv" accept=".pdf,.doc,.docx"
                                       class="block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-cog text-blue-600 mr-1"></i> Pengaturan Lainnya
                        </h3>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">NPWP</label>
                                <input type="text" name="npwp"
                                       class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">No. BPJS</label>
                                <input type="text" name="no_bpjs"
                                       class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <select name="status"
                                        class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                    <option value="pending">Pending</option>
                                    <option value="diterima">Diterima</option>
                                    <option value="ditolak">Ditolak</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </section>

            </div>

            <div class="shrink-0 flex justify-between items-center gap-2 border-t px-4 py-3">
                <button type="button" data-nav-prev class="invisible px-4 py-2 rounded-lg bg-gray-100 text-gray-600 text-sm"
                        onclick="wizardGo('editForm', Number(document.getElementById('editForm').dataset.step) - 1)">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </button>

                <div class="flex gap-2">
                    <button type="button" data-nav-next
                            class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm hover:bg-blue-700"
                            onclick="wizardNext('editForm')">
                        Berikutnya <i class="fas fa-arrow-right ml-1"></i>
                    </button>

                    <button type="submit" data-nav-submit
                            class="hidden px-4 py-2 rounded-lg bg-blue-600 text-white text-sm hover:bg-blue-700">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ==================== Modal Jadikan Karyawan ==================== -->
<div id="karyawanModal" class="hidden fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl w-full max-w-md">

        <div class="px-5 py-4 rounded-t-xl text-white flex justify-between items-center"
             style="background: linear-gradient(to right, #16a34a, #2563eb);">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center">
                    <i class="fas fa-user-check text-white text-sm"></i>
                </div>
                <h2 class="font-semibold text-sm">Jadikan Karyawan</h2>
            </div>
            <button type="button" onclick="closeKaryawanModal()"
                    class="w-7 h-7 flex items-center justify-center rounded-full hover:bg-white/20 text-xl leading-none">
                &times;
            </button>
        </div>

        <form id="karyawanForm" action="" method="POST" class="p-5 space-y-4">
            @csrf

            <p class="text-sm text-gray-600">
                Kandidat: <strong id="namaKandidat"></strong>
            </p>

            <div>
                <label class="block text-sm font-medium text-gray-700">ID Departemen</label>
                <input type="number" name="departemen_id" placeholder="Kosongkan kalau belum ada"
                       class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Tanggal Masuk</label>
                <input type="date" name="tanggal_masuk" value="{{ now()->format('Y-m-d') }}"
                       class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeKaryawanModal()"
                        class="px-4 py-2 bg-gray-200 rounded-lg text-sm">Batal</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm">
                    Jadikan Karyawan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ==================== Script ==================== --}}
<script>
/* ============ Riwayat Pendidikan ============ */
const INP = 'w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm';
const JENJANG = ['SD', 'SMP', 'SMA/SMK', 'D3', 'S1', 'S2', 'S3'];

function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>'"]/g, char => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;'
    })[char]);
}

function tambahPendidikan(containerId, values) {
    const wrap = document.getElementById(containerId);
    if (!wrap) return;

    const i = parseInt(wrap.dataset.index || '0', 10);
    wrap.dataset.index = i + 1;

    const v = values || {};

    const row = document.createElement('div');
    row.className = 'pendidikan-row border border-gray-200 rounded-lg p-3 bg-gray-50 space-y-2';
    row.innerHTML = `
        <div class="grid grid-cols-2 gap-2">
            <select name="pendidikan[${i}][jenjang]" class="${INP}">
                <option value="">Jenjang</option>
                ${JENJANG.map(j => `<option value="${j}">${j}</option>`).join('')}
            </select>
            <input type="text" name="pendidikan[${i}][nama_sekolah]" placeholder="Nama sekolah" class="${INP}">
        </div>
        <div class="grid grid-cols-3 gap-2">
            <input type="text" name="pendidikan[${i}][jurusan]" placeholder="Jurusan" class="${INP}">
            <input type="text" name="pendidikan[${i}][gelar]" placeholder="Gelar (S.Kom)" class="${INP}">
            <input type="text" name="pendidikan[${i}][lokasi]" placeholder="Lokasi" class="${INP}">
        </div>
        <div class="grid grid-cols-3 gap-2">
            <input type="number" name="pendidikan[${i}][tahun_masuk]" placeholder="Masuk" class="${INP}">
            <input type="number" name="pendidikan[${i}][tahun_lulus]" placeholder="Lulus" class="${INP}">
            <input type="text" name="pendidikan[${i}][ipk_atau_nilai]" placeholder="IPK / Nilai" class="${INP}">
        </div>
        <button type="button" onclick="this.closest('.pendidikan-row').remove()"
                class="text-xs text-red-500 hover:text-red-700">
            <i class="fas fa-trash mr-1"></i> Hapus baris
        </button>`;
    wrap.appendChild(row);

    const set = (field, val) => {
        const el = row.querySelector(`[name="pendidikan[${i}][${field}]"]`);
        if (el) el.value = (val === null || val === undefined) ? '' : val;
    };
    set('jenjang', v.jenjang);
    set('nama_sekolah', v.nama_sekolah);
    set('jurusan', v.jurusan);
    set('gelar', v.gelar);
    set('lokasi', v.lokasi);
    set('tahun_masuk', v.tahun_masuk);
    set('tahun_lulus', v.tahun_lulus);
    set('ipk_atau_nilai', v.ipk_atau_nilai);
}

function kosongkanPendidikan(containerId, withBlankRow = true) {
    const wrap = document.getElementById(containerId);
    if (!wrap) return;
    wrap.innerHTML = '';
    wrap.dataset.index = 0;
    if (withBlankRow) tambahPendidikan(containerId);
}

function hitungUsia(tanggalLahir) {
    const tanggal = normalisasiTanggal(tanggalLahir);
    if (!tanggal) return '';
    const hariIni = new Date();
    const lahir = new Date(`${tanggal}T00:00:00`);
    let usia = hariIni.getFullYear() - lahir.getFullYear();
    if (hariIni.getMonth() < lahir.getMonth() ||
        (hariIni.getMonth() === lahir.getMonth() && hariIni.getDate() < lahir.getDate())) usia--;
    return usia >= 0 ? `${usia} tahun` : '';
}

function normalisasiTanggal(value) {
    const tanggal = String(value || '').trim();
    if (/^\d{4}-\d{2}-\d{2}$/.test(tanggal)) return tanggal;

    const cocok = tanggal.match(/^(\d{1,2})[\/-](\d{1,2})[\/-](\d{4})$/);
    if (!cocok) return '';

    const [, hari, bulan, tahun] = cocok;
    const hasil = `${tahun}-${bulan.padStart(2, '0')}-${hari.padStart(2, '0')}`;
    const date = new Date(`${hasil}T00:00:00`);
    return date.getFullYear() === Number(tahun) &&
        date.getMonth() + 1 === Number(bulan) && date.getDate() === Number(hari)
        ? hasil : '';
}

function tampilanTanggal(value) {
    const tanggal = normalisasiTanggal(value);
    if (!tanggal) return value || '';
    const [tahun, bulan, hari] = tanggal.split('-');
    return `${hari}/${bulan}/${tahun}`;
}

function normalisasiTanggalKeluarga(form) {
    form.querySelectorAll('.keluarga-tanggal-lahir').forEach(input => {
        const tanggal = normalisasiTanggal(input.value);
        input.setCustomValidity(tanggal ? '' : 'Gunakan format tanggal DD/MM/YYYY yang valid.');
        if (tanggal) input.value = tanggal;
    });
}

function tambahKeluarga(containerId, values) {
    const wrap = document.getElementById(containerId);
    if (!wrap) return;

    const i = parseInt(wrap.dataset.index || '0', 10);
    wrap.dataset.index = i + 1;
    const v = values || {};
    const row = document.createElement('div');
    row.className = 'keluarga-row border border-emerald-200 rounded-lg p-3 bg-emerald-50 space-y-2';
    row.innerHTML = `
        ${v.id ? `<input type="hidden" name="keluarga[${i}][id]" value="${escapeHtml(v.id)}">` : ''}
        <div class="grid grid-cols-2 gap-2">
            <input type="text" name="keluarga[${i}][nama]" value="${escapeHtml(v.nama)}" placeholder="Nama lengkap" required class="${INP}">
            <select name="keluarga[${i}][status]" required class="${INP}">
                <option value="">Status hubungan</option>
                <option value ="Ayah" ${v.status === 'Ayah' ? 'selected' : ''}>Ayah</option>
                <option value="Ibu" ${v.status === 'Ibu' ? 'selected' : ''}>Ibu</option>
                <option value="Suami" ${v.status === 'Suami' ? 'selected' : ''}>Suami</option>
                <option value="Istri" ${v.status === 'Istri' ? 'selected' : ''}>Istri</option>
                <option value="Anak" ${v.status === 'Anak' ? 'selected' : ''}>Anak</option>
            </select>
        </div>
        <div class="grid grid-cols-3 gap-2">
            <input type="text" name="keluarga[${i}][tempat_lahir]" value="${escapeHtml(v.tempat_lahir)}" placeholder="Tempat lahir" required class="${INP}">
            <input type="text" name="keluarga[${i}][tanggal_lahir]" value="${tampilanTanggal(v.tanggal_lahir)}" placeholder="DD/MM/YYYY" inputmode="numeric" maxlength="10" required class="${INP} keluarga-tanggal-lahir">
            <input type="text" value="${hitungUsia(v.tanggal_lahir)}" placeholder="Usia otomatis" readonly class="${INP} bg-gray-100 keluarga-usia">
        </div>
        <button type="button" onclick="this.closest('.keluarga-row').remove()" class="text-xs text-red-500 hover:text-red-700">
            <i class="fas fa-trash mr-1"></i> Hapus anggota
        </button>`;
    wrap.appendChild(row);

    row.querySelector('.keluarga-tanggal-lahir').addEventListener('input', function () {
        this.setCustomValidity(normalisasiTanggal(this.value) || !this.value ? '' : 'Gunakan format tanggal DD/MM/YYYY yang valid.');
        row.querySelector('.keluarga-usia').value = hitungUsia(this.value);
    });
}

function kosongkanKeluarga(containerId) {
    const wrap = document.getElementById(containerId);
    if (!wrap) return;
    wrap.innerHTML = '';
    wrap.dataset.index = 0;
}

/* ============ Wizard ============ */
function wizardGo(formId, step) {
    const form = document.getElementById(formId);
    if (!form) return;

    const sections = form.querySelectorAll('[data-wizard-step]');
    const total = sections.length;
    step = Math.min(Math.max(Number(step) || 1, 1), total);

    sections.forEach(s => s.classList.toggle('hidden', Number(s.dataset.wizardStep) !== step));
    form.querySelectorAll('[data-step-button]').forEach(b =>
        b.classList.toggle('active', Number(b.dataset.stepButton) === step));

    form.querySelectorAll('[data-nav-prev]').forEach(b => b.classList.toggle('invisible', step === 1));
    form.querySelectorAll('[data-nav-next]').forEach(b => b.classList.toggle('hidden', step === total));
    form.querySelectorAll('[data-nav-submit]').forEach(b => b.classList.toggle('hidden', step !== total));

    form.dataset.step = step;

    const body = form.querySelector('.overflow-y-auto');
    if (body) body.scrollTop = 0;
}

function wizardNext(formId) {
    const form = document.getElementById(formId);
    if (!form) return;

    const step = Number(form.dataset.step || 1);
    const section = form.querySelector(`[data-wizard-step="${step}"]`);
    if (!section) return;

    // validasi field di slide yang sedang tampil aja
    const invalid = Array.from(section.querySelectorAll('input, select, textarea'))
        .find(el => !el.checkValidity() && el.type !== 'hidden');

    if (invalid) { invalid.reportValidity(); return; }

    wizardGo(formId, step + 1);
}

/* ============ Modal ============ */
function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
}

function openAddModal() {
    const form = document.getElementById('addForm');
    form.reset();
    kosongkanPendidikan('pendidikanRowsAdd', false);
    wizardGo('addForm', 1);
    document.getElementById('modal').classList.remove('hidden');
}

function openEditModal(btn) {
    const d = btn.dataset;
    const form = document.getElementById('editForm');
    if (!form) { console.error('#editForm nggak ketemu'); return; }

    form.action = d.updateUrl;

    const setVal = (name, val) => {
        const el = form.querySelector(`[name="${name}"]`);
        if (el) el.value = (val === undefined || val === null) ? '' : val;
    };

    setVal('nama_lengkap', d.nama_lengkap);
    setVal('nik', d.nik);
    setVal('no_telepon', d.no_telepon);
    setVal('divisi', d.divisi);
    setVal('tgl_lamar', d.tgl_lamar);
    setVal('npwp', d.npwp);
    setVal('no_bpjs', d.no_bpjs);
    setVal('status', d.status);

    // Preview file lama
    const wrap = document.getElementById('editFotoWrap');
    const img  = document.getElementById('editFotoImg');
    if (d.foto) { img.src = d.foto; wrap.classList.remove('hidden'); }
    else { img.src = ''; wrap.classList.add('hidden'); }

    const ktp = document.getElementById('editKtpLink');
    if (d.ktp) { ktp.href = d.ktp; ktp.classList.remove('hidden'); } else { ktp.classList.add('hidden'); }

    const cv = document.getElementById('editCvLink');
    if (d.cv) { cv.href = d.cv; cv.classList.remove('hidden'); } else { cv.classList.add('hidden'); }

    // Riwayat pendidikan
    let list = [];
    try { list = JSON.parse(d.pendidikan || '[]'); } catch (e) { list = []; }

    if (list.length === 0) {
        kosongkanPendidikan('pendidikanRowsEdit', false);
    } else {
        kosongkanPendidikan('pendidikanRowsEdit', false);
        list.forEach(p => tambahPendidikan('pendidikanRowsEdit', p));
    }

    wizardGo('editForm', 1);
    document.getElementById('editModal').classList.remove('hidden');
}

/* ============ Modal Jadikan Karyawan ============ */
function openKaryawanModal(btn) {
    document.getElementById('karyawanForm').action = btn.dataset.url;
    document.getElementById('namaKandidat').textContent = btn.dataset.nama;
    document.getElementById('karyawanModal').classList.remove('hidden');
}

function closeKaryawanModal() {
    document.getElementById('karyawanModal').classList.add('hidden');
}

let hrdSortState = { column: null, direction: 'asc' };
const hrdSearchInput = document.getElementById('hrdSearch');
const hrdStatusFilter = document.getElementById('hrdStatusFilter');
const hrdDivisionFilter = document.getElementById('hrdDivisionFilter');
const hrdFilterEmpty = document.getElementById('hrdFilterEmpty');
let hrdCandidateRows = Array.from(document.querySelectorAll('tr.hrd-kandidat-row'));
const hrdNoDataRow = document.querySelector('.hrd-no-data-row');
const hrdPageSizeSelect = document.getElementById('hrdPageSize');
const hrdPaginationInfo = document.getElementById('hrdPaginationInfo');
const hrdPagination = document.getElementById('hrdPagination');
let hrdCurrentPage = 1;
let hrdPageSize = Number(hrdPageSizeSelect.value) || 10;

function getFilteredHrdRows() {
    const query = hrdSearchInput?.value.trim().toLocaleLowerCase('id') || '';
    const selectedStatus = hrdStatusFilter?.value || '';
    const selectedDivision = hrdDivisionFilter?.value.toLocaleLowerCase('id') || '';

    return hrdCandidateRows.filter(row => {
        const matchesSearch = (row.dataset.search || '').toLocaleLowerCase('id').includes(query);
        const matchesStatus = !selectedStatus || row.dataset.status === selectedStatus;
        const matchesDivision = !selectedDivision || row.dataset.division === selectedDivision;
        return matchesSearch && matchesStatus && matchesDivision;
    });
}

function makeHrdPageButton(label, page, options = {}) {
    const button = document.createElement('button');
    button.type = 'button';
    button.innerHTML = label;
    button.disabled = Boolean(options.disabled);
    button.setAttribute('aria-label', options.ariaLabel || `Halaman ${page}`);

    const baseClasses = 'min-w-9 rounded-md border px-3 py-2 text-sm transition-colors';
    button.className = options.active
        ? `${baseClasses} border-indigo-600 bg-indigo-600 text-white`
        : `${baseClasses} border-gray-300 bg-white text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40`;

    if (!button.disabled && !options.active) {
        button.addEventListener('click', () => {
            hrdCurrentPage = page;
            renderHrdPagination();
        });
    }

    return button;
}

function renderHrdPagination() {
    const filteredRows = getFilteredHrdRows();
    const totalRows = filteredRows.length;
    const pageCount = Math.max(1, Math.ceil(totalRows / hrdPageSize));
    hrdCurrentPage = Math.min(hrdCurrentPage, pageCount);

    const startIndex = totalRows ? (hrdCurrentPage - 1) * hrdPageSize : 0;
    const endIndex = Math.min(startIndex + hrdPageSize, totalRows);
    const visibleRows = new Set(filteredRows.slice(startIndex, endIndex));

    hrdCandidateRows.forEach(row => row.classList.toggle('hidden', !visibleRows.has(row)));
    if (hrdNoDataRow) hrdNoDataRow.classList.toggle('hidden', hrdCandidateRows.length > 0);
    hrdFilterEmpty.classList.toggle('hidden', totalRows > 0 || hrdCandidateRows.length === 0);

    filteredRows.slice(startIndex, endIndex).forEach((row, index) => {
        row.cells[0].textContent = startIndex + index + 1;
    });

    hrdPaginationInfo.textContent = totalRows
        ? `Menampilkan ${startIndex + 1} sampai ${endIndex} dari ${totalRows} hasil`
        : 'Menampilkan 0 hasil';

    hrdPagination.replaceChildren();
    hrdPagination.appendChild(makeHrdPageButton('<i class="fas fa-angle-left"></i>', Math.max(1, hrdCurrentPage - 1), {
        disabled: hrdCurrentPage === 1,
        ariaLabel: 'Halaman sebelumnya',
    }));

    const pageNumbers = new Set([1, pageCount, hrdCurrentPage - 1, hrdCurrentPage, hrdCurrentPage + 1]);
    const pages = [...pageNumbers].filter(page => page >= 1 && page <= pageCount).sort((a, b) => a - b);
    let previousPage = 0;

    pages.forEach(page => {
        if (previousPage && page - previousPage > 1) {
            const ellipsis = document.createElement('span');
            ellipsis.className = 'px-1 text-sm text-gray-400';
            ellipsis.textContent = '...';
            hrdPagination.appendChild(ellipsis);
        }

        hrdPagination.appendChild(makeHrdPageButton(String(page), page, {
            active: page === hrdCurrentPage,
            ariaLabel: `Halaman ${page}${page === hrdCurrentPage ? ', aktif' : ''}`,
        }));
        previousPage = page;
    });

    hrdPagination.appendChild(makeHrdPageButton('<i class="fas fa-angle-right"></i>', Math.min(pageCount, hrdCurrentPage + 1), {
        disabled: hrdCurrentPage === pageCount,
        ariaLabel: 'Halaman berikutnya',
    }));
}

hrdSearchInput?.addEventListener('input', () => {
    hrdCurrentPage = 1;
    renderHrdPagination();
});
hrdStatusFilter?.addEventListener('change', () => {
    hrdCurrentPage = 1;
    renderHrdPagination();
});
hrdDivisionFilter?.addEventListener('change', () => {
    hrdCurrentPage = 1;
    renderHrdPagination();
});
document.getElementById('hrdFilterReset')?.addEventListener('click', () => {
    if (hrdSearchInput) hrdSearchInput.value = '';
    if (hrdStatusFilter) hrdStatusFilter.value = '';
    if (hrdDivisionFilter) hrdDivisionFilter.value = '';
    hrdCurrentPage = 1;
    renderHrdPagination();
});
hrdPageSizeSelect.addEventListener('change', () => {
    hrdPageSize = Number(hrdPageSizeSelect.value) || 10;
    hrdCurrentPage = 1;
    renderHrdPagination();
});

document.querySelectorAll('[data-sort-column]').forEach(button => {
    button.addEventListener('click', () => {
        const column = Number(button.dataset.sortColumn);
        const direction = hrdSortState.column === column && hrdSortState.direction === 'asc' ? 'desc' : 'asc';
        const tbody = button.closest('table').tBodies[0];
        const rows = Array.from(tbody.querySelectorAll('tr.hrd-kandidat-row'));

        rows.sort((first, second) => {
            const firstValue = first.cells[column]?.textContent.trim() || '';
            const secondValue = second.cells[column]?.textContent.trim() || '';
            const comparison = firstValue.localeCompare(secondValue, 'id', { numeric: true, sensitivity: 'base' });
            return direction === 'asc' ? comparison : -comparison;
        });

        rows.forEach(row => tbody.appendChild(row));
        hrdCandidateRows = rows;
        hrdSortState = { column, direction };

        document.querySelectorAll('[data-sort-column]').forEach(sortButton => {
            const header = sortButton.closest('th');
            const icon = sortButton.querySelector('i');
            const active = Number(sortButton.dataset.sortColumn) === column;
            header.setAttribute('aria-sort', active ? (direction === 'asc' ? 'ascending' : 'descending') : 'none');
            icon.className = active
                ? `fas fa-sort-${direction === 'asc' ? 'up' : 'down'} text-blue-600`
                : 'fas fa-sort text-gray-400';
        });

            hrdCurrentPage = 1;
            renderHrdPagination();
    });
});

        renderHrdPagination();

const hrdKeluargaState = { karyawanId: null, records: [] };

function hrdKeluargaStatusClass(status) {
    return status === 'Suami' ? 'bg-blue-100 text-blue-800' :
        status === 'Istri' ? 'bg-pink-100 text-pink-800' : 'bg-amber-100 text-amber-800';
}

function hrdFormatTanggal(value) {
    const iso = normalisasiTanggal(value);
    return iso ? iso.split('-').reverse().join('/') : (value || '-');
}

function renderHrdKeluargaList() {
    const list = document.getElementById('hrdKeluargaList');
    if (!hrdKeluargaState.records.length) {
        list.innerHTML = '<div class="rounded-lg border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500">Belum ada data keluarga.</div>';
        return;
    }

    list.innerHTML = hrdKeluargaState.records.map(record => `
        <div class="flex items-center justify-between gap-3 rounded-lg border border-gray-200 bg-white p-3 shadow-sm">
            <div class="min-w-0">
                <div class="font-medium text-sm text-gray-800">${escapeHtml(record.nama)}</div>
                <div class="text-xs text-gray-500">${escapeHtml(record.tempat_lahir)}, ${hrdFormatTanggal(record.tanggal_lahir)} &middot; ${hitungUsia(record.tanggal_lahir)} </div>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <span class="rounded-full px-2 py-1 text-xs font-medium ${hrdKeluargaStatusClass(record.status)}">${escapeHtml(record.status)}</span>
                <button type="button" onclick="editHrdKeluarga(${record.id})" class="text-blue-600 hover:text-blue-800" title="Edit"><i class="fas fa-edit"></i></button>
                <form action="/keluarga/${record.id}" method="POST" class="inline" onsubmit="openHrdKeluargaDelete(event, this)">
                    @csrf
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus"><i class="fas fa-trash"></i></button>
                </form>
            </div>
        </div>`).join('');
}

function openHrdKeluargaModal(button) {
    hrdKeluargaState.karyawanId = button.dataset.karyawanId;
    try { hrdKeluargaState.records = JSON.parse(button.dataset.keluarga || '[]'); }
    catch (error) { hrdKeluargaState.records = []; }
    document.getElementById('hrdKeluargaPemilik').textContent = `Karyawan: ${button.dataset.karyawanNama}`;
    resetHrdKeluargaForm();
    renderHrdKeluargaList();
    document.getElementById('hrdKeluargaModal').classList.remove('hidden');
}

function openHrdKeluargaForm() {
    resetHrdKeluargaForm();
    openHrdKeluargaFormModal();
}

function openHrdKeluargaFormModal() {
    const modal = document.getElementById('hrdKeluargaFormModal');
    const card = document.getElementById('hrdKeluargaFormCard');
    modal.classList.remove('hidden');
    requestAnimationFrame(() => {
        modal.classList.remove('opacity-0');
        card.classList.remove('scale-95', 'opacity-0');
    });
}

function editHrdKeluarga(id) {
    const record = hrdKeluargaState.records.find(item => item.id === id);
    if (!record) return;
    document.getElementById('hrdKeluargaForm').action = `/keluarga/${id}`;
    document.getElementById('hrdKeluargaMethod').value = 'PUT';
    document.getElementById('hrdKeluargaFormTitle').innerHTML = '<i class="fas fa-edit mr-2"></i>Edit Anggota Keluarga';
    document.getElementById('hrdKeluargaNama').value = record.nama;
    document.getElementById('hrdKeluargaTempat').value = record.tempat_lahir;
    document.getElementById('hrdKeluargaTanggal').value = normalisasiTanggal(record.tanggal_lahir);
    document.getElementById('hrdKeluargaUsia').value = hitungUsia(record.tanggal_lahir);
    document.getElementById('hrdKeluargaStatus').value = record.status;
    openHrdKeluargaFormModal();
}

function resetHrdKeluargaForm() {
    const form = document.getElementById('hrdKeluargaForm');
    form.reset();
    form.action = `/karyawan/${hrdKeluargaState.karyawanId}/keluarga`;
    document.getElementById('hrdKeluargaMethod').value = 'POST';
    document.getElementById('hrdKeluargaFormTitle').innerHTML = '<i class="fas fa-plus-circle mr-2"></i>Tambah Anggota Keluarga';
    document.getElementById('hrdKeluargaUsia').value = '';
}

function closeHrdKeluargaModal() {
    document.getElementById('hrdKeluargaModal').classList.add('hidden');
    closeHrdKeluargaForm();
}

function closeHrdKeluargaForm() {
    const modal = document.getElementById('hrdKeluargaFormModal');
    const card = document.getElementById('hrdKeluargaFormCard');
    modal.classList.add('opacity-0');
    card.classList.add('scale-95', 'opacity-0');
    setTimeout(() => modal.classList.add('hidden'), 200);
}

function showHrdKeluargaToast(message, type = 'success') {
    const toast = document.getElementById('hrdKeluargaToast');
    toast.textContent = message;
    toast.className = type === 'success'
        ? 'mx-5 mt-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 opacity-0 -translate-y-2 transition-all duration-300'
        : 'mx-5 mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 opacity-0 -translate-y-2 transition-all duration-300';
    requestAnimationFrame(() => toast.classList.remove('opacity-0', '-translate-y-2'));
    clearTimeout(window.hrdKeluargaToastTimer);
    window.hrdKeluargaToastTimer = setTimeout(() => {
        toast.classList.add('opacity-0', '-translate-y-2');
        setTimeout(() => toast.classList.add('hidden'), 300);
    }, 3500);
}

async function readHrdKeluargaResponse(response, fallback) {
    let result = {};
    try { result = await response.json(); } catch (error) { /* response bukan JSON */ }
    if (response.status === 401) throw new Error('Sesi login sudah berakhir. Silakan login kembali.');
    if (!response.ok) {
        const validation = result.errors ? Object.values(result.errors).flat().join(' ') : '';
        throw new Error(result.message || validation || fallback);
    }
    return result;
}

let hrdKeluargaDeleteForm = null;

function openHrdKeluargaDelete(event, form) {
    event.preventDefault();
    hrdKeluargaDeleteForm = form;
    const modal = document.getElementById('hrdKeluargaDeleteModal');
    const card = document.getElementById('hrdKeluargaDeleteCard');
    modal.classList.remove('hidden');
    requestAnimationFrame(() => {
        modal.classList.remove('opacity-0');
        card.classList.remove('scale-95', 'opacity-0');
    });
}

function closeHrdKeluargaDelete() {
    const modal = document.getElementById('hrdKeluargaDeleteModal');
    const card = document.getElementById('hrdKeluargaDeleteCard');
    modal.classList.add('opacity-0');
    card.classList.add('scale-95', 'opacity-0');
    setTimeout(() => modal.classList.add('hidden'), 200);
    hrdKeluargaDeleteForm = null;
}

async function confirmHrdKeluargaDelete() {
    const form = hrdKeluargaDeleteForm;
    if (!form) return;
    closeHrdKeluargaDelete();

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { Accept: 'application/json' },
        });

        await readHrdKeluargaResponse(response, 'Gagal menghapus data keluarga.');
        hrdKeluargaState.records = hrdKeluargaState.records.filter(record => `/keluarga/${record.id}` !== form.action);
        renderHrdKeluargaList();
        showHrdKeluargaToast('Data keluarga berhasil dihapus.');
    } catch (error) {
        showHrdKeluargaToast(error.message, 'error');
    }
}

document.getElementById('hrdKeluargaTanggal').addEventListener('input', function () {
    document.getElementById('hrdKeluargaUsia').value = hitungUsia(this.value);
    this.setCustomValidity(normalisasiTanggal(this.value) ? '' : 'Gunakan format tanggal DD/MM/YYYY yang valid.');
});
document.getElementById('hrdKeluargaForm').addEventListener('submit', async function (event) {
    event.preventDefault();
    const input = document.getElementById('hrdKeluargaTanggal');
    const tanggal = normalisasiTanggal(input.value);
    input.setCustomValidity(tanggal ? '' : 'Gunakan format tanggal DD/MM/YYYY yang valid.');
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
        const result = await readHrdKeluargaResponse(response, 'Data keluarga gagal disimpan.');

        const saved = result.data;
        const savedRecord = {
            id: saved.id,
            nama: saved.nama,
            tempat_lahir: saved.tempat_lahir,
            tanggal_lahir: saved.tanggal_lahir,
            status: saved.status,
        };
        const existingIndex = hrdKeluargaState.records.findIndex(record => record.id === savedRecord.id);
        if (existingIndex >= 0) hrdKeluargaState.records[existingIndex] = savedRecord;
        else hrdKeluargaState.records.unshift(savedRecord);

        renderHrdKeluargaList();
        closeHrdKeluargaForm();
        showHrdKeluargaToast(
            existingIndex >= 0 ? 'Data keluarga berhasil diperbarui.' : 'Data keluarga berhasil ditambahkan.'
        );
    } catch (error) {
        showHrdKeluargaToast(error.message, 'error');
    }
});
document.getElementById('hrdKeluargaModal').addEventListener('click', event => {
    if (event.target.id === 'hrdKeluargaModal') closeHrdKeluargaModal();
});
document.getElementById('hrdKeluargaFormModal').addEventListener('click', event => {
    if (event.target.id === 'hrdKeluargaFormModal') closeHrdKeluargaForm();
});
document.getElementById('hrdKeluargaDeleteModal').addEventListener('click', event => {
    if (event.target.id === 'hrdKeluargaDeleteModal') closeHrdKeluargaDelete();
});

/* ============ Init ============ */
document.addEventListener('DOMContentLoaded', function () {
    kosongkanPendidikan('pendidikanRowsAdd', false);
    wizardGo('addForm', 1);
    wizardGo('editForm', 1);

    document.querySelectorAll('input[type="file"][name="foto"]').forEach(function (input) {
        input.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;

            if (file.size > 2 * 1024 * 1024) {
                this.value = '';
                alert('Foto terlalu besar. Ukuran maksimal foto adalah 2 MB.');
            }
        });
    });
});

// Kalau ada field invalid di slide lain, balik ke slide itu sebelum submit
['addForm', 'editForm'].forEach(function (fid) {
    const form = document.getElementById(fid);
    if (!form) return;

    form.addEventListener('submit', function (e) {
        normalisasiTanggalKeluarga(form);
        if (!form.checkValidity()) {
            e.preventDefault();
            const bad = form.querySelector(':invalid');
            if (bad) {
                const section = bad.closest('[data-wizard-step]');
                if (section) wizardGo(fid, Number(section.dataset.wizardStep));
                bad.reportValidity();
            }
        }
    });
});

// Tutup klik area gelap
['modal', 'editModal', 'karyawanModal'].forEach(function (id) {
    const el = document.getElementById(id);
    if (el) el.addEventListener('click', e => { if (e.target === el) el.classList.add('hidden'); });
});

// Tutup pakai Escape
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        if (!document.getElementById('hrdKeluargaFormModal').classList.contains('hidden')) closeHrdKeluargaForm();
        else if (!document.getElementById('hrdKeluargaDeleteModal').classList.contains('hidden')) closeHrdKeluargaDelete();
        else if (!document.getElementById('hrdKeluargaModal').classList.contains('hidden')) closeHrdKeluargaModal();
        closeModal('modal');
        closeModal('editModal');
        closeKaryawanModal();
    }
});
</script>
@endsection
