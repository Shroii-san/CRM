@extends('layout.main')
@section('title','Company')

@section('content')
<div class="container-expanded mx-auto px-6 lg:px-8 py-8 pt-[60px] mt-0 fade-in">
    <!-- KPI Section -->
    <x-company.attribut.kpi
        :totalCompanies="$totalCompanies"
        :jenisCompanies="$jenisCompanies"
        :tierCompanies="$tierCompanies"
        :activeCompanies="$activeCompanies"
    />
    
    <!-- Company Card dengan Everything Inside -->
    <div style="background-color: #ffffff; border-radius: 0.5rem; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb; overflow: hidden; margin-top: 0.5rem;">
        
        <!-- Card Header dengan Title dan Action Button -->
        <div style="padding: 0.5rem 1.5rem; border-bottom: 1px solid #e5e7eb;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin: 0;">Company Management</h3>
                    <p style="font-size: 0.875rem; color: #6b7280; margin: 0.25rem 0 0 0;">Kelola data perusahaan dan informasinya</p>
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
                <x-globals.filtersearch
                    tableId="companyTable"
                    :columns="[
                        'number',
                        'company_name', 
                        'company_type',
                        'tier',
                        'description', 
                        'status',
                        'actions'
                    ]"
                    :filters="[
                        'Type' => $types->pluck('type_name', 'company_type_id')->toArray(),
                        'Tier' => ['A', 'B', 'C', 'D'],
                        'Status' => ['Active', 'Inactive']
                    ]"
                    ajaxUrl="{{ route('company.search') }}"
                    placeholder="Cari nama perusahaan, deskripsi, atau tipe..."
                />
            </div>
        </div>
        
        <!-- Table Section - NO PADDING! -->
        <x-company.table.table :companies="$companies" :currentMenuId="$currentMenuId" />
        
        <!-- Pagination -->
        @if($companies->hasPages())
        <div style="border-top: 1px solid #e5e7eb; background-color: #f9fafb;">
            <x-globals.pagination :paginator="$companies" />
        </div>
        @endif
    </div>
</div>

<!-- Modals -->
<x-company.action.action :types="$types" :provinces="$provinces"/>
<x-company.action.edit :types="$types" :provinces="$provinces"/>

@push('scripts')
<script src="{{ asset('js/address-cascade.js') }}"></script>
<script src="{{ asset('js/company-modal.js') }}"></script>
<script src="{{ asset('js/search.js') }}"></script>
@endpush

<style>
    /* Hover effects for buttons */
    button:hover, a[href]:hover {
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
        button span, a span {
            display: none;
        }
    }
</style>

<script>
// ==================== DELETE FUNCTION ====================
function deleteCompany(companyId, deleteRoute, csrfToken) {
    console.log('🗑️ Delete company:', {companyId, deleteRoute, csrfToken});
    
    deleteRecord(companyId, deleteRoute, csrfToken, (data) => {
        console.log('✅ Delete success:', data);
        if (window.companyTableHandler) {
            console.log('🔄 Refreshing table...');
            window.companyTableHandler.refresh();
        } else {
            console.warn('⚠️ companyTableHandler not found, reloading page');
            location.reload();
        }
    });
}

// ==================== TABLE HANDLER INITIALIZATION ====================
document.addEventListener('DOMContentLoaded', () => {
    console.log('📋 Company page loaded');
    
    if (typeof TableHandler === 'undefined') {
        console.error('❌ TableHandler class not found. search.js may not be loaded.');
        return;
    }

    console.log('🎯 Creating TableHandler instance...');
    
    try {
        window.companyTableHandler = new TableHandler({
            tableId: 'companyTable',
            ajaxUrl: '{{ route("company.search") }}',
            filters: ['type', 'tier', 'status'],
            columns: ['number', 'company_name', 'company_type', 'tier', 'description', 'status', 'actions']
        });
        
        console.log('✅ TableHandler initialized:', window.companyTableHandler);
    } catch (error) {
        console.error('❌ Error initializing TableHandler:', error);
    }
});
</script>
@endsection