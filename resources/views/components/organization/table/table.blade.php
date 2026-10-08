@props(['organizations', 'currentMenuId'])

<div class="fade-in m-0 p-0">
    <!-- Table ONLY -->
    <div class="overflow-x-auto m-0 p-0">
        <table id="companyTable" class="w-full m-0 border-collapse">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th
                        class="px-3 py-2 text-left text-[0.7rem] font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                        No</th>
                    <th
                        class="px-3 py-2 text-left text-[0.7rem] font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                        Nama Perusahaan</th>
                    <th
                        class="px-3 py-2 text-left text-[0.7rem] font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                        Jenis Industri</th>
                    <th
                        class="px-3 py-2 text-left text-[0.7rem] font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                        Tier</th>
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

{{-- Company Detail Modal - ROUNDED KONSISTEN --}}
<div id="companyDetailModal"
    class="hidden fixed inset-0 w-full h-full bg-black/50 z-[9999] items-center justify-center">
    <div class="bg-white rounded-3xl w-[95%] max-w-[700px] max-h-[90vh] flex flex-col shadow-xl overflow-hidden">

        {{-- Modal Header - FIXED --}}
        <div class="flex justify-between items-center p-6 bg-gradient-to-r from-indigo-600 to-purple-600 shrink-0">
            <h3 class="text-xl font-semibold text-white m-0">Detail Perusahaan</h3>
            <button onclick="closeCompanyDetailModal()"
                class="text-white bg-transparent border-0 text-2xl cursor-pointer p-0">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Modal Body - SCROLLABLE WITH BORDER RADIUS AT BOTTOM --}}
        <div class="overflow-y-auto flex-1 p-6 flex flex-col gap-6 rounded-b-3xl">

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
    function showCompanyDetail(companyId) {
        const modal = document.getElementById('companyDetailModal');
        modal.style.display = 'flex';

        document.getElementById('picsContainer').innerHTML = `
        <div style="text-align: center; padding: 1rem; color: #6b7280;">
            <i class="fas fa-spinner fa-spin"></i> Loading...
        </div>
    `;

        Promise.all([
            fetch(`/organization/${companyId}`),
            fetch(`/organization/${companyId}/pics`)
        ])
            .then(async ([companyResponse, picsResponse]) => {
                const companyData = await companyResponse.json();
                const picsData = await picsResponse.json();
                const c = companyData.company || {};
                const pics = Array.isArray(picsData.pics) ? picsData.pics : [];

                document.getElementById('detailCompanyName').textContent = c.name || '-';
                document.getElementById('detailCompanyType').textContent = c.industry?.name || '-';
                document.getElementById('detailCompanyTier').textContent = c.tier || '-';
                document.getElementById('detailCompanyDescription').textContent = c.description || '-';

                const statusBadge = document.getElementById('detailCompanyStatus');
                const statusText = c.is_active ? 'Active' : 'Inactive';
                statusBadge.textContent = statusText;
                if (c.is_active) {
                    statusBadge.style.backgroundColor = '#d1fae5';
                    statusBadge.style.color = '#065f46';
                } else {
                    statusBadge.style.backgroundColor = '#fee2e2';
                    statusBadge.style.color = '#991b1b';
                }

                document.getElementById('detailProvince').textContent = c.province?.name || '-';
                document.getElementById('detailRegency').textContent = c.regency?.name || '-';
                document.getElementById('detailDistrict').textContent = c.district?.name || '-';
                document.getElementById('detailVillage').textContent = c.village?.name || '-';
                document.getElementById('detailFullAddress').textContent = c.address || '-';
                document.getElementById('detailCompanyPhone').textContent = c.phone || '-';
                document.getElementById('detailCompanyEmail').textContent = c.email || '-';

                const websiteLink = document.getElementById('detailCompanyWebsiteLink');
                const websiteWrap = document.getElementById('detailCompanyWebsite');
                if (c.website) {
                    websiteLink.href = c.website;
                    websiteLink.textContent = c.website;
                    websiteWrap.innerHTML = '';
                } else {
                    websiteWrap.innerHTML = '<span style="color: #111827;">-</span>';
                }

                const linkedinLink = document.getElementById('detailCompanyLinkedinLink');
                const linkedinWrap = document.getElementById('detailCompanyLinkedin');
                linkedinLink.href = '#';
                linkedinLink.textContent = '-';
                linkedinWrap.innerHTML = '<span style="color: #111827;">-</span>';

                const instagramLink = document.getElementById('detailCompanyInstagramLink');
                const instagramWrap = document.getElementById('detailCompanyInstagram');
                instagramLink.href = '#';
                instagramLink.textContent = '-';
                instagramWrap.innerHTML = '<span style="color: #111827;">-</span>';

                const logoContainer = document.getElementById('detailLogoContainer');
                logoContainer.innerHTML = '<div style="width: 120px; height: 120px; background-color: #e5e7eb; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center;"><i class="fas fa-building" style="font-size: 2.5rem; color: #9ca3af;"></i></div>';

                const picsContainer = document.getElementById('picsContainer');
                const picCount = document.getElementById('picCount');

                if (pics.length > 0) {
                    picCount.textContent = String(pics.length);
                    picsContainer.innerHTML = pics.map(pic => `
                    <div style="border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 0.5rem; background-color: #f9fafb; display: flex; gap: 0.5rem; align-items: flex-start;">
                        <div style="flex-shrink: 0; width: 2rem; height: 2rem; border-radius: 0.75rem; background: linear-gradient(135deg, #4f46e5, #7c3aed); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.875rem;">
                            ${(pic.name || 'P').charAt(0).toUpperCase()}
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <p style="font-size: 0.8125rem; font-weight: 600; color: #111827; margin: 0 0 0.125rem 0;">${pic.name || '-'}</p>
                            <p style="font-size: 0.75rem; color: #6b7280; margin: 0 0 0.25rem 0;">${pic.role || '-'}</p>
                            <div style="display: flex; gap: 0.5rem; font-size: 0.7rem; flex-wrap: wrap;">
                                <a href="tel:${pic.phone || ''}" style="color: #3b82f6; text-decoration: none; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${pic.phone || '-'}</a>
                                <span style="color: #d1d5db;">•</span>
                                <a href="mailto:${pic.email || ''}" style="color: #3b82f6; text-decoration: none; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${pic.email || '-'}</a>
                            </div>
                        </div>
                    </div>
                `).join('');
                } else {
                    picCount.textContent = '0';
                    picsContainer.innerHTML = '<p style="text-align: center; color: #9ca3af; font-size: 0.875rem; margin: 0;">Belum ada PIC</p>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('picsContainer').innerHTML = '<div style="text-align: center; color: #dc2626; font-size: 0.875rem;">Gagal memuat data</div>';
            });
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