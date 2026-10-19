<div class="fade-in m-0 p-0">
    <!-- Table ONLY -->
    <div class="overflow-x-auto m-0 p-0">
        <table id="hrdCandidateTable" class="w-full m-0 border-collapse">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-3 py-2 text-left text-[0.7rem] font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                        No
                    </th>
                    <th @click="onSort('full_name')" class="px-4 py-3 cursor-pointer select-none hover:bg-gray-100">
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-2 text-left text-[0.7rem] font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Nama
                            </span>
                            <span class="inline-flex">
                                <svg x-show="sortBy === 'full_name' && sortDir === 'asc'" class="w-4 h-4 text-blue-600"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                </svg>
                                <svg x-show="sortBy === 'full_name' && sortDir === 'desc'" class="w-4 h-4 text-blue-600"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                                <svg x-show="sortBy !== 'full_name'" class="w-4 h-4 text-gray-400 opacity-40" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                </svg>
                            </span>
                        </div>
                    </th>
                    <th @click="onSort('nik')" class="px-4 py-3 cursor-pointer select-none hover:bg-gray-100">
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-2 text-left text-[0.7rem] font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                NIK
                            </span>
                            <span class="inline-flex">
                                <svg x-show="sortBy === 'nik' && sortDir === 'asc'" class="w-4 h-4 text-blue-600"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                </svg>
                                <svg x-show="sortBy === 'nik' && sortDir === 'desc'" class="w-4 h-4 text-blue-600"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                                <svg x-show="sortBy !== 'nik'" class="w-4 h-4 text-gray-400 opacity-40" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                </svg>
                            </span>
                        </div>
                    </th>
                    <th @click="onSort('division')" class="px-4 py-3 cursor-pointer select-none hover:bg-gray-100">
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-2 text-left text-[0.7rem] font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Divisi
                            </span>
                            <span class="inline-flex">
                                <svg x-show="sortBy === 'division' && sortDir === 'asc'" class="w-4 h-4 text-blue-600"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                </svg>
                                <svg x-show="sortBy === 'division' && sortDir === 'desc'" class="w-4 h-4 text-blue-600"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                                <svg x-show="sortBy !== 'division'" class="w-4 h-4 text-gray-400 opacity-40" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                </svg>
                            </span>
                        </div>
                    </th>
                    <th class="px-3 py-2 text-left text-[0.7rem] font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                        Foto
                    </th>
                    <th class="px-3 py-2 text-left text-[0.7rem] font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                        KTP
                    </th>
                    <th class="px-3 py-2 text-left text-[0.7rem] font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                        CV
                    </th>
                    <th @click="onSort('status')" class="px-4 py-3 cursor-pointer select-none hover:bg-gray-100">
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-2 text-left text-[0.7rem] font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Status
                            </span>
                            <span class="inline-flex">
                                <svg x-show="sortBy === 'status' && sortDir === 'asc'" class="w-4 h-4 text-blue-600"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                </svg>
                                <svg x-show="sortBy === 'status' && sortDir === 'desc'" class="w-4 h-4 text-blue-600"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                                <svg x-show="sortBy !== 'status'" class="w-4 h-4 text-gray-400 opacity-40" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                </svg>
                            </span>
                        </div>
                    </th>
                    <th class="px-3 py-2 text-left text-[0.7rem] font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                        Keluarga
                    </th>
                    <th class="px-3 py-2 text-right text-[0.7rem] font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody id="hrdTableBody" class="divide-y divide-gray-200">
                <template x-if="loading">
                    <tr>
                        <td colspan="10" class="px-3 py-6 text-center text-gray-500">
                            <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                            <p class="text-xs">Memuat data calon karyawan...</p>
                        </td>
                    </tr>
                </template>

                <template x-if="!loading && items.length === 0">
                    <tr>
                        <td colspan="10" class="px-3 py-6 text-center text-gray-500">
                            <i class="fas fa-user-tie text-3xl mb-2"></i>
                            <p class="text-xs">Belum ada data calon karyawan</p>
                        </td>
                    </tr>
                </template>

                <template x-if="!loading && items.length > 0">
                    <template x-for="(item, index) in items" :key="item.id || index">
                        <tr class="border-b border-gray-200 transition-colors duration-150 hover:bg-gray-50">
                            <td class="px-3 py-2 text-[0.8125rem] text-gray-900 whitespace-nowrap">
                                <span class="font-medium" x-text="((page - 1) * perPage) + index + 1"></span>
                            </td>
                            <td class="px-3 py-2 text-[0.8125rem] font-medium text-gray-900 whitespace-nowrap"
                                x-text="item.full_name || '-'"></td>
                            <td class="px-3 py-2 text-[0.8125rem] text-gray-900 whitespace-nowrap"
                                x-text="item.nik || '-'"></td>
                            <td class="px-3 py-2 text-[0.8125rem] text-gray-900 whitespace-nowrap"
                                x-text="item.division || '-'"></td>

                            <td class="px-3 py-2 whitespace-nowrap">
                                <template x-if="item.foto">
                                    <button type="button" @click="previewPhoto(item.foto, item.full_name)"
                                        class="block rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer"
                                        title="Klik untuk melihat foto">
                                        <img :src="item.foto" class="h-10 w-10 rounded-lg border border-gray-200 object-cover shadow-sm hover:opacity-80 transition-opacity" alt="Foto">
                                    </button>
                                </template>
                                <template x-if="!item.foto">
                                    <span class="text-xs text-gray-400">-</span>
                                </template>
                            </td>

                            <td class="px-3 py-2 whitespace-nowrap">
                                <template x-if="item.scan_ktp">
                                    <a :href="item.scan_ktp" target="_blank"
                                        class="inline-flex items-center gap-1 rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 hover:bg-blue-100 transition-colors">
                                        <i class="fas fa-id-card"></i>
                                        <span>Lihat</span>
                                    </a>
                                </template>
                                <template x-if="!item.scan_ktp">
                                    <span class="text-xs text-gray-400">-</span>
                                </template>
                            </td>

                            <td class="px-3 py-2 whitespace-nowrap">
                                <template x-if="item.cv">
                                    <a :href="item.cv" target="_blank"
                                        class="inline-flex items-center gap-1 rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 hover:bg-indigo-100 transition-colors">
                                        <i class="fas fa-file-pdf"></i>
                                        <span>Lihat</span>
                                    </a>
                                </template>
                                <template x-if="!item.cv">
                                    <span class="text-xs text-gray-400">-</span>
                                </template>
                            </td>

                            <td class="px-3 py-2 whitespace-nowrap">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[0.6875rem] font-medium"
                                    :class="{
                                        'bg-yellow-100 text-yellow-800': Number(item.status) === 1,
                                        'bg-green-100 text-green-800': Number(item.status) === 2,
                                        'bg-red-100 text-red-800': Number(item.status) === 3,
                                        'bg-gray-100 text-gray-700': ![1, 2, 3].includes(Number(item.status))
                                    }"
                                    x-text="item.status_label || (Number(item.status) === 1 ? 'Pending' : (Number(item.status) === 2 ? 'Accepted' : (Number(item.status) === 3 ? 'Rejected' : 'Unknown')))">
                                </span>
                            </td>

                            <td class="px-3 py-2 whitespace-nowrap">
                                <template x-if="item.employee">
                                    <button type="button" @click="handleOpenKeluarga(item)"
                                        class="inline-flex items-center gap-1 rounded-lg bg-emerald-100 px-3 py-1.5 text-[0.6875rem] font-medium text-emerald-700 hover:bg-emerald-200"
                                        :title="'Kelola keluarga ' + (item.full_name || '')">
                                        <i class="fas fa-users"></i>
                                        <span>Kelola (<span x-text="item.employee?.families?.length ?? item.employee?.families_count ?? 0"></span>)</span>
                                    </button>
                                </template>
                                <template x-if="!item.employee">
                                    <span class="text-xs text-gray-400">Belum jadi karyawan</span>
                                </template>
                            </td>

                            <td class="px-3 py-2 text-right whitespace-nowrap">
                                <template x-if="Number(item.status) === 2">
                                    <span>
                                        <template x-if="item.employee">
                                            <span class="mr-2 text-xs text-green-600" title="Sudah jadi karyawan">
                                                <i class="fas fa-check-circle"></i>
                                            </span>
                                        </template>
                                        <template x-if="!item.employee">
                                            <button type="button" @click="handleOpenPromote(item)"
                                                class="mr-2 text-green-600 hover:text-green-800" title="Jadikan karyawan">
                                                <i class="fas fa-user-check"></i>
                                            </button>
                                        </template>
                                    </span>
                                </template>

                                <button type="button" @click="handleOpenEdit(item)"
                                    class="mr-2 text-blue-600 hover:text-blue-800 text-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <button type="button" @click="handleDelete(item.id)"
                                    class="text-red-600 hover:text-red-800 text-sm" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                </template>
            </tbody>
        </table>
    </div>
</div>