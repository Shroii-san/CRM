@props(['organizations' => [], 'currentMenuId' => null])

<div class="fade-in m-0 p-0">
    <!-- Table ONLY -->
    <div class="overflow-x-auto m-0 p-0">
        <table id="companyTable" class="w-full m-0 border-collapse">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th
                        class="px-3 py-2 text-left text-[0.7rem] font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                        No</th>
                    <th @click="onSort('name')" class="px-4 py-3 cursor-pointer select-none hover:bg-gray-100">
                        <div class="flex items-center gap-2">
                            <span
                                class="px-3 py-2 text-left text-[0.7rem] font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Nama
                                Perusahaan</span>

                            <!-- Area Ikon Panah -->
                            <span class="inline-flex">
                                <!-- 1. Panah Atas (Tampil jika kolom 'name' aktif & asc) -->
                                <svg x-show="sortBy === 'name' && sortDir === 'asc'" class="w-4 h-4 text-blue-600"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 15l7-7 7 7" />
                                </svg>

                                <!-- 2. Panah Bawah (Tampil jika kolom 'name' aktif & desc) -->
                                <svg x-show="sortBy === 'name' && sortDir === 'desc'" class="w-4 h-4 text-blue-600"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>

                                <!-- 3. Panah Netral/Atas-Bawah (Tampil jika kolom 'name' sedang TIDAK di-sort) -->
                                <svg x-show="sortBy !== 'name'" class="w-4 h-4 text-gray-400 opacity-40" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                </svg>
                            </span>
                        </div>
                    </th>
                    <th @click="onSort('industry_name')" class="px-4 py-3 cursor-pointer select-none hover:bg-gray-100">
                        <div class="flex items-center gap-2">
                            <span
                                class="px-3 py-2 text-left text-[0.7rem] font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Jenis
                                Industri</span>

                            <!-- Area Ikon Panah -->
                            <span class="inline-flex">
                                <!-- 1. Panah Atas (Tampil jika kolom 'industry_name' aktif & asc) -->
                                <svg x-show="sortBy === 'industry_name' && sortDir === 'asc'"
                                    class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 15l7-7 7 7" />
                                </svg>

                                <!-- 2. Panah Bawah (Tampil jika kolom 'industry_name' aktif & desc) -->
                                <svg x-show="sortBy === 'industry_name' && sortDir === 'desc'"
                                    class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>

                                <!-- 3. Panah Netral/Atas-Bawah (Tampil jika kolom 'industry_name' sedang TIDAK di-sort) -->
                                <svg x-show="sortBy !== 'industry_name'" class="w-4 h-4 text-gray-400 opacity-40"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                </svg>
                            </span>
                        </div>
                    </th>
                    <th @click="onSort('tier')" class="px-4 py-3 cursor-pointer select-none hover:bg-gray-100">
                        <div class="flex items-center gap-2">
                            <span
                                class="px-3 py-2 text-left text-[0.7rem] font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Tier</span>

                            <!-- Area Ikon Panah -->
                            <span class="inline-flex">
                                <!-- 1. Panah Atas (Tampil jika kolom 'tier' aktif & asc) -->
                                <svg x-show="sortBy === 'tier' && sortDir === 'asc'" class="w-4 h-4 text-blue-600"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 15l7-7 7 7" />
                                </svg>

                                <!-- 2. Panah Bawah (Tampil jika kolom 'tier' aktif & desc) -->
                                <svg x-show="sortBy === 'tier' && sortDir === 'desc'" class="w-4 h-4 text-blue-600"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>

                                <!-- 3. Panah Netral/Atas-Bawah (Tampil jika kolom 'tier' sedang TIDAK di-sort) -->
                                <svg x-show="sortBy !== 'tier'" class="w-4 h-4 text-gray-400 opacity-40" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                </svg>
                            </span>
                        </div>
                    </th>
                    <th
                        class="px-3 py-2 text-left text-[0.7rem] font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                        Deskripsi</th>
                    <th
                        class="px-3 py-2 text-left text-[0.7rem] font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                        Status</th>
                    <th
                        class="px-3 py-2 text-right text-[0.7rem] font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                        Aksi</th>
                </tr>
            </thead>
            <tbody id="organizationTableBody" class="divide-y divide-gray-200">
                <template x-if="loading">
                    <tr>
                        <td colspan="7" class="px-3 py-6 text-center text-gray-500">
                            <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                            <p class="text-xs">Memuat data organisasi...</p>
                        </td>
                    </tr>
                </template>

                <template x-if="!loading && items.length === 0">
                    <tr>
                        <td colspan="7" class="px-3 py-6 text-center text-gray-500">
                            <i class="fas fa-building text-3xl mb-2"></i>
                            <p class="text-xs">Belum ada data organisasi</p>
                        </td>
                    </tr>
                </template>

                <template x-if="!loading && items.length > 0">
                    <template x-for="(item, index) in items" :key="item.id || index">
                        <tr class="border-b border-gray-200 transition-colors duration-150 hover:bg-gray-50">
                            <td class="px-3 py-2 text-[0.8125rem] text-gray-900 whitespace-nowrap">
                                <span class="font-medium" x-text="((page - 1) * perPage) + index + 1"></span>
                            </td>
                            <td class="px-3 py-2 text-[0.8125rem] font-medium text-gray-900" x-text="item.name || '-'">
                            </td>
                            <td class="px-3 py-2 text-[0.8125rem] text-gray-900" x-text="item.industry?.name || '-'">
                            </td>
                            <td class="px-3 py-2 text-[0.8125rem] text-gray-900" x-text="item.tier || '-'"></td>
                            <td class="px-3 py-2 text-[0.8125rem] text-gray-900" x-text="item.description || '-'"></td>
                            <td class="px-3 py-2 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[0.6875rem] font-medium"
                                    :class="item.is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'"
                                    x-text="item.is_active ? 'Active' : 'Inactive'"></span>
                            </td>
                            <td class="px-3 py-2 text-right whitespace-nowrap">
                                <button @click="showDetail(item.id)" class="text-indigo-600 mr-2 text-sm"
                                    title="Detail">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @if(!$currentMenuId || auth()->user()?->canAccess($currentMenuId, 'update'))
                                    <button @click="openEditCompanyModal(item)"
                                        class="text-blue-600 mr-2 text-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                @endif
                                @if(!$currentMenuId || auth()->user()?->canAccess($currentMenuId, 'delete'))
                                    <button @click="deleteCompanyItem(item.id)"
                                        class="text-red-600 text-sm" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    </template>
                </template>

            </tbody>
        </table>
    </div>
