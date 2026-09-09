@extends('layout.main')

@section('content')
<div class="container-fluid mt-12" style="padding: 1.5rem;">
    
    <!-- Main Card -->
    <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb; overflow: hidden;">
        
        <!-- Header (Padding Diperkecil!) -->
        <div style="padding: 0.5rem 1.5rem; border-bottom: 1px solid #e5e7eb;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin: 0;">PIC Management</h3>
                    <p style="font-size: 0.875rem; color: #6b7280; margin: 0.25rem 0 0 0;">Kelola data Person In Charge dan informasinya</p>
                </div>
                @if(auth()->user()->canAccess($currentMenuId ?? 10, 'create'))
                <button onclick="openPICModal()" 
                    style="display: flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1rem; background-color: #6366f1; color: white; border: none; border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; cursor: pointer; transition: all 0.2s; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);"
                    onmouseover="this.style.backgroundColor='#4f46e5'; this.style.transform='scale(1.02)'"
                    onmouseout="this.style.backgroundColor='#6366f1'; this.style.transform='scale(1)'">
                    <i class="fas fa-plus"></i>
                    <span>Tambah PIC</span>
                </button>
                @endif
            </div>
        </div>

        <!-- Filters -->
        <div style="padding: 0.5rem 1.5rem; background-color: #f9fafb; border-bottom: 1px solid #e5e7eb;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; align-items: center;">
                <div>
                    <input type="text" id="searchInput" 
                        style="width: 100%; padding: 0.5rem 0.5rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem;"
                        placeholder="Cari nama, email, telepon, jabatan...">
                </div>
                <div>
                    <select id="companyFilter" 
                        style="width: 100%; padding: 0.5rem 0.5rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem;">
                        <option value="">Semua Perusahaan</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->company_id }}">{{ $company->company_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Table Component -->
        <x-PICharge.table.table :pics="$pics" :companies="$companies" :currentMenuId="$currentMenuId ?? 10" />

        <!-- Pagination -->
        <x-globals.pagination />

    </div>

    <!-- Action Modals -->
    <x-PICharge.action.action :companies="$companies" :currentMenuId="$currentMenuId ?? 10" />
    <x-PICharge.action.edit :companies="$companies" />
</div>

<!-- Toast Notification Container (Fixed Position) -->
<div id="toastContainer" style="position: fixed; top: 80px; right: 20px; z-index: 9999; max-width: 400px;"></div>

<style>
/* Responsive Grid */
@media (max-width: 1024px) {
    div[style*="grid-template-columns: 1fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
}

@media (max-width: 640px) {
    div[style*="grid-template-columns: 1fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
}

/* Toast Notification Styles */
.toast-notification {
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    padding: 1rem 1.25rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    border-left: 4px solid;
    animation: slideInRight 0.3s ease-out;
    min-width: 300px;
}

.toast-notification.success {
    border-left-color: #10b981;
}

.toast-notification.error {
    border-left-color: #ef4444;
}

.toast-icon {
    flex-shrink: 0;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.toast-icon.success {
    color: #10b981;
}

.toast-icon.error {
    color: #ef4444;
}

.toast-content {
    flex: 1;
}

.toast-title {
    font-weight: 600;
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
    color: #111827;
}

.toast-message {
    font-size: 0.875rem;
    color: #6b7280;
    margin: 0;
}

.toast-message ul {
    margin: 0.5rem 0 0 0;
    padding-left: 1.25rem;
}

.toast-message li {
    margin: 0.25rem 0;
}

.toast-close {
    background: none;
    border: none;
    color: #9ca3af;
    cursor: pointer;
    padding: 0;
    font-size: 1.25rem;
    line-height: 1;
    transition: color 0.2s;
    flex-shrink: 0;
}

.toast-close:hover {
    color: #4b5563;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOutRight {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

.toast-notification.hiding {
    animation: slideOutRight 0.3s ease-out forwards;
}
</style>
@endsection

@push('scripts')
<script>
    function deletePIC(picId, deleteUrl, csrfToken) {
        if (confirm('Apakah Anda yakin ingin menghapus PIC ini?')) {
            // Simple form submission approach
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = deleteUrl;
            
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
            
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endpush