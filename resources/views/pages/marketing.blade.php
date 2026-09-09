@extends('layout.main')
@section('title','Marketing')

@section('content')
<div class="container-expanded mx-auto px-6 lg:px-8 py-8 pt-[60px] mt-4 fade-in">
    
    <!-- Sales Management Card dengan Everything Inside -->
    <div style="background-color: #ffffff; border-radius: 0.5rem; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb; overflow: hidden;">
        
        <!-- Card Header dengan Title dan Action Button -->
        <div style="padding: 0.5rem 1.5rem; border-bottom: 1px solid #e5e7eb;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin: 0;">Sales Management</h3>
                    <p style="font-size: 0.875rem; color: #6b7280; margin: 0.25rem 0 0 0;">Kelola data sales dan informasinya</p>
                </div>
                
                @if(auth()->user()->canAccess($currentMenuId, 'create'))
                <button onclick="openAddSalesModal()" 
                    style="display: flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1rem; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; border: none; border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; cursor: pointer; box-shadow: 0 2px 4px rgba(99, 102, 241, 0.2); transition: all 0.2s;">
                    <i class="fas fa-plus"></i>
                    <span>Tambah Sales</span>
                </button>
                @endif
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div style="padding: 0.5rem 1.5rem; background-color: #f9fafb; border-bottom: 1px solid #e5e7eb;">
            <div style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: center;">
                <x-globals.filtersearch
                    tableId="salesTable"
                    :columns="[
                        'number',
                        'user',
                        'phone',
                        'date_birth',
                        'alamat',
                        'status',
                        'actions'
                    ]"
                    :filters="['Status' => ['Active', 'Inactive']]"
                    ajaxUrl="{{ route('marketing.search') }}"
                    placeholder="Cari nama sales, email, atau nomor telepon..."
                />
            </div>
        </div>

        <!-- Table Section -->
        <x-marketing.table.table :salesUsers="$salesUsers" :currentMenuId="$currentMenuId" />
    </div>
</div>

<!-- Modals -->
<x-marketing.action.action :provinces="$provinces" :currentMenuId="$currentMenuId"/>
<x-marketing.action.edit :provinces="$provinces" />

@push('scripts')
<script src="{{ asset('js/address-cascade.js') }}"></script>
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
// ========== MARKETING PAGE SCRIPT - FIXED VERSION ==========

window.Laravel = {!! json_encode(['csrfToken' => csrf_token()]) !!};

// Global variable for create cascade
let createCascadeInstance = null;

// Function untuk delete sales
function deleteSales(userId, deleteRoute, csrfToken) {
    console.log('deleteSales called:', { userId, deleteRoute, csrfToken });

    deleteRecord(userId, deleteRoute, csrfToken, (data) => {
        console.log('Delete success:', data);
        if (window.salesTableHandler) {
            console.log('Refreshing table...');
            window.salesTableHandler.refresh();
        } else {
            console.warn('salesTableHandler not found, reloading page');
            location.reload();
        }
    });
}

// Initialize setelah DOM siap
document.addEventListener('DOMContentLoaded', () => {
    console.log('🚀 Marketing page loaded');
    
    // Check for TableHandler
    if (typeof TableHandler === 'undefined') {
        console.error('❌ TableHandler class not found. search.js may not be loaded.');
    } else {
        console.log('✅ TableHandler found, initializing...');
        
        try {
            window.salesTableHandler = new TableHandler({
                tableId: 'salesTable',
                ajaxUrl: '{{ route("marketing.search") }}',
                filters: ['status'],
                columns: ['number', 'user', 'phone', 'date_birth', 'alamat', 'status', 'actions']
            });
            
            console.log('✅ TableHandler initialized successfully:', window.salesTableHandler);
        } catch (error) {
            console.error('❌ Error initializing TableHandler:', error);
        }
    }

    // Check for AddressCascade
    if (typeof AddressCascade === 'undefined') {
        console.error('❌ AddressCascade class not found. address-cascade.js may not be loaded.');
    } else {
        console.log('✅ AddressCascade class found');
    }
    
    // Initialize create cascade
    initializeCreateCascade();
    
    console.log('✅ Marketing page initialization complete');
});

// Initialize create address cascade
function initializeCreateCascade() {
    console.log('🔧 Initializing CREATE address cascade...');
    
    try {
        // Destroy previous instance if exists
        if (createCascadeInstance) {
            console.log('🗑️ Destroying previous CREATE cascade instance');
            createCascadeInstance.destroy();
            createCascadeInstance = null;
        }
        
        // Create new instance
        createCascadeInstance = new AddressCascade({
            provinceId: 'create-province',
            regencyId: 'create-regency',
            districtId: 'create-district',
            villageId: 'create-village'
        });
        
        console.log('✅ CREATE AddressCascade initialized successfully');
    } catch (error) {
        console.error('❌ Error initializing CREATE AddressCascade:', error);
    }
}

// Re-initialize create cascade when modal opens
function openAddSalesModal() {
    console.log('🔓 Opening add sales modal');
    
    document.getElementById('addSalesModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Re-initialize cascade untuk memastikan event listeners fresh
    setTimeout(() => {
        initializeCreateCascade();
        
        // Focus first input
        const firstInput = document.querySelector('#addSalesModal input[name="username"]');
        if (firstInput) firstInput.focus();
    }, 100);
}

function closeAddSalesModal() {
    console.log('🔒 Closing add sales modal');
    
    document.getElementById('addSalesModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    
    // Reset form
    document.getElementById('addSalesForm').reset();
    
    // Destroy cascade instance
    if (createCascadeInstance) {
        console.log('🗑️ Destroying CREATE cascade on close');
        createCascadeInstance.destroy();
        createCascadeInstance = null;
    }
    
    // Reset dropdowns to initial state
    const regency = document.getElementById('create-regency');
    const district = document.getElementById('create-district');
    const village = document.getElementById('create-village');
    
    if (regency) {
        regency.innerHTML = '<option value="">-- Pilih Kabupaten/Kota --</option>';
        regency.disabled = true;
    }
    
    if (district) {
        district.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        district.disabled = true;
    }
    
    if (village) {
        village.innerHTML = '<option value="">-- Pilih Kelurahan/Desa --</option>';
        village.disabled = true;
    }
    
    // Reset address collapse state
    const content = document.getElementById('sales-address-content');
    const icon = document.getElementById('sales-address-toggle-icon');
    const statusText = document.getElementById('sales-address-status');
    
    if (content) content.classList.add('hidden');
    if (icon) icon.style.transform = 'rotate(0deg)';
    if (statusText) {
        statusText.textContent = 'Belum diisi';
        statusText.classList.remove('text-green-600', 'font-medium');
        statusText.classList.add('text-gray-500');
    }
    
    // Re-initialize untuk next use
    setTimeout(() => {
        initializeCreateCascade();
    }, 300);
}

console.log('✅ Marketing page script loaded');
</script>
@endsection