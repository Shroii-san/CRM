@extends('layouts.main')
@section('title', 'Organization')

@section('content')
    <div class="container-expanded mx-auto px-6 lg:px-8 py-8 pt-[60px] mt-0 fade-in">
        <!-- KPI Section -->
        <x-organization.attribut.kpi :totalCompanies="$totalOrganizations" :jenisCompanies="$totalIndustry"
            :tierCompanies="$tierOrganization" :activeCompanies="$activeOrganization" />

        <!-- Organization Card -->
        <div x-data="organizationTable" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mt-2">

            <!-- Card Header -->
            <div class="px-6 py-2 border-b border-gray-200">
                <div class="flex justify-between items-center flex-wrap gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 m-0">Organization Management</h3>
                        <p class="text-sm text-gray-500 mt-1 mb-0">Kelola data organisasi dan informasinya</p>
                    </div>

                    <div class="flex gap-2.5 items-center flex-wrap">

                        <button type="button" onclick="openImportExcelModal()"
                            class="inline-flex items-center gap-2 px-3.5 py-2 bg-white text-sky-600 border border-sky-600 hover:bg-sky-50 rounded-lg font-medium text-sm cursor-pointer transition-all hover:-translate-y-0.5 hover:shadow-sm shrink-0">
                            <i class="fas fa-file-import"></i>
                            <span>Import Excel</span>
                        </button>

                        <button type="button" onclick="openExportExcelModal()"
                            class="inline-flex items-center gap-2 px-3.5 py-2 bg-white text-green-700 border border-green-700 hover:bg-green-50 rounded-lg font-medium text-sm cursor-pointer transition-all hover:-translate-y-0.5 hover:shadow-sm shrink-0">
                            <i class="fas fa-file-excel"></i>
                            <span>Export Excel</span>
                        </button>

                        <button type="button" onclick="openExportPdfModal()"
                            class="inline-flex items-center gap-2 px-3.5 py-2 bg-white text-indigo-600 border border-indigo-600 hover:bg-indigo-50 rounded-lg font-medium text-sm cursor-pointer transition-all hover:-translate-y-0.5 hover:shadow-sm shrink-0">
                            <i class="fas fa-file-pdf"></i>
                            <span>Export PDF</span>
                        </button>

                        <button type="button" onclick="openAddCompanyModal()"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white border-0 rounded-lg font-semibold text-sm cursor-pointer shadow-md transition-all hover:-translate-y-0.5 hover:shadow-lg shrink-0">
                            <i class="fas fa-plus"></i>
                            <span>Tambah Perusahaan</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Search & Filter -->
            <x-organization.attribut.filtersearch :industries="$industries" />

            <!-- Table -->
            <x-organization.table.table />

            <!-- Pagination -->
            <div data-pagination class="px-6 py-3 border-t border-gray-200 bg-gray-50">
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
    <div id="exportPdfModal" class="hidden fixed inset-0 bg-black/50 z-[1000] items-center justify-content-center"
        style="display:none;">
        <div
            class="bg-white rounded-lg w-full max-w-[480px] p-6 shadow-[0_10px_25px_rgba(0,0,0,0.2)] max-h-[85vh] overflow-y-auto mx-auto my-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 m-0">Export PDF Organisasi</h3>
                <button type="button" onclick="closeExportPdfModal()"
                    class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors cursor-pointer text-xl leading-none">&times;</button>
            </div>

            <!-- Tab Switcher -->
            <div class="flex gap-2 mb-5 border-b border-gray-200">
                <button type="button" onclick="switchExportTab('detail')" id="tabBtnDetail"
                    class="px-3 py-2 bg-transparent border-0 border-b-2 border-indigo-600 text-indigo-600 font-semibold text-[0.8rem] cursor-pointer">
                    1 Organisasi (Detail)
                </button>
                <button type="button" onclick="switchExportTab('filter')" id="tabBtnFilter"
                    class="px-3 py-2 bg-transparent border-0 border-b-2 border-transparent text-gray-500 font-semibold text-[0.8rem] cursor-pointer">
                    Filter Industri/Tier
                </button>
                <button type="button" onclick="switchExportTab('multi')" id="tabBtnMulti"
                    class="px-3 py-2 bg-transparent border-0 border-b-2 border-transparent text-gray-500 font-semibold text-[0.8rem] cursor-pointer">
                    Pilih Beberapa
                </button>
            </div>

            <!-- Tab 1: Detail -->
            <div id="tabContentDetail">
                <p class="text-sm text-gray-500 mb-4">Pilih satu organisasi untuk melihat profil lengkapnya dalam PDF.</p>
                <select id="exportPdfOrgSelect" class="w-full px-2.5 py-2 border border-gray-300 rounded-lg text-sm mb-5">
                    <option value="">-- Pilih Organisasi --</option>
                    @if(isset($allOrganizations))
                        @foreach($allOrganizations as $org)
                            <option value="{{ $org->id }}">{{ $org->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <!-- Tab 2: Filter -->
            <div id="tabContentFilter" class="hidden">
                <p class="text-sm text-gray-500 mb-4">Pilih industri dan tier untuk mengekspor daftar organisasi. Biarkan
                    kosong untuk menyertakan semua.</p>

                <label class="block text-[0.8rem] font-medium text-gray-700 mb-1.5">Industri</label>
                <select id="exportPdfIndustrySelect"
                    class="w-full px-2.5 py-2 border border-gray-300 rounded-lg text-sm mb-4">
                    <option value="">Semua Industri</option>
                    @foreach($industries as $industry)
                        <option value="{{ $industry->id }}">{{ $industry->name }}</option>
                    @endforeach
                </select>

                <label class="block text-[0.8rem] font-medium text-gray-700 mb-1.5">Tier</label>
                <select id="exportPdfTierSelect" class="w-full px-2.5 py-2 border border-gray-300 rounded-lg text-sm mb-5">
                    <option value="">Semua Tier</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>
            </div>

            <!-- Tab 3: Multi -->
            <div id="tabContentMulti" class="hidden">
                <p class="text-sm text-gray-500 mb-2">Centang satu atau lebih organisasi yang ingin dimasukkan ke dalam
                    daftar PDF.</p>

                <div class="flex justify-between items-center mb-2 px-0.5">
                    <label class="text-[0.8rem] text-gray-600 cursor-pointer flex items-center gap-1.5 select-none">
                        <input type="checkbox" id="selectAllExportPdf" onchange="toggleSelectAllExportPdf(this)">
                        <span>Pilih Semua</span>
                    </label>
                    <input type="text" id="searchExportPdfMulti" placeholder="Cari organisasi..."
                        oninput="filterExportPdfMulti(this.value)"
                        class="px-2 py-1 text-xs border border-gray-300 rounded-md w-[150px] outline-none focus:border-indigo-500">
                </div>

                <div id="exportPdfMultiList"
                    class="max-h-[220px] overflow-y-auto border border-gray-300 rounded-lg p-2 mb-5">
                    @if(isset($allOrganizations) && $allOrganizations->isNotEmpty())
                        @foreach($allOrganizations as $org)
                            <label
                                class="export-multi-item flex items-center gap-2 py-1.5 px-0.5 text-[0.85rem] text-gray-700 cursor-pointer"
                                data-name="{{ strtolower($org->name) }}">
                                <input type="checkbox" class="export-pdf-checkbox" value="{{ $org->id }}">
                                <span>{{ $org->name }}</span>
                            </label>
                        @endforeach
                    @else
                        <p class="text-[0.8rem] text-gray-400 m-2">Memuat data...</p>
                    @endif
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 mt-5">
                <button type="button" onclick="closeExportPdfModal()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-900 transition-all shadow-sm cursor-pointer">
                    Batal
                </button>
                <button type="button" onclick="submitExportPdf()"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md cursor-pointer">
                    <i class="fas fa-file-pdf"></i>
                    <span>Export PDF</span>
                </button>
            </div>
        </div>
    </div>


    <!-- ==================== EXPORT EXCEL MODAL ==================== -->
    <div id="exportExcelModal" class="fixed inset-0 bg-black/50 z-[1000] items-center justify-center" style="display:none;">
        <div class="bg-white rounded-xl w-full max-w-[520px] p-7 shadow-[0_20px_40px_rgba(0,0,0,0.15)] mx-auto my-auto">

            <!-- Header -->
            <div class="flex justify-between items-center mb-5">
                <div class="flex items-center gap-2.5">
                    <div
                        class="w-8 h-8 bg-gradient-to-br from-green-700 to-green-400 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-excel text-white text-sm"></i>
                    </div>
                    <div>
                        <h3 class="m-0 text-base font-bold text-gray-900">Export Excel</h3>
                        <p class="m-0 text-xs text-gray-500">Ekspor data organisasi ke spreadsheet</p>
                    </div>
                </div>
                <button type="button" onclick="closeExportExcelModal()"
                    class="bg-gray-100 border-0 cursor-pointer w-8 h-8 rounded-full flex items-center justify-center text-lg text-gray-500 transition-all hover:bg-gray-200">
                    &times;
                </button>
            </div>

            <!-- Tab Switcher -->
            <div class="flex bg-gray-100 rounded-lg p-1 gap-1 mb-5">
                <button type="button" onclick="switchExportExcelTab('data')" id="excelTabBtnData"
                    class="flex-1 px-3 py-2 bg-white border-0 rounded-md text-gray-700 font-semibold text-[0.8125rem] cursor-pointer shadow-sm transition-all">
                    <i class="fas fa-table mr-1.5"></i>Data Lengkap
                </button>
                <button type="button" onclick="switchExportExcelTab('pivot')" id="excelTabBtnPivot"
                    class="flex-1 px-3 py-2 bg-transparent border-0 rounded-md text-gray-500 font-medium text-[0.8125rem] cursor-pointer transition-all">
                    <i class="fas fa-chart-bar mr-1.5"></i>Rekap Pivot
                </button>
            </div>

            <!-- Tab 1: Data Lengkap -->
            <div id="excelTabContentData">
                <p class="text-[0.8125rem] text-gray-500 mb-4">
                    Ekspor seluruh kolom data organisasi dengan filter opsional. Pilih format file sesuai kebutuhan.
                </p>

                <!-- Format -->
                <div class="mb-4">
                    <label class="block text-[0.8rem] font-semibold text-gray-700 mb-2">Format File</label>
                    <div class="flex gap-3">
                        <label
                            class="flex-1 flex items-center gap-2 px-3.5 py-2.5 border-2 border-green-700 rounded-lg cursor-pointer bg-green-50">
                            <input type="radio" name="excelExportFormat" value="xlsx" checked class="accent-green-700"
                                onchange="updateFormatSelection()">
                            <span class="text-sm font-semibold text-green-700">XLSX</span>
                            <span class="text-xs text-gray-500 ml-auto">Excel</span>
                        </label>
                        <label id="csvRadioLabel"
                            class="flex-1 flex items-center gap-2 px-3.5 py-2.5 border-2 border-gray-200 rounded-lg cursor-pointer bg-white">
                            <input type="radio" name="excelExportFormat" value="csv" class="accent-sky-600"
                                onchange="updateFormatSelection()">
                            <span class="text-sm font-semibold text-gray-700">CSV</span>
                            <span class="text-xs text-gray-500 ml-auto">Universal</span>
                        </label>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3.5 mb-5">
                    <p class="text-xs font-semibold text-gray-700 m-0 mb-3 uppercase tracking-wide">
                        <i class="fas fa-filter mr-1.5 text-gray-500"></i>Filter (Opsional)
                    </p>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1.5">Industri</label>
                            <select id="excelExportIndustry"
                                class="w-full px-2.5 py-2 border border-gray-300 rounded-md text-[0.8125rem] bg-white text-gray-700">
                                <option value="">Semua Industri</option>
                                @foreach($industries as $ind)
                                    <option value="{{ $ind->id }}">{{ $ind->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1.5">Tier</label>
                            <select id="excelExportTier"
                                class="w-full px-2.5 py-2 border border-gray-300 rounded-md text-[0.8125rem] bg-white text-gray-700">
                                <option value="">Semua Tier</option>
                                <option value="A">Tier A</option>
                                <option value="B">Tier B</option>
                                <option value="C">Tier C</option>
                                <option value="D">Tier D</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">Status</label>
                        <select id="excelExportStatus"
                            class="w-full px-2.5 py-2 border border-gray-300 rounded-md text-[0.8125rem] bg-white text-gray-700">
                            <option value="">Semua Status</option>
                            <option value="active">Aktif</option>
                            <option value="inactive">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 mt-5">
                    <button type="button" onclick="closeExportExcelModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-900 transition-all shadow-sm cursor-pointer">
                        Batal
                    </button>
                    <button type="button" onclick="submitExportExcelData()"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-green-700 hover:bg-green-800 rounded-lg shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md cursor-pointer">
                        <i class="fas fa-file-excel"></i>
                        <span>Download Excel</span>
                    </button>
                </div>
            </div>

            <!-- Tab 2: Rekap Pivot -->
            <div id="excelTabContentPivot" class="hidden">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-5">
                    <div class="flex gap-3 items-start">
                        <div class="w-9 h-9 bg-green-700 rounded-lg flex items-center justify-center shrink-0">
                            <i class="fas fa-chart-bar text-white text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-green-700 m-0 mb-1">Tabel Rekap per Tier (A–D)</p>
                            <p class="text-[0.8125rem] text-green-800 m-0 leading-relaxed">
                                File Excel berisi ringkasan: jumlah organisasi, jumlah aktif/nonaktif, dan total PIC —
                                dikelompokkan per Tier dan Grand Total.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 mt-5">
                    <button type="button" onclick="closeExportExcelModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-900 transition-all shadow-sm cursor-pointer">
                        Batal
                    </button>
                    <button type="button" onclick="submitExportExcel()"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-green-700 hover:bg-green-800 rounded-lg shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md cursor-pointer">
                        <i class="fas fa-chart-bar"></i>
                        <span>Download Pivot</span>
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- ==================== IMPORT EXCEL MODAL ==================== -->
    <div id="importExcelModal" class="fixed inset-0 bg-black/50 z-[1000] items-center justify-center" style="display:none;">
        <div class="bg-white rounded-xl w-full max-w-[520px] p-7 shadow-[0_20px_40px_rgba(0,0,0,0.15)] mx-auto my-auto">

            <!-- Header -->
            <div class="flex justify-between items-center mb-5">
                <div class="flex items-center gap-2.5">
                    <div
                        class="w-8 h-8 bg-gradient-to-br from-sky-600 to-sky-400 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-import text-white text-sm"></i>
                    </div>
                    <div>
                        <h3 class="m-0 text-base font-bold text-gray-900">Import Data Organisasi</h3>
                        <p class="m-0 text-xs text-gray-500">Upload file Excel/CSV untuk import massal</p>
                    </div>
                </div>
                <button type="button" onclick="closeImportExcelModal()"
                    class="bg-gray-100 border-0 cursor-pointer w-8 h-8 rounded-full flex items-center justify-center text-lg text-gray-500 hover:bg-gray-200">
                    &times;
                </button>
            </div>

            <form id="importExcelForm" onsubmit="submitImportExcel(event)">

                <!-- Drag & Drop Zone -->
                <div id="importDropZone" onclick="document.getElementById('importExcelFileInput').click()"
                    ondragover="handleImportDragOver(event)" ondragleave="handleImportDragLeave(event)"
                    ondrop="handleImportDrop(event)"
                    class="border-2 border-dashed border-slate-300 rounded-xl px-4 py-7 text-center cursor-pointer transition-all bg-slate-50 mb-4">

                    <!-- Default state -->
                    <div id="importDropZoneContent">
                        <div class="w-12 h-12 bg-sky-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-cloud-upload-alt text-[1.375rem] text-sky-600"></i>
                        </div>
                        <p class="text-sm font-semibold text-gray-700 m-0 mb-1">Klik atau seret file ke sini</p>
                        <p class="text-xs text-gray-400 m-0">Mendukung: .xlsx, .xls, .csv (maks. 5 MB)</p>
                    </div>

                    <!-- File selected state -->
                    <div id="importFileSelected" class="hidden items-center justify-center gap-3">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-file-excel text-green-700 text-lg"></i>
                        </div>
                        <div class="text-left">
                            <p id="importSelectedFileName" class="text-sm font-semibold text-green-700 m-0"></p>
                            <p id="importSelectedFileSize" class="text-xs text-gray-500 m-0"></p>
                        </div>
                        <button type="button" onclick="clearImportFile(event)"
                            class="ml-2 bg-red-100 border-0 cursor-pointer w-6 h-6 rounded-full text-sm text-red-500 flex items-center justify-center">
                            &times;
                        </button>
                    </div>

                    <input type="file" id="importExcelFileInput" accept=".xlsx,.xls,.csv" required class="hidden"
                        onchange="onImportFileSelected(this)">
                </div>

                <!-- Default Industri -->
                <div class="mb-4">
                    <label class="block text-[0.8125rem] font-semibold text-gray-700 mb-1.5">
                        Industri Default
                        <span class="font-normal text-gray-400">(dipakai jika baris tidak memiliki kolom industri)</span>
                    </label>
                    <select id="importDefaultIndustrySelect"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white text-gray-700">
                        <option value="">-- Pilih Industri Default (Opsional) --</option>
                        @foreach ($industries as $industry)
                            <option value="{{ $industry->id }}">{{ $industry->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Progress Bar -->
                <div id="importProgressContainer" class="hidden mb-4">
                    <div class="flex justify-between mb-1.5">
                        <span class="text-xs font-semibold text-gray-700">Memproses import...</span>
                        <span id="importProgressText" class="text-xs text-gray-500"></span>
                    </div>
                    <div class="w-full h-1.5 bg-gray-200 rounded-full overflow-hidden">
                        <div id="importProgressBar"
                            class="h-full bg-gradient-to-r from-sky-600 to-sky-300 rounded-full w-0 transition-[width] duration-300 ease-out animate-pulse-bar">
                        </div>
                    </div>
                </div>

                <!-- Result Panel -->
                <div id="importResultPanel" class="hidden mb-4 rounded-lg overflow-hidden"></div>

                <!-- Actions Row -->
                <div class="flex justify-between items-center pt-4 border-t border-gray-100 mt-5">
                    <a href="{{ route('organization.import.template') }}"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-colors shadow-sm">
                        <i class="fas fa-download text-xs"></i>
                        <span>Unduh Template</span>
                    </a>

                    <div class="flex items-center gap-2.5">
                        <button type="button" onclick="closeImportExcelModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-900 transition-all shadow-sm cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" id="btnSubmitImport"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-sky-600 hover:bg-sky-700 rounded-lg shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-upload" id="btnSubmitImportIcon"></i>
                            <span id="btnSubmitImportText">Proses Import</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    @push('scripts')
        @vite(['resources/js/pages/organizations.js'])
        <script src="{{ asset('js/address-cascade.js') }}"></script>
        <script src="{{ asset('js/company-modal.js') }}"></script>
    @endpush

    <style>
        /* Progress bar pulse animation (not expressible in plain Tailwind) */
        @keyframes pulse-bar {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.6;
            }
        }

        .animate-pulse-bar {
            animation: pulse-bar 1.5s ease-in-out infinite;
        }

        /* Drop zone drag-over state */
        #importDropZone.drag-over {
            border-color: #0284c7 !important;
            background: #e0f2fe !important;
        }

        /* Excel tab active / inactive (toggled via JS className) */
        .excel-tab-active {
            background: #fff !important;
            color: #374151 !important;
            font-weight: 600 !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
        }

        .excel-tab-inactive {
            background: transparent !important;
            color: #6b7280 !important;
            font-weight: 500 !important;
            box-shadow: none !important;
        }
    </style>

    <script>
        let currentExportTab = 'detail';
        let currentExcelTab = 'data';

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
            document.getElementById('tabContentMulti').style.display = tab === 'multi' ? 'block' : 'none';

            const activeClass = 'px-3 py-2 bg-transparent border-0 border-b-2 border-indigo-600 text-indigo-600 font-semibold text-[0.8rem] cursor-pointer';
            const inactiveClass = 'px-3 py-2 bg-transparent border-0 border-b-2 border-transparent text-gray-500 font-medium text-[0.8rem] hover:text-gray-700 cursor-pointer';

            ['Detail', 'Filter', 'Multi'].forEach(key => {
                const btn = document.getElementById('tabBtn' + key);
                if (btn) {
                    btn.className = (key.toLowerCase() === tab) ? activeClass : inactiveClass;
                    btn.removeAttribute('style');
                }
            });
        }

        /**
         * Memuat daftar organisasi dari endpoint web JSON jika opsi belum ter-render.
         */
        function loadOrgOptionsForExport() {
            const select = document.getElementById('exportPdfOrgSelect');
            const multiList = document.getElementById('exportPdfMultiList');

            if (select && select.options.length > 1) return;

            fetch('{{ route("organization.options") }}', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(res => res.json())
                .then(res => {
                    const list = res.data || [];
                    if (list.length > 0) {
                        select.innerHTML = '<option value="">-- Pilih Organisasi --</option>';
                        multiList.innerHTML = '';

                        list.forEach(org => {
                            const option = document.createElement('option');
                            option.value = org.id;
                            option.textContent = org.name;
                            select.appendChild(option);

                            const label = document.createElement('label');
                            label.className = 'export-multi-item flex items-center gap-2 py-1.5 px-0.5 text-[0.85rem] text-gray-700 cursor-pointer';
                            label.setAttribute('data-name', (org.name || '').toLowerCase());
                            label.innerHTML = `<input type="checkbox" class="export-pdf-checkbox" value="${org.id}"> <span>${org.name}</span>`;
                            multiList.appendChild(label);
                        });
                    } else {
                        multiList.innerHTML = '<p class="text-[0.8rem] text-gray-400 m-2">Tidak ada data.</p>';
                    }
                })
                .catch(err => console.error('❌ Gagal memuat daftar organisasi:', err));
        }

        function toggleSelectAllExportPdf(masterCheckbox) {
            document.querySelectorAll('.export-multi-item').forEach(item => {
                if (item.style.display !== 'none') {
                    const cb = item.querySelector('.export-pdf-checkbox');
                    if (cb) cb.checked = masterCheckbox.checked;
                }
            });
        }

        function filterExportPdfMulti(query) {
            const q = query.toLowerCase().trim();
            document.querySelectorAll('.export-multi-item').forEach(item => {
                const name = item.getAttribute('data-name') || '';
                item.style.display = name.includes(q) ? 'flex' : 'none';
            });
        }

        function submitExportPdf() {
            if (currentExportTab === 'detail') {
                const orgId = document.getElementById('exportPdfOrgSelect').value;
                if (!orgId) { alert('Silakan pilih organisasi terlebih dahulu.'); return; }
                window.open('{{ route("organization.pdf", ["id" => ":id"]) }}'.replace(':id', orgId), '_blank');

            } else if (currentExportTab === 'filter') {
                const industryId = document.getElementById('exportPdfIndustrySelect').value;
                const tier = document.getElementById('exportPdfTierSelect').value;
                let url = '{{ route("organization.pdf.list") }}?';
                const params = [];
                if (industryId) params.push('industry_id=' + encodeURIComponent(industryId));
                if (tier) params.push('tier=' + encodeURIComponent(tier));
                url += params.join('&');
                window.open(url, '_blank');

            } else if (currentExportTab === 'multi') {
                const checked = document.querySelectorAll('.export-pdf-checkbox:checked');
                if (checked.length === 0) { alert('Pilih minimal satu organisasi.'); return; }
                const ids = Array.from(checked).map(cb => cb.value).join(',');
                window.open('{{ route("organization.pdf.selected") }}?ids=' + encodeURIComponent(ids), '_blank');
            }

            closeExportPdfModal();
        }

        // ==================== MODAL EXCEL (Tabbed) ====================
        function openExportExcelModal() {
            document.getElementById('exportExcelModal').style.display = 'flex';
            switchExportExcelTab('data');
        }

        function closeExportExcelModal() {
            document.getElementById('exportExcelModal').style.display = 'none';
        }

        function switchExportExcelTab(tab) {
            currentExcelTab = tab;
            const isData = tab === 'data';

            document.getElementById('excelTabContentData').style.display = isData ? 'block' : 'none';
            document.getElementById('excelTabContentPivot').style.display = isData ? 'none' : 'block';

            const btnData = document.getElementById('excelTabBtnData');
            const btnPivot = document.getElementById('excelTabBtnPivot');

            const activeClass = 'flex-1 px-3 py-2 bg-white border-0 rounded-md text-gray-800 font-semibold text-[0.8125rem] cursor-pointer shadow-sm transition-all';
            const inactiveClass = 'flex-1 px-3 py-2 bg-transparent border-0 rounded-md text-gray-500 font-medium text-[0.8125rem] cursor-pointer hover:text-gray-700 transition-all';

            if (btnData) {
                btnData.className = isData ? activeClass : inactiveClass;
                btnData.removeAttribute('style');
            }
            if (btnPivot) {
                btnPivot.className = !isData ? activeClass : inactiveClass;
                btnPivot.removeAttribute('style');
            }
        }

        function updateFormatSelection() {
            const selectedFormat = document.querySelector('input[name="excelExportFormat"]:checked')?.value;
            const xlsxLabel = document.querySelector('input[name="excelExportFormat"][value="xlsx"]')?.parentElement;
            const csvLabel = document.querySelector('input[name="excelExportFormat"][value="csv"]')?.parentElement;
            if (xlsxLabel) {
                xlsxLabel.style.borderColor = selectedFormat === 'xlsx' ? '#15803d' : '#e5e7eb';
                xlsxLabel.style.background = selectedFormat === 'xlsx' ? '#f0fdf4' : '#fff';
            }
            if (csvLabel) {
                csvLabel.style.borderColor = selectedFormat === 'csv' ? '#0284c7' : '#e5e7eb';
                csvLabel.style.background = selectedFormat === 'csv' ? '#e0f2fe' : '#fff';
            }
        }

        function submitExportExcelData() {
            const format = document.querySelector('input[name="excelExportFormat"]:checked')?.value || 'xlsx';
            const industryId = document.getElementById('excelExportIndustry').value;
            const tier = document.getElementById('excelExportTier').value;
            const status = document.getElementById('excelExportStatus').value;

            const params = new URLSearchParams({ format });
            if (industryId) params.append('industry_id', industryId);
            if (tier) params.append('tier', tier);
            if (status) params.append('status', status);

            window.location.href = '{{ route("organization.export.excel") }}?' + params.toString();
            closeExportExcelModal();
        }

        function submitExportExcel() {
            window.location.href = '{{ route("organization.pivot.excel") }}';
            closeExportExcelModal();
        }

        // ==================== MODAL IMPORT EXCEL ====================
        function openImportExcelModal() {
            document.getElementById('importExcelModal').style.display = 'flex';
            resetImportModal();
        }

        function closeImportExcelModal() {
            document.getElementById('importExcelModal').style.display = 'none';
            document.getElementById('importExcelForm').reset();
            resetImportModal();
        }

        function resetImportModal() {
            document.getElementById('importDropZoneContent').style.display = 'block';
            document.getElementById('importFileSelected').style.display = 'none';
            document.getElementById('importProgressContainer').style.display = 'none';
            document.getElementById('importResultPanel').style.display = 'none';
            document.getElementById('importProgressBar').style.width = '0%';
            document.getElementById('importDropZone').style.borderColor = '';
            document.getElementById('importDropZone').style.background = '';

            const btn = document.getElementById('btnSubmitImport');
            const btnText = document.getElementById('btnSubmitImportText');
            if (btn) btn.disabled = false;
            if (btnText) btnText.innerText = 'Proses Import';
        }

        function onImportFileSelected(input) {
            if (input.files && input.files[0]) showImportFileInfo(input.files[0]);
        }

        function showImportFileInfo(file) {
            const fileNameEl = document.getElementById('importSelectedFileName');
            const fileSizeEl = document.getElementById('importSelectedFileSize');
            const sizeKB = (file.size / 1024).toFixed(1);
            const sizeMB = (file.size / 1024 / 1024).toFixed(2);
            const displaySize = file.size > 1024 * 1024 ? `${sizeMB} MB` : `${sizeKB} KB`;

            fileNameEl.textContent = file.name;
            fileSizeEl.textContent = displaySize;

            document.getElementById('importDropZoneContent').style.display = 'none';
            document.getElementById('importFileSelected').style.display = 'flex';
            document.getElementById('importDropZone').style.borderColor = '#15803d';
            document.getElementById('importDropZone').style.background = '#f0fdf4';
        }

        function clearImportFile(e) {
            e.stopPropagation();
            document.getElementById('importExcelFileInput').value = '';
            document.getElementById('importDropZoneContent').style.display = 'block';
            document.getElementById('importFileSelected').style.display = 'none';
            document.getElementById('importDropZone').style.borderColor = '';
            document.getElementById('importDropZone').style.background = '';
        }

        function handleImportDragOver(e) {
            e.preventDefault();
            document.getElementById('importDropZone').classList.add('drag-over');
        }

        function handleImportDragLeave(e) {
            document.getElementById('importDropZone').classList.remove('drag-over');
        }

        function handleImportDrop(e) {
            e.preventDefault();
            document.getElementById('importDropZone').classList.remove('drag-over');

            const file = e.dataTransfer?.files?.[0];
            if (!file) return;

            const allowed = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-excel', 'text/csv', 'application/csv'];
            const ext = file.name.split('.').pop().toLowerCase();
            if (!allowed.includes(file.type) && !['xlsx', 'xls', 'csv'].includes(ext)) {
                alert('File harus berformat .xlsx, .xls, atau .csv');
                return;
            }

            const dt = new DataTransfer();
            dt.items.add(file);
            document.getElementById('importExcelFileInput').files = dt.files;
            showImportFileInfo(file);
        }

        async function submitImportExcel(e) {
            e.preventDefault();
            const fileInput = document.getElementById('importExcelFileInput');
            if (!fileInput.files || !fileInput.files[0]) {
                alert('Silakan pilih berkas spreadsheet terlebih dahulu.');
                return;
            }

            const btn = document.getElementById('btnSubmitImport');
            const btnText = document.getElementById('btnSubmitImportText');
            const btnIcon = document.getElementById('btnSubmitImportIcon');

            btn.disabled = true;
            btnText.innerText = 'Mengimpor...';
            btnIcon.className = 'fas fa-spinner fa-spin';

            const progressContainer = document.getElementById('importProgressContainer');
            const progressBar = document.getElementById('importProgressBar');
            const progressText = document.getElementById('importProgressText');
            progressContainer.style.display = 'block';

            let progress = 0;
            const progressInterval = setInterval(() => {
                progress = Math.min(progress + Math.random() * 15, 88);
                progressBar.style.width = progress + '%';
                progressText.textContent = Math.round(progress) + '%';
            }, 400);

            const formData = new FormData();
            formData.append('file', fileInput.files[0]);
            const industryId = document.getElementById('importDefaultIndustrySelect').value;
            if (industryId) formData.append('industry_id', industryId);

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const response = await fetch('{{ route("organization.import.excel") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: formData,
                });

                clearInterval(progressInterval);
                progressBar.style.width = '100%';
                progressText.textContent = '100%';

                const data = await response.json();

                await new Promise(r => setTimeout(r, 400));
                progressContainer.style.display = 'none';
                showImportResult(data, response.ok && data.success);

                if (response.ok && data.success) {
                    const rootEl = document.querySelector('[x-data="organizationTable"]');
                    if (rootEl && window.Alpine) {
                        const tableData = window.Alpine.$data(rootEl);
                        if (tableData && typeof tableData.refresh === 'function') tableData.refresh();
                    }
                }

            } catch (err) {
                clearInterval(progressInterval);
                progressContainer.style.display = 'none';
                console.error(err);
                showImportResult({ message: 'Terjadi kesalahan jaringan atau server.' }, false);
            } finally {
                btn.disabled = false;
                btnText.innerText = 'Proses Import';
                btnIcon.className = 'fas fa-upload';
            }
        }

        function showImportResult(data, isSuccess) {
            const panel = document.getElementById('importResultPanel');
            panel.style.display = 'block';

            if (isSuccess) {
                const errorsHtml = data.errors && data.errors.length > 0
                    ? `<details class="mt-2.5">
                                   <summary class="text-xs text-amber-700 cursor-pointer font-semibold">
                                       ⚠️ ${data.errors.length} baris gagal (klik untuk lihat detail)
                                   </summary>
                                   <div class="mt-1.5 text-xs text-amber-800 max-h-[100px] overflow-y-auto leading-relaxed">
                                       ${data.errors.join('<br>')}
                                   </div>
                               </details>`
                    : '';

                panel.innerHTML = `
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <div class="flex items-center gap-2 mb-2">
                                    <i class="fas fa-check-circle text-green-700 text-base"></i>
                                    <span class="text-sm font-bold text-green-700">Import Berhasil</span>
                                </div>
                                <div class="grid grid-cols-3 gap-2 mb-2">
                                    <div class="text-center bg-green-100 rounded-md py-2 px-1">
                                        <div class="text-lg font-extrabold text-green-700">${data.created || 0}</div>
                                        <div class="text-[0.6875rem] text-green-800">Baru Dibuat</div>
                                    </div>
                                    <div class="text-center bg-blue-100 rounded-md py-2 px-1">
                                        <div class="text-lg font-extrabold text-blue-700">${data.updated || 0}</div>
                                        <div class="text-[0.6875rem] text-blue-800">Diperbarui</div>
                                    </div>
                                    <div class="text-center bg-gray-100 rounded-md py-2 px-1">
                                        <div class="text-lg font-extrabold text-gray-700">${data.skipped || 0}</div>
                                        <div class="text-[0.6875rem] text-gray-500">Tidak Berubah</div>
                                    </div>
                                </div>
                                ${errorsHtml}
                            </div>`;
            } else {
                panel.innerHTML = `
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4 flex gap-2.5 items-start">
                                <i class="fas fa-times-circle text-red-600 text-base mt-0.5"></i>
                                <div>
                                    <p class="text-sm font-bold text-red-600 m-0 mb-1">Import Gagal</p>
                                    <p class="text-[0.8125rem] text-red-700 m-0">${data.message || 'Terjadi kesalahan saat memproses file.'}</p>
                                </div>
                            </div>`;
            }
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