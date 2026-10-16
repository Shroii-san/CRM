@extends('layouts.main')
@section('title', 'Organization')

@section('content')
    <div class="container-expanded mx-auto px-6 lg:px-8 py-8 pt-[60px] mt-0 fade-in">
        <!-- KPI Section -->
        <x-organization.attribut.kpi :totalCompanies="$totalOrganizations" :jenisCompanies="$totalIndustry"
            :tierCompanies="$tierOrganization" :activeCompanies="$activeOrganization" />

        <!-- Organization Card dengan Everything Inside -->
        <div x-data="organizationTable"
            style="background-color: #ffffff; border-radius: 0.5rem; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb; overflow: hidden; margin-top: 0.5rem;">

            <!-- Card Header dengan Title dan Action Button -->
            <div style="padding: 0.5rem 1.5rem; border-bottom: 1px solid #e5e7eb;">
                <div
                    style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin: 0;">Organization
                            Management</h3>
                        <p style="font-size: 0.875rem; color: #6b7280; margin: 0.25rem 0 0 0;">Kelola data organisasi dan
                            informasinya</p>
                    </div>

                    <div style="display: flex; gap: 0.75rem; align-items: center;">
                        <button onclick="openExportPdfModal()"
                            style="display: flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1rem; background: #ffffff; color: #4f46e5; border: 1px solid #4f46e5; border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; cursor: pointer; transition: all 0.2s;">
                            <i class="fas fa-file-pdf"></i>
                            <span>Export PDF</span>
                        </button>

                        <button onclick="openExportExcelModal()"
                            style="display: flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1rem; background: #ffffff; color: #15803d; border: 1px solid #15803d; border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; cursor: pointer; transition: all 0.2s;">
                            <i class="fas fa-file-excel"></i>
                            <span>Export Excel</span>
                        </button>

                        <button onclick="openAddCompanyModal()"
                            style="display: flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1rem; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; border: none; border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; cursor: pointer; box-shadow: 0 2px 4px rgba(99, 102, 241, 0.2); transition: all 0.2s;">
                            <i class="fas fa-plus"></i>
                            <span>Tambah Perusahaan</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Search and Filter Section -->
            <x-organization.attribut.filtersearch :industries="$industries" />

            <!-- Table Section - NO PADDING! -->
            <x-organization.table.table />

            <!-- Dynamic AJAX Pagination Container -->
            <div data-pagination style="padding: 0.75rem 1.5rem; border-top: 1px solid #e5e7eb; background-color: #f9fafb;">
                <div x-show="meta.total > 0"
                    class="flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-600">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span>Tampilkan</span>
                        <select x-model.number="perPage" @change="onPerPageChange($event)"
                            class="px-2 py-1 bg-white border border-gray-300 rounded text-xs font-medium text-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all cursor-pointer">
                            <template x-for="option in perPageOptions" :key="option">
                                <option :value="option" x-text="option" :selected="option === perPage"></option>
                            </template>
                        </select>
                        <span>data per halaman dari <span class="font-medium" x-text="meta.total"></span> data</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click="setPage(page - 1)" :disabled="page <= 1"
                            class="px-3 py-1 border rounded text-xs font-medium disabled:opacity-50 hover:bg-gray-100">
                            Prev
                        </button>
                        <span class="text-xs">
                            Halaman <span x-text="meta.current_page"></span> dari <span x-text="meta.last_page"></span>
                        </span>
                        <button @click="setPage(page + 1)" :disabled="page >= meta.last_page"
                            class="px-3 py-1 border rounded text-xs font-medium disabled:opacity-50 hover:bg-gray-100">
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <x-organization.action.action :industries="$industries" :provinces="$provinces" />
    <x-organization.action.edit :industries="$industries" :provinces="$provinces" />

    <!-- ==================== EXPORT PDF MODAL ==================== -->
    <div id="exportPdfModal"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div
            style="background: #fff; border-radius: 0.5rem; width: 100%; max-width: 480px; padding: 1.5rem; box-shadow: 0 10px 25px rgba(0,0,0,0.2); max-height: 85vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="margin: 0; font-size: 1.125rem; font-weight: 600; color: #111827;">Export PDF Organisasi</h3>
                <button onclick="closeExportPdfModal()"
                    style="background: none; border: none; cursor: pointer; font-size: 1.25rem; color: #6b7280;">&times;</button>
            </div>

            <!-- Tab Switcher -->
            <div style="display: flex; gap: 0.5rem; margin-bottom: 1.25rem; border-bottom: 1px solid #e5e7eb;">
                <button type="button" onclick="switchExportTab('detail')" id="tabBtnDetail"
                    style="padding: 0.5rem 0.75rem; background: none; border: none; border-bottom: 2px solid #4f46e5; color: #4f46e5; font-weight: 600; font-size: 0.8rem; cursor: pointer;">
                    1 Organisasi (Detail)
                </button>
                <button type="button" onclick="switchExportTab('filter')" id="tabBtnFilter"
                    style="padding: 0.5rem 0.75rem; background: none; border: none; border-bottom: 2px solid transparent; color: #6b7280; font-weight: 600; font-size: 0.8rem; cursor: pointer;">
                    Filter Industri/Tier
                </button>
                <button type="button" onclick="switchExportTab('multi')" id="tabBtnMulti"
                    style="padding: 0.5rem 0.75rem; background: none; border: none; border-bottom: 2px solid transparent; color: #6b7280; font-weight: 600; font-size: 0.8rem; cursor: pointer;">
                    Pilih Beberapa
                </button>
            </div>

            <!-- Tab 1: Detail 1 Organisasi -->
            <div id="tabContentDetail">
                <p style="font-size: 0.875rem; color: #6b7280; margin-bottom: 1rem;">Pilih satu organisasi untuk melihat
                    profil lengkapnya dalam PDF.</p>
                <select id="exportPdfOrgSelect"
                    style="width: 100%; padding: 0.625rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; margin-bottom: 1.25rem;">
                    <option value="">-- Pilih Organisasi --</option>
                    @if(isset($allOrganizations))
                        @foreach($allOrganizations as $org)
                            <option value="{{ $org->id }}">{{ $org->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <!-- Tab 2: Filter Industri / Tier -->
            <div id="tabContentFilter" style="display: none;">
                <p style="font-size: 0.875rem; color: #6b7280; margin-bottom: 1rem;">Pilih industri dan tier untuk
                    mengekspor daftar organisasi. Biarkan kosong untuk menyertakan semua.</p>

                <label style="display: block; font-size: 0.8rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">Industri</label>
                <select id="exportPdfIndustrySelect"
                    style="width: 100%; padding: 0.625rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; margin-bottom: 1rem;">
                    <option value="">Semua Industri</option>
                    @foreach($industries as $industry)
                        <option value="{{ $industry->id }}">{{ $industry->name }}</option>
                    @endforeach
                </select>

                <label style="display: block; font-size: 0.8rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">Tier</label>
                <select id="exportPdfTierSelect"
                    style="width: 100%; padding: 0.625rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; margin-bottom: 1.25rem;">
                    <option value="">Semua Tier</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>
            </div>

            <!-- Tab 3: Pilih Beberapa Organisasi -->
            <div id="tabContentMulti" style="display: none;">
                <p style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem;">Centang satu atau lebih
                    organisasi yang ingin dimasukkan ke dalam daftar PDF.</p>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; padding: 0 0.125rem;">
                    <label style="font-size: 0.8rem; color: #4b5563; cursor: pointer; display: flex; align-items: center; gap: 0.375rem; user-select: none;">
                        <input type="checkbox" id="selectAllExportPdf" onchange="toggleSelectAllExportPdf(this)">
                        <span>Pilih Semua</span>
                    </label>
                    <input type="text" id="searchExportPdfMulti" placeholder="Cari organisasi..." oninput="filterExportPdfMulti(this.value)"
                        style="padding: 0.25rem 0.5rem; font-size: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; width: 150px; outline: none;">
                </div>

                <div id="exportPdfMultiList"
                    style="max-height: 220px; overflow-y: auto; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem; margin-bottom: 1.25rem;">
                    @if(isset($allOrganizations) && $allOrganizations->isNotEmpty())
                        @foreach($allOrganizations as $org)
                            <label class="export-multi-item" data-name="{{ strtolower($org->name) }}"
                                style="display: flex; align-items: center; gap: 0.5rem; padding: 0.375rem 0.25rem; font-size: 0.85rem; color: #374151; cursor: pointer;">
                                <input type="checkbox" class="export-pdf-checkbox" value="{{ $org->id }}">
                                <span>{{ $org->name }}</span>
                            </label>
                        @endforeach
                    @else
                        <p style="font-size: 0.8rem; color: #9ca3af; margin: 0.5rem;">Memuat data...</p>
                    @endif
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button onclick="closeExportPdfModal()"
                    style="padding: 0.625rem 1rem; background: #f3f4f6; color: #374151; border: none; border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; cursor: pointer;">
                    Batal
                </button>
                <button onclick="submitExportPdf()"
                    style="padding: 0.625rem 1rem; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; border: none; border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; cursor: pointer;">
                    Export
                </button>
            </div>
        </div>
    </div>

    <!-- ==================== EXPORT EXCEL MODAL ==================== -->
    <div id="exportExcelModal"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div
            style="background: #fff; border-radius: 0.5rem; width: 100%; max-width: 420px; padding: 1.5rem; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="margin: 0; font-size: 1.125rem; font-weight: 600; color: #111827;">Export Pivot Excel</h3>
                <button onclick="closeExportExcelModal()"
                    style="background: none; border: none; cursor: pointer; font-size: 1.25rem; color: #6b7280;">&times;</button>
            </div>

            <p style="font-size: 0.875rem; color: #6b7280; margin-bottom: 1rem;">
                File Excel akan berisi tabel rekap organisasi per Tier, mencakup jumlah organisasi, status aktif/nonaktif,
                dan total PIC.
            </p>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button onclick="closeExportExcelModal()"
                    style="padding: 0.625rem 1rem; background: #f3f4f6; color: #374151; border: none; border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; cursor: pointer;">
                    Batal
                </button>
                <button onclick="submitExportExcel()"
                    style="padding: 0.625rem 1rem; background: linear-gradient(135deg, #15803d 0%, #22c55e 100%); color: white; border: none; border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; cursor: pointer;">
                    Export
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        @vite(['resources/js/pages/organizations.js'])
        <script src="{{ asset('js/address-cascade.js') }}"></script>
        <script src="{{ asset('js/company-modal.js') }}"></script>
    @endpush

    <style>
        button:hover,
        a[href]:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        #searchInput:focus,
        #filterType:focus,
        #filterTier:focus,
        #filterStatus:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        @media (max-width: 768px) {
            button span,
            a span {
                display: none;
            }
        }
    </style>

    <script>
        let currentExportTab = 'detail';

        // ==================== MODAL PDF ====================
        function openExportPdfModal() {
            document.getElementById('exportPdfModal').style.display = 'flex';
            switchExportTab('detail');
            loadOrgOptionsForExport();
        }

        function closeExportPdfModal() {
            document.getElementById('exportPdfModal').style.display = 'none';
        }

        function switchExportTab(tab) {
            currentExportTab = tab;

            document.getElementById('tabContentDetail').style.display = tab === 'detail' ? 'block' : 'none';
            document.getElementById('tabContentFilter').style.display = tab === 'filter' ? 'block' : 'none';
            document.getElementById('tabContentMulti').style.display  = tab === 'multi'  ? 'block' : 'none';

            ['Detail', 'Filter', 'Multi'].forEach(key => {
                const btn = document.getElementById('tabBtn' + key);
                const isActive = key.toLowerCase() === tab;
                btn.style.borderBottomColor = isActive ? '#4f46e5' : 'transparent';
                btn.style.color             = isActive ? '#4f46e5' : '#6b7280';
            });
        }

        /**
         * Memuat daftar organisasi dari endpoint web JSON jika opsi belum ter-render.
         */
        function loadOrgOptionsForExport() {
            const select    = document.getElementById('exportPdfOrgSelect');
            const multiList = document.getElementById('exportPdfMultiList');

            // Jika opsi sudah ter-render oleh Blade, tidak perlu fetch ulang
            if (select && select.options.length > 1) {
                return;
            }

            fetch('{{ route("organization.options") }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(res => res.json())
                .then(res => {
                    const list = res.data || [];
                    if (list.length > 0) {
                        select.innerHTML = '<option value="">-- Pilih Organisasi --</option>';
                        multiList.innerHTML = '';

                        list.forEach(org => {
                            // Dropdown option untuk Tab Detail
                            const option = document.createElement('option');
                            option.value       = org.id;
                            option.textContent = org.name;
                            select.appendChild(option);

                            // Checkbox item untuk Tab Multi
                            const label = document.createElement('label');
                            label.className = 'export-multi-item';
                            label.setAttribute('data-name', (org.name || '').toLowerCase());
                            label.style.cssText = 'display: flex; align-items: center; gap: 0.5rem; padding: 0.375rem 0.25rem; font-size: 0.85rem; color: #374151; cursor: pointer;';
                            label.innerHTML = `<input type="checkbox" class="export-pdf-checkbox" value="${org.id}"> <span>${org.name}</span>`;
                            multiList.appendChild(label);
                        });
                    } else {
                        multiList.innerHTML = '<p style="font-size: 0.8rem; color: #9ca3af; margin: 0.5rem;">Tidak ada data.</p>';
                    }
                })
                .catch(err => {
                    console.error('❌ Gagal memuat daftar organisasi:', err);
                });
        }

        /**
         * Toggle checkbox pilih semua pada Tab Multi.
         */
        function toggleSelectAllExportPdf(masterCheckbox) {
            const items = document.querySelectorAll('.export-multi-item');
            items.forEach(item => {
                if (item.style.display !== 'none') {
                    const cb = item.querySelector('.export-pdf-checkbox');
                    if (cb) cb.checked = masterCheckbox.checked;
                }
            });
        }

        /**
         * Filter list organisasi di Tab Multi secara realtime.
         */
        function filterExportPdfMulti(query) {
            const q = query.toLowerCase().trim();
            const items = document.querySelectorAll('.export-multi-item');
            items.forEach(item => {
                const name = item.getAttribute('data-name') || '';
                item.style.display = name.includes(q) ? 'flex' : 'none';
            });
        }

        function submitExportPdf() {
            if (currentExportTab === 'detail') {
                const orgId = document.getElementById('exportPdfOrgSelect').value;
                if (!orgId) {
                    alert('Silakan pilih organisasi terlebih dahulu.');
                    return;
                }
                const url = '{{ route("organization.pdf", ["id" => ":id"]) }}'.replace(':id', orgId);
                window.open(url, '_blank');

            } else if (currentExportTab === 'filter') {
                const industryId = document.getElementById('exportPdfIndustrySelect').value;
                const tier       = document.getElementById('exportPdfTierSelect').value;

                let url = '{{ route("organization.pdf.list") }}?';
                const params = [];
                if (industryId) params.push('industry_id=' + encodeURIComponent(industryId));
                if (tier)       params.push('tier='        + encodeURIComponent(tier));
                url += params.join('&');

                window.open(url, '_blank');

            } else if (currentExportTab === 'multi') {
                const checked = document.querySelectorAll('.export-pdf-checkbox:checked');
                if (checked.length === 0) {
                    alert('Pilih minimal satu organisasi.');
                    return;
                }
                const ids = Array.from(checked).map(cb => cb.value).join(',');
                window.open('{{ route("organization.pdf.selected") }}?ids=' + encodeURIComponent(ids), '_blank');
            }

            closeExportPdfModal();
        }

        // ==================== MODAL EXCEL ====================
        function openExportExcelModal() {
            document.getElementById('exportExcelModal').style.display = 'flex';
        }

        function closeExportExcelModal() {
            document.getElementById('exportExcelModal').style.display = 'none';
        }

        function submitExportExcel() {
            window.location.href = '{{ route("organization.pivot.excel") }}';
            closeExportExcelModal();
        }

        // ==================== DELETE ====================
        function deleteCompany(companyId, deleteRoute, csrfToken) {
            deleteRecord(companyId, deleteRoute, csrfToken, () => {
                if (window.companyTableHandler) {
                    window.companyTableHandler.refresh();
                } else {
                    location.reload();
                }
            });
        }
    </script>
@endsection