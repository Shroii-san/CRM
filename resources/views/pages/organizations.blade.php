@extends('layouts.main')
@section('title', 'Organization')

@section('content')
    <div class="container-expanded mx-auto px-6 lg:px-8 py-8 pt-[60px] mt-0 fade-in">
        <!-- KPI Section -->
        <x-company.attribut.kpi :totalCompanies="$totalOrganizations" :jenisCompanies="$totalIndustry"
            :tierCompanies="$tierOrganization" :activeCompanies="$activeOrganization" />

        <!-- Company Card dengan Everything Inside -->
        <div x-data="organizationTable"
            style="background-color: #ffffff; border-radius: 0.5rem; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb; overflow: hidden; margin-top: 0.5rem;">

            <!-- Card Header dengan Title dan Action Button -->
            <div style="padding: 0.5rem 1.5rem; border-bottom: 1px solid #e5e7eb;">
                <div
                    style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin: 0;">Organization
                            Management
                        </h3>
                        <p style="font-size: 0.875rem; color: #6b7280; margin: 0.25rem 0 0 0;">Kelola data organisasi dan
                            informasinya</p>
                    </div>

                    @if(auth()->user()->canAccess($currentMenuId, 'create'))
                        <button onclick="openAddCompanyModal()"
                            style="display: flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1rem; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; border: none; border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; cursor: pointer; box-shadow: 0 2px 4px rgba(99, 102, 241, 0.2); transition: all 0.2s;">
                            <i class="fas fa-plus"></i>
                            <span>Tambah Perusahaan</span>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Search and Filter Section -->
            <div style="padding: 0.5rem 1.5rem; background-color: #f9fafb; border-bottom: 1px solid #e5e7eb; fade-in">
                <div style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: center;">
                    <div class="flex flex-col md:flex-row md:items-center gap-3">
                        <div class="relative flex-1 md:flex-initial md:w-80">
                            <input type="text" id="searchInput" data-search placeholder="{{ $placeholder }}"
                                @input="onSearchInput($event)" :value="search"
                                class="w-full pl-10 pr-4 py-2 text-sm bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all" />
                            <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-sm"></i>
                        </div>
                    </div>
                </div>

                <!-- Table Section - NO PADDING! -->
                <x-company.table.table :companies="$companies" :currentMenuId="$currentMenuId" />

                <!-- Dynamic AJAX Pagination Container -->
                <div data-pagination
                    style="padding: 0.75rem 1.5rem; border-top: 1px solid #e5e7eb; background-color: #f9fafb;">
                    <div x-show="meta.total > 0" class="flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-600">
                        <div>
                            Menampilkan <span class="font-medium" x-text="meta.from"></span> sampai <span class="font-medium" x-text="meta.to"></span> dari <span class="font-medium" x-text="meta.total"></span> data
                        </div>
                        <div class="flex items-center gap-2">
                            <button @click="setPage(page - 1)" :disabled="page <= 1" class="px-3 py-1 border rounded text-xs font-medium disabled:opacity-50 hover:bg-gray-100">
                                Prev
                            </button>
                            <span class="text-xs">
                                Halaman <span x-text="meta.current_page"></span> dari <span x-text="meta.last_page"></span>
                            </span>
                            <button @click="setPage(page + 1)" :disabled="page >= meta.last_page" class="px-3 py-1 border rounded text-xs font-medium disabled:opacity-50 hover:bg-gray-100">
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modals -->
        <x-company.action.action :industries="$industries" :provinces="$provinces" />
        <x-company.action.edit :industries="$industries" :provinces="$provinces" />

        @push('scripts')
            <script>
                window._orgPermissions = {
                    edit: {{ auth()->user()->canAccess($currentMenuId, 'update') ? 'true' : 'false' }},
                    delete: {{ auth()->user()->canAccess($currentMenuId, 'delete') ? 'true' : 'false' }}
                };
            </script>
            @vite(['resources/js/pages/organizations.js'])
            <script src="{{ asset('js/address-cascade.js') }}"></script>
            <script src="{{ asset('js/company-modal.js') }}"></script>
        @endpush

        <style>
            /* Hover effects for buttons */
            button:hover,
            a[href]:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            }

            /* Focus styles for inputs */
            #searchInput:focus,
            #filterType:focus,
            #filterTier:focus,
            #filterStatus:focus {
                outline: none;
                border-color: #3b82f6;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            }

            /* Responsive adjustments */
            @media (max-width: 768px) {

                button span,
                a span {
                    display: none;
                }
            }
        </style>

        <script>
            function deleteCompany(companyId, deleteRoute, csrfToken) {
                console.log('🗑️ Delete company:', { companyId, deleteRoute, csrfToken });

                deleteRecord(companyId, deleteRoute, csrfToken, (data) => {
                    console.log('✅ Delete success:', data);
                    if (window.companyTableHandler) {
                        console.log('🔄 Refreshing table...');
                        window.companyTableHandler.refresh();
                    } else {
                        location.reload();
                    }
                });
            }
        </script>
@endsection