<!-- Table -->
<div class="bg-white rounded-lg shadow-sm overflow-hidden fade-in">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-3 py-2 text-left">
                        <input type="checkbox" id="selectAll"
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-3.5 h-3.5" />
                    </th>
                    <th class="px-3 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-tight">
                        No
                    </th>
                    <th class="px-3 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-tight">
                        Tipe
                    </th>
                    <th class="px-3 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-tight">
                        Nama Client
                    </th>
                    <th class="px-3 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-tight">
                        Sumber
                    </th>
                    <th class="px-3 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-tight">
                        Status
                    </th>
                    <th class="px-3 py-2 text-right text-[10px] font-medium text-gray-500 uppercase tracking-tight">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody id="customerTableBody" class="divide-y divide-gray-200">
                <template x-if="loading">
                    <tr>
                        <td colspan="7" class="px-3 py-6 text-center text-gray-500">
                            <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                            <p class="text-xs">Memuat data client...</p>
                        </td>
                    </tr>
                </template>

                <template x-if="!loading && items.length === 0">
                    <tr>
                        <td colspan="7" class="px-3 py-6 text-center text-gray-500">
                            <i class="fas fa-building text-3xl mb-2"></i>
                            <p class="text-xs">Belum ada data client</p>
                        </td>
                    </tr>
                </template>

                <template x-if="!loading && items.length > 0">
                    <template x-for="(item, index) in items" :key="item.id || index">
                        <tr class="border-b border-gray-200 transition-colors duration-150 hover:bg-gray-50">
                            <td class="px-3 py-2 text-[0.8125rem] text-gray-900 whitespace-nowrap">
                                <span class="font-medium" x-text="((page - 1) * perPage) + index + 1"></span>
                            </td>
                            <td class="px-3 py-2 text-[0.8125rem] font-medium text-gray-900"
                                x-text="item.client_type || '-'">
                            </td>
                            <td class="px-3 py-2 text-[0.8125rem] text-gray-900"
                                x-text="item.organization?.name || item.person?.name || '-'">
                            </td>
                            <td class="px-3 py-2 text-[0.8125rem] text-gray-900" x-text="item.source?.name || '-'"></td>
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
                                @if(auth()->user()->canAccess($currentMenuId, 'update'))
                                    <button
                                        :onclick="'openEditCompanyModal(' + JSON.stringify(item).replace(/&quot;/g, '&quot;') + ')'"
                                        class="text-blue-600 mr-2 text-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                @endif
                                @if(auth()->user()->canAccess($currentMenuId, 'delete'))
                                    <button
                                        @click="deleteCompanyItem(item.id, '/organization/' + item.id, '{{ csrf_token() }}')"
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

<style>
    .fade-in {
        animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }
</style>