</div>

{{-- Company Detail Modal - ROUNDED KONSISTEN --}}
<div id="companyDetailModal"
    onclick="if (event.target === this) closeCompanyDetailModal()"
    class="hidden fixed inset-0 w-full h-full bg-black/50 z-[9999] items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-[700px] max-h-[90vh] flex flex-col shadow-2xl overflow-hidden animate-modal-in">

        {{-- Modal Header - FIXED --}}
        <div style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); padding: 1rem 1.5rem; flex-shrink: 0;">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div style="width: 2.5rem; height: 2.5rem; background-color: rgba(255, 255, 255, 0.2); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="fas fa-building text-white text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-white m-0 leading-tight">Detail Perusahaan</h3>
                        <p class="text-xs text-indigo-100 mt-0.5 m-0">Informasi profil dan kontak organisasi</p>
                    </div>
                </div>
                <button type="button" onclick="closeCompanyDetailModal()"
                    class="text-white hover:bg-white/20 rounded-full w-9 h-9 flex items-center justify-center transition-colors cursor-pointer border-0 bg-transparent"
                    title="Tutup">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>

        {{-- Modal Body - SCROLLABLE --}}
        <div class="overflow-y-auto flex-1 p-6 flex flex-col gap-6">

            {{-- Logo --}}
            <div class="text-center">
                <div id="detailLogoContainer" class="flex justify-center items-center min-h-[120px]">
                    <p class="text-gray-400 text-sm">Loading...</p>
                </div>
            </div>

            {{-- Info Grid --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">Nama</label>
                    <p id="detailCompanyName" class="text-[0.9375rem] font-medium text-gray-900 m-0">-</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">Jenis</label>
                    <p id="detailCompanyType" class="text-[0.9375rem] text-gray-900 m-0">-</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">Tier</label>
                    <p id="detailCompanyTier" class="text-[0.9375rem] text-gray-900 m-0">-</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">Status</label>
                    <span id="detailCompanyStatus"
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium">-</span>
                </div>
            </div>

            {{-- Deskripsi --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">Deskripsi</label>
                <p id="detailCompanyDescription" class="text-[0.9375rem] text-gray-900 m-0 leading-normal">-</p>
            </div>

            {{-- Address --}}
            <div class="border-t border-gray-200 pt-4">
                <h4 class="text-sm font-semibold text-gray-900 mb-3">Lokasi</h4>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Provinsi</label>
                        <p id="detailProvince" class="text-sm text-gray-900 m-0">-</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Kabupaten</label>
                        <p id="detailRegency" class="text-sm text-gray-900 m-0">-</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Kecamatan</label>
                        <p id="detailDistrict" class="text-sm text-gray-900 m-0">-</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Kelurahan</label>
                        <p id="detailVillage" class="text-sm text-gray-900 m-0">-</p>
                    </div>
                </div>
                <div class="mt-3">
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Alamat
                        Lengkap</label>
                    <p id="detailFullAddress" class="text-sm text-gray-900 m-0 leading-normal">
                        -</p>
                </div>
            </div>

            {{-- Contact Info --}}
            <div class="border-t border-gray-200 pt-4">
                <h4 class="text-sm font-semibold text-gray-900 mb-3">Kontak & Media
                </h4>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Telepon</label>
                        <p id="detailCompanyPhone" class="text-sm text-gray-900 m-0">-</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Email</label>
                        <p id="detailCompanyEmail" class="text-sm text-gray-900 m-0">-</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Website</label>
                        <p id="detailCompanyWebsite" class="text-sm m-0"><a id="detailCompanyWebsiteLink" href="#"
                                target="_blank" class="text-blue-500 no-underline hover:underline">-</a></p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">LinkedIn</label>
                        <p id="detailCompanyLinkedin" class="text-sm m-0"><a id="detailCompanyLinkedinLink" href="#"
                                target="_blank" class="text-blue-500 no-underline hover:underline">-</a></p>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Instagram</label>
                        <p id="detailCompanyInstagram" class="text-sm m-0"><a id="detailCompanyInstagramLink" href="#"
                                target="_blank" class="text-blue-500 no-underline hover:underline">-</a>
                        </p>
                    </div>
                </div>
            </div>

            {{-- PICs - COMPACT & ROUNDED --}}
            <div class="border-t border-gray-200 pt-4">
                <div class="flex justify-between items-center mb-3">
                    <h4 class="text-sm font-semibold text-gray-900 m-0">PIC</h4>
                    <span id="picCount"
                        class="text-xs text-white bg-indigo-600 px-2.5 py-1 rounded-full font-semibold">0</span>
                </div>
                <div id="picsContainer" class="flex flex-col gap-2 max-h-[300px] overflow-y-auto">
                    {{-- PICs will be loaded here --}}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Proxy ke Alpine organizationTable.showDetail (didefinisikan di organizations.js)
    function showCompanyDetail(companyId) {
        if (window.showCompanyDetail) {
            window.showCompanyDetail(companyId);
        }
    }

    function closeCompanyDetailModal() {
        const modal = document.getElementById('companyDetailModal');
        if (modal) modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
</script>

<style>
    @media (max-width: 1024px) {
        #companyTable {
            font-size: 0.8125rem;
        }

        #companyTable th,
        #companyTable td {
            padding: 0.4rem 0.65rem;
        }
    }

    @media (max-width: 768px) {
        #companyTable {
            font-size: 0.75rem;
        }

        #companyTable th,
        #companyTable td {
            padding: 0.375rem 0.5rem;
        }

        #companyDetailModal>div {
            width: 95%;
        }
    }
</style>