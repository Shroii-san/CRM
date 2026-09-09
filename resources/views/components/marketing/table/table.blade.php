@props(['salesUsers', 'provinces', 'currentMenuId'])

<div class="fade-in">

    <!-- Table -->
    <div class="overflow-x-auto">
        <table id="salesTable" class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-3 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-tight">No</th>
                    <th class="px-3 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-tight">Name</th>
                    <th class="px-3 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-tight">Phone</th>
                    <th class="px-3 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-tight">Birth Date</th>
                    <th class="px-3 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-tight">Address</th>
                    <th class="px-3 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-tight">Roles</th>
                    <th class="px-3 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-tight">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($salesUsers as $index => $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-3 py-2 text-xs text-gray-900">{{ $loop->iteration }}</td>
                    <td class="px-3 py-2">
                        <div class="flex items-center">
                            <div>
                                <div class="text-xs font-medium text-gray-900">{{ $user->username }}</div>
                                <div class="text-[10px] text-gray-500">{{ $user->email ?? 'No email' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-3 py-2 text-xs text-gray-900">{{ $user->phone ?? '-' }}</td>
                    <td class="px-3 py-2 text-xs text-gray-900">
                        {{ $user->birth_date ? date('d/m/Y', strtotime($user->birth_date)) : '-' }}
                    </td>
                    <td class="px-3 py-2 max-w-xs">
                        <div class="text-xs text-gray-900">
                            @if($user->province || $user->regency || $user->district || $user->village || $user->address)
                                @php
                                    $alamatWilayah = collect([
                                        $user->village->name ?? null,
                                        $user->district->name ?? null, 
                                        $user->regency->name ?? null,
                                        $user->province->name ?? null
                                    ])->filter()->implode(', ');
                                @endphp
                                
                                @if($alamatWilayah)
                                    <div class="font-medium truncate" title="{{ $alamatWilayah }}">{{ $alamatWilayah }}</div>
                                    @if($user->address)
                                        <div class="text-[10px] text-gray-600 truncate" title="{{ $user->address }}">{{ $user->address }}</div>
                                    @endif
                                @else
                                    {{ $user->address ?? '-' }}
                                @endif
                            @else
                                -
                            @endif
                        </div>
                    </td>
                    <td class="px-3 py-2 text-xs">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-blue-100 text-blue-800">
                            {{ $user->role->role_name ?? 'No Role' }}
                        </span>
                    </td>
                    <td class="px-3 py-2 text-xs font-medium">
                        <div class="flex items-center space-x-1">
                            {{-- Show Detail Button --}}
                            @if(auth()->user()->canAccess($currentMenuId, 'view'))
                            <button 
                                onclick="showSalesDetail('{{ $user->user_id }}')" 
                                class="text-green-600 hover:text-green-900 p-1.5 rounded-lg hover:bg-green-50 flex items-center"
                                title="Show Detail">
                                <i class="fas fa-eye text-xs"></i>
                            </button>
                            @endif

                            @if(auth()->user()->canAccess($currentMenuId, 'edit'))
                            <button onclick="openEditSalesModal('{{ $user->user_id }}', '{{ $user->username }}', '{{ $user->email }}', '{{ $user->phone }}', '{{ $user->birth_date }}', '{{ $user->address }}', '{{ $user->province_id }}', '{{ $user->regency_id }}', '{{ $user->district_id }}', '{{ $user->village_id }}')" 
                                class="text-blue-600 hover:text-blue-900 p-1.5 rounded-lg hover:bg-blue-50 flex items-center" 
                                title="Edit User">
                                <i class="fas fa-edit text-xs"></i>
                            </button>
                            @endif
                            
                            @if(auth()->user()->canAccess($currentMenuId, 'delete'))
                            <form action="{{ route('marketing.sales.destroy', $user->user_id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 p-1.5 flex items-center" title="Hapus User" onclick="return confirm('Yakin ingin menghapus user ini?')">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <x-globals.pagination :paginator="$salesUsers" />
</div>

{{-- ========== SALES USER DETAIL MODAL ========== --}}
<div id="salesDetailModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background-color: white; border-radius: 0.75rem; width: 95%; height: 90vh; max-width: 1600px; display: flex; flex-direction: column; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); overflow: hidden;">
        
        {{-- Modal Header --}}
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.25rem; background: linear-gradient(to right, #2563eb, #3b82f6); flex-shrink: 0; border-radius: 0.75rem 0.75rem 0 0;">
            <h3 style="font-size: 1.125rem; font-weight: 600; color: white; margin: 0;">
                <i class="fas fa-user-tie" style="margin-right: 0.5rem;"></i>
                Detail Sales User - <span id="detailHeaderUsername"></span>
            </h3>
            <button onclick="closeSalesDetailModal()" style="color: white; background: transparent; border: none; font-size: 1.5rem; cursor: pointer; padding: 0; line-height: 1; opacity: 0.9; transition: opacity 0.15s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.9'">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Modal Body: Split Layout --}}
        <div style="display: flex; flex: 1; overflow: hidden; gap: 0;">
            
            {{-- LEFT SIDE: 1/4 (Personal & Address Info) --}}
            <div style="width: 25%; border-right: 1px solid #e5e7eb; overflow-y: auto; padding: 1rem; background-color: #fafbfc;">
                
                {{-- Personal Info --}}
                <div style="background-color: white; padding: 0.875rem; border-radius: 0.75rem; margin-bottom: 1rem; border: 1px solid #e5e7eb; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                    <h4 style="font-size: 0.9375rem; font-weight: 600; color: #111827; margin: 0 0 0.75rem 0; display: flex; align-items: center; gap: 0.5rem; border-bottom: 2px solid #2563eb; padding-bottom: 0.5rem;">
                        <i class="fas fa-id-card" style="color: #2563eb;"></i>
                        Personal Info
                    </h4>
                    <div style="display: grid; grid-template-columns: 1fr; gap: 0.875rem;">
                        <div>
                            <label style="display: block; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem; letter-spacing: 0.5px;">Username</label>
                            <p id="detailUsername" style="font-size: 0.875rem; font-weight: 600; color: #111827; margin: 0;">-</p>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem; letter-spacing: 0.5px;">Email</label>
                            <p id="detailEmail" style="font-size: 0.8125rem; color: #2563eb; margin: 0; word-break: break-all;">-</p>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem; letter-spacing: 0.5px;">Phone</label>
                            <p id="detailPhone" style="font-size: 0.8125rem; color: #111827; margin: 0;">-</p>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem; letter-spacing: 0.5px;">Birth Date</label>
                            <p id="detailBirthDate" style="font-size: 0.8125rem; color: #111827; margin: 0;">-</p>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem; letter-spacing: 0.5px;">Role</label>
                            <span id="detailRole" style="display: inline-flex; align-items: center; padding: 0.25rem 0.625rem; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 600; background-color: #dbeafe; color: #1e40af; border: 1px solid #93c5fd;">-</span>
                        </div>
                    </div>
                </div>

                {{-- Address Info --}}
                <div style="background-color: white; padding: 0.875rem; border-radius: 0.75rem; border: 1px solid #e5e7eb; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                    <h4 style="font-size: 0.9375rem; font-weight: 600; color: #111827; margin: 0 0 0.75rem 0; display: flex; align-items: center; gap: 0.5rem; border-bottom: 2px solid #059669; padding-bottom: 0.5rem;">
                        <i class="fas fa-map-marker-alt" style="color: #059669;"></i>
                        Address Info
                    </h4>
                    <div style="display: grid; grid-template-columns: 1fr; gap: 0.875rem;">
                        <div>
                            <label style="display: block; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem; letter-spacing: 0.5px;">Province</label>
                            <p id="detailProvince" style="font-size: 0.8125rem; color: #111827; margin: 0; font-weight: 500;">-</p>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem; letter-spacing: 0.5px;">Regency</label>
                            <p id="detailRegency" style="font-size: 0.8125rem; color: #111827; margin: 0; font-weight: 500;">-</p>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem; letter-spacing: 0.5px;">District</label>
                            <p id="detailDistrict" style="font-size: 0.8125rem; color: #111827; margin: 0; font-weight: 500;">-</p>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem; letter-spacing: 0.5px;">Village</label>
                            <p id="detailVillage" style="font-size: 0.8125rem; color: #111827; margin: 0; font-weight: 500;">-</p>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem; letter-spacing: 0.5px;">Full Address</label>
                            <p id="detailAddress" style="font-size: 0.8125rem; color: #111827; margin: 0; word-break: break-word; line-height: 1.4;">-</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT SIDE: 3/4 (KPI + Visits Table dengan filter) --}}
            <div style="flex: 1; display: flex; flex-direction: column; overflow: hidden;">
                
                {{-- KPI Cards (SMALLER) --}}
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.5rem; padding: 0.75rem; background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%); border-bottom: 1px solid #e5e7eb; flex-shrink: 0;">
                    
                    {{-- KPI 1: Total Visits --}}
                    <div style="background-color: white; padding: 0.625rem; border-radius: 0.5rem; border-left: 3px solid #2563eb; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                        <p style="font-size: 0.6rem; color: #6b7280; text-transform: uppercase; font-weight: 700; margin: 0 0 0.25rem 0; letter-spacing: 0.3px;">
                            <i class="fas fa-calendar-check" style="margin-right: 0.25rem; color: #2563eb;"></i>Visits
                        </p>
                        <p id="kpiTotalVisits" style="font-size: 1.25rem; font-weight: 800; color: #111827; margin: 0;">0</p>
                    </div>
                    
                    {{-- KPI 2: Follow Up Count --}}
                    <div style="background-color: white; padding: 0.625rem; border-radius: 0.5rem; border-left: 3px solid #059669; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                        <p style="font-size: 0.6rem; color: #6b7280; text-transform: uppercase; font-weight: 700; margin: 0 0 0.25rem 0; letter-spacing: 0.3px;">
                            <i class="fas fa-check-circle" style="margin-right: 0.25rem; color: #059669;"></i>Follow Up
                        </p>
                        <p id="kpiFollowUp" style="font-size: 1.25rem; font-weight: 800; color: #111827; margin: 0;">0</p>
                    </div>
                    
                    {{-- KPI 3: Deal Count --}}
                    <div style="background-color: white; padding: 0.625rem; border-radius: 0.5rem; border-left: 3px solid #dc2626; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                        <p style="font-size: 0.6rem; color: #6b7280; text-transform: uppercase; font-weight: 700; margin: 0 0 0.25rem 0; letter-spacing: 0.3px;">
                            <i class="fas fa-handshake" style="margin-right: 0.25rem; color: #dc2626;"></i>Deals
                        </p>
                        <p id="kpiDealCount" style="font-size: 1.25rem; font-weight: 800; color: #111827; margin: 0;">0</p>
                    </div>

                    {{-- KPI 4: Last Visit --}}
                    <div style="background-color: white; padding: 0.625rem; border-radius: 0.5rem; border-left: 3px solid #7c3aed; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                        <p style="font-size: 0.6rem; color: #6b7280; text-transform: uppercase; font-weight: 700; margin: 0 0 0.25rem 0; letter-spacing: 0.3px;">
                            <i class="fas fa-history" style="margin-right: 0.25rem; color: #7c3aed;"></i>Last Visit
                        </p>
                        <p id="kpiLastVisit" style="font-size: 0.75rem; font-weight: 600; color: #111827; margin: 0;">-</p>
                    </div>

                </div>

                {{-- Advanced Filter Section di dalam Modal --}}
                <div style="padding: 1rem; background-color: #f9fafb; border-bottom: 1px solid #e5e7eb; flex-shrink: 0;">
                    <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                        {{-- Month Filter --}}
                        <div style="display: flex; align-items: flex-end; gap: 0.5rem;">
                            <div>
                                <label style="display: block; font-size: 0.65rem; font-weight: 600; color: #6b7280; margin-bottom: 0.25rem;">By Month</label>
                                <input 
                                    type="month" 
                                    id="modalMonthFilter" 
                                    style="padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.8125rem; width: 150px; outline: none;"
                                    value="{{ date('Y-m') }}"
                                >
                            </div>
                            <button 
                                onclick="applyModalMonthFilter()" 
                                style="padding: 0.5rem 1rem; background-color: #2563eb; color: white; border: none; border-radius: 0.375rem; font-size: 0.8125rem; font-weight: 600; cursor: pointer; transition: all 0.2s; white-space: nowrap;">
                                <i class="fas fa-check"></i> Apply
                            </button>
                        </div>

                        {{-- Divider --}}
                        <div style="width: 1px; height: 30px; background-color: #e5e7eb;"></div>

                        {{-- Date Range Filter with Color Highlight --}}
                        <div style="display: flex; align-items: flex-end; gap: 0.5rem;">
                            <div>
                                <label style="display: block; font-size: 0.65rem; font-weight: 600; color: #6b7280; margin-bottom: 0.25rem;">From</label>
                                <input 
                                    type="date" 
                                    id="modalStartDate" 
                                    style="padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.8125rem; width: 130px; outline: none;"
                                >
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.65rem; font-weight: 600; color: #6b7280; margin-bottom: 0.25rem;">To</label>
                                <input 
                                    type="date" 
                                    id="modalEndDate" 
                                    style="padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.8125rem; width: 130px; outline: none;"
                                >
                            </div>
                            <button 
                                onclick="applyModalDateRangeFilter()" 
                                style="padding: 0.5rem 1rem; background-color: #7c3aed; color: white; border: none; border-radius: 0.375rem; font-size: 0.8125rem; font-weight: 600; cursor: pointer; transition: all 0.2s; white-space: nowrap;">
                                <i class="fas fa-check"></i> Apply
                            </button>
                        </div>

                        {{-- Reset Button --}}
                        <button 
                            onclick="resetModalFilter()" 
                            style="padding: 0.5rem 1rem; background-color: #6b7280; color: white; border: none; border-radius: 0.375rem; font-size: 0.8125rem; font-weight: 600; cursor: pointer; transition: all 0.2s; white-space: nowrap; margin-left: auto;">
                            <i class="fas fa-redo"></i> Reset
                        </button>
                    </div>

                    {{-- Active Filter Info dengan animasi smooth --}}
                    <div id="modalFilterInfo" style="margin-top: 0.75rem; padding: 0.5rem 0.75rem; background-color: white; border-left: 3px solid #2563eb; border-radius: 0.375rem; font-size: 0.75rem; color: #374151; display: none; transition: all 0.3s ease-out; max-height: 100px; overflow: hidden;">
                        <strong>Active Filter:</strong> <span id="modalFilterText"></span>
                    </div>
                </div>

                {{-- Visits Table - Header STICKY saat scroll --}}
                <div style="flex: 1; display: flex; flex-direction: column; overflow: hidden; background-color: white; border-radius: 0 0 0.75rem 0.75rem;">
                    <div style="overflow: auto; flex: 1;">
                        <table id="visitsTable" style="width: 100%; font-size: 0.8125rem; border-collapse: collapse;">
                            <thead style="position: sticky; top: 0; z-index: 10; background-color: #f3f4f6;">
                                <tr style="border-bottom: 2px solid #e5e7eb;">
                                    <th style="padding: 0.75rem 0.875rem; text-align: left; font-weight: 700; color: #374151; white-space: nowrap; min-width: 110px;">Tanggal</th>
                                    <th style="padding: 0.75rem 0.875rem; text-align: left; font-weight: 700; color: #374151; white-space: nowrap; min-width: 150px;">Perusahaan</th>
                                    <th style="padding: 0.75rem 0.875rem; text-align: left; font-weight: 700; color: #374151; white-space: nowrap; min-width: 120px;">PIC</th>
                                    <th style="padding: 0.75rem 0.875rem; text-align: left; font-weight: 700; color: #374151; white-space: nowrap; min-width: 100px;">Lokasi</th>
                                    <th style="padding: 0.75rem 0.875rem; text-align: left; font-weight: 700; color: #374151; min-width: 200px;">Tujuan</th>
                                    <th style="padding: 0.75rem 0.875rem; text-align: center; font-weight: 700; color: #374151; white-space: nowrap; min-width: 100px;">Follow Up</th>
                                </tr>
                            </thead>
                            <tbody id="visitsTableBody" style="background-color: white;">
                                {{-- Visits data akan diload via JS --}}
                            </tbody>
                        </table>

                        {{-- Empty State --}}
                        <div id="visitsEmptyState" style="text-align: center; padding: 3rem 2rem; color: #9ca3af; display: none;">
                            <i class="fas fa-inbox" style="font-size: 2.5rem; margin-bottom: 0.75rem; display: block;"></i>
                            <p style="margin: 0; font-size: 0.9375rem; font-weight: 500;">Tidak ada riwayat kunjungan pada periode ini</p>
                        </div>

                        {{-- Loading State --}}
                        <div id="visitsLoadingState" style="text-align: center; padding: 3rem 2rem; color: #6b7280;">
                            <i class="fas fa-spinner fa-spin" style="font-size: 2.5rem; margin-bottom: 0.75rem; display: block;"></i>
                            <p style="margin: 0; font-size: 0.9375rem;">Loading...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Global state untuk modal filter
let currentModalFilter = 'all';
let currentModalUserId = null;
let modalFilterState = {
    month: null,
    year: null,
    startDate: null,
    endDate: null
};
let filterInfoTimeout = null;

// ========== SHOW DETAIL MODAL ==========
function showSalesDetail(userId) {
    console.log('Opening detail for user:', userId);
    currentModalUserId = userId;
    const modal = document.getElementById('salesDetailModal');
    
    currentModalFilter = 'all';
    modalFilterState = { month: null, year: null, startDate: null, endDate: null };
    document.getElementById('modalMonthFilter').value = new Date().toISOString().substring(0, 7);
    document.getElementById('modalStartDate').value = '';
    document.getElementById('modalEndDate').value = '';
    document.getElementById('modalFilterInfo').style.display = 'none';
    
    if (filterInfoTimeout) clearTimeout(filterInfoTimeout);
    
    modal.style.display = 'flex';
    showVisitsLoading(true);
    
    fetch(`/marketing/sales/${userId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        }
    })
    .then(response => {
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        return response.json();
    })
    .then(data => {
        if (data.success && data.user) {
            document.getElementById('detailHeaderUsername').textContent = data.user.username;
            document.getElementById('detailUsername').textContent = data.user.username || '-';
            document.getElementById('detailEmail').textContent = data.user.email || '-';
            document.getElementById('detailPhone').textContent = data.user.phone || '-';
            document.getElementById('detailBirthDate').textContent = data.user.birth_date || '-';
            document.getElementById('detailRole').textContent = data.user.role || '-';
            
            document.getElementById('detailProvince').textContent = data.user.province || '-';
            document.getElementById('detailRegency').textContent = data.user.regency || '-';
            document.getElementById('detailDistrict').textContent = data.user.district || '-';
            document.getElementById('detailVillage').textContent = data.user.village || '-';
            document.getElementById('detailAddress').textContent = data.user.address || '-';
            
            populateVisitsTable(data.visits || []);
            updateKPI(data.visits || [], data.deals || []);
        } else {
            showVisitsError(data.error || 'Data tidak valid');
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        showVisitsError(error.message);
    });
}

// ========== POPULATE VISITS TABLE ==========
function populateVisitsTable(visits) {
    const tableBody = document.getElementById('visitsTableBody');
    const emptyState = document.getElementById('visitsEmptyState');
    
    showVisitsLoading(false);

    if (!visits || visits.length === 0) {
        tableBody.innerHTML = '';
        emptyState.style.display = 'block';
        return;
    }

    emptyState.style.display = 'none';

    let html = '';
    visits.forEach((visit, index) => {
        const rowBg = index % 2 === 0 ? '#ffffff' : '#f9fafb';
        const followUpBg = visit.is_follow_up === 'Ya' ? '#d1fae5' : '#fee2e2';
        const followUpColor = visit.is_follow_up === 'Ya' ? '#065f46' : '#991b1b';
        
        html += `
            <tr style="background-color: ${rowBg}; border-bottom: 1px solid #e5e7eb; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#eff6ff'" onmouseout="this.style.backgroundColor='${rowBg}'">
                <td style="padding: 0.75rem 0.875rem; color: #374151; font-weight: 500; white-space: nowrap;">${visit.visit_date}</td>
                <td style="padding: 0.75rem 0.875rem; color: #374151; white-space: nowrap;">${visit.company_name}</td>
                <td style="padding: 0.75rem 0.875rem; color: #374151; white-space: nowrap;">${visit.pic_name}</td>
                <td style="padding: 0.75rem 0.875rem; color: #374151; font-size: 0.75rem; max-width: 100px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${visit.location}">${visit.location}</td>
                <td style="padding: 0.75rem 0.875rem; color: #374151; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${visit.visit_purpose}">${visit.visit_purpose}</td>
                <td style="padding: 0.75rem 0.875rem; text-align: center;">
                    <span style="background-color: ${followUpBg}; color: ${followUpColor}; padding: 0.375rem 0.75rem; border-radius: 0.375rem; font-weight: 600; font-size: 0.75rem; display: inline-block; white-space: nowrap;">
                        ${visit.is_follow_up}
                    </span>
                </td>
            </tr>
        `;
    });

    tableBody.innerHTML = html;
}

// ========== UPDATE KPI ==========
function updateKPI(visits, deals) {
    const totalVisits = visits.length;
    const followUpCount = visits.filter(v => v.is_follow_up === 'Ya').length;
    const lastVisit = visits.length > 0 ? visits[0].visit_date : '-';
    const dealCount = deals ? deals.length : 0;

    document.getElementById('kpiTotalVisits').textContent = totalVisits;
    document.getElementById('kpiFollowUp').textContent = followUpCount;
    document.getElementById('kpiLastVisit').textContent = lastVisit;
    document.getElementById('kpiDealCount').textContent = dealCount;
}

// ========== MODAL MONTH FILTER ==========
function applyModalMonthFilter() {
    const monthInput = document.getElementById('modalMonthFilter').value;
    if (!monthInput) {
        showModalNotification('Pilih bulan terlebih dahulu', 'warning');
        return;
    }

    const [year, month] = monthInput.split('-');
    currentModalFilter = 'month';
    modalFilterState = { month: parseInt(month), year: parseInt(year), startDate: null, endDate: null };

    showVisitsLoading(true);

    fetch(`/marketing/sales/${currentModalUserId}?year=${year}&month=${month}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.visits !== undefined) {
            populateVisitsTable(data.visits);
            updateKPI(data.visits, data.deals || []);
            
            const monthName = new Date(year, month - 1).toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
            showModalFilterInfo(`Showing visits for ${monthName}`);
        } else {
            showVisitsError(data.error || 'Error loading visits');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showVisitsError(error.message);
    });
}

// ========== MODAL DATE RANGE FILTER ==========
function applyModalDateRangeFilter() {
    const startDate = document.getElementById('modalStartDate').value;
    const endDate = document.getElementById('modalEndDate').value;

    if (!startDate || !endDate) {
        showModalNotification('Pilih kedua tanggal terlebih dahulu', 'warning');
        return;
    }

    if (new Date(startDate) > new Date(endDate)) {
        showModalNotification('Tanggal mulai harus sebelum tanggal akhir', 'error');
        return;
    }

    currentModalFilter = 'dateRange';
    modalFilterState = { month: null, year: null, startDate, endDate };

    showVisitsLoading(true);

    fetch(`/marketing/sales/${currentModalUserId}?start_date=${startDate}&end_date=${endDate}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.visits !== undefined) {
            populateVisitsTable(data.visits);
            updateKPI(data.visits, data.deals || []);
            
            const startObj = new Date(startDate);
            const endObj = new Date(endDate);
            const dateRange = `${startObj.toLocaleDateString('id-ID')} - ${endObj.toLocaleDateString('id-ID')}`;
            showModalFilterInfo(`Showing visits from ${dateRange}`);
        } else {
            showVisitsError(data.error || 'Error loading visits');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showVisitsError(error.message);
    });
}

// ========== RESET MODAL FILTER ==========
function resetModalFilter() {
    currentModalFilter = 'all';
    modalFilterState = { month: null, year: null, startDate: null, endDate: null };
    document.getElementById('modalMonthFilter').value = new Date().toISOString().substring(0, 7);
    document.getElementById('modalStartDate').value = '';
    document.getElementById('modalEndDate').value = '';
    document.getElementById('modalFilterInfo').style.display = 'none';
    
    if (filterInfoTimeout) clearTimeout(filterInfoTimeout);

    showVisitsLoading(true);

    fetch(`/marketing/sales/${currentModalUserId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.visits !== undefined) {
            populateVisitsTable(data.visits);
            updateKPI(data.visits, data.deals || []);
        } else {
            showVisitsError(data.error || 'Error loading visits');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showVisitsError(error.message);
    });
}

// ========== HELPER FUNCTIONS ==========
function showVisitsLoading(isLoading) {
    document.getElementById('visitsLoadingState').style.display = isLoading ? 'block' : 'none';
    document.getElementById('visitsTableBody').style.display = isLoading ? 'none' : 'table-row-group';
    document.getElementById('visitsEmptyState').style.display = 'none';
}

function showVisitsError(message) {
    document.getElementById('visitsLoadingState').style.display = 'none';
    document.getElementById('visitsTableBody').innerHTML = '';
    document.getElementById('visitsEmptyState').innerHTML = `
        <div style="text-align: center; padding: 2rem; color: #dc2626; background-color: #fee2e2; border-radius: 0.5rem; margin: 1rem;">
            <i class="fas fa-exclamation-triangle" style="font-size: 2.5rem; margin-bottom: 0.75rem; display: block;"></i>
            <p style="margin: 0; font-size: 0.9375rem; font-weight: 500;">Error: ${message}</p>
        </div>
    `;
    document.getElementById('visitsEmptyState').style.display = 'block';
}

function showModalFilterInfo(text) {
    const filterInfoElement = document.getElementById('modalFilterInfo');
    document.getElementById('modalFilterText').textContent = text;
    
    if (filterInfoTimeout) clearTimeout(filterInfoTimeout);
    
    filterInfoElement.style.display = 'block';
    filterInfoElement.style.maxHeight = '100px';
    filterInfoElement.style.opacity = '1';
    filterInfoElement.style.padding = '0.5rem 0.75rem';
    filterInfoElement.style.margin = '0.75rem 0 0 0';
    
    // AUTO HIDE SETELAH 2 DETIK ✅
    filterInfoTimeout = setTimeout(() => {
        filterInfoElement.style.maxHeight = '0px';
        filterInfoElement.style.opacity = '0';
        filterInfoElement.style.padding = '0';
        filterInfoElement.style.margin = '0';
        
        setTimeout(() => {
            filterInfoElement.style.display = 'none';
        }, 300);
    }, 2000);
}

function showModalNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 0.75rem 1.25rem;
        border-radius: 0.5rem;
        color: white;
        font-weight: 600;
        font-size: 0.875rem;
        z-index: 10001;
        animation: slideInNotif 0.3s ease-out;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    `;
    
    if (type === 'success') {
        notification.style.backgroundColor = '#10b981';
        notification.innerHTML = '<i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i>' + message;
    } else if (type === 'error') {
        notification.style.backgroundColor = '#ef4444';
        notification.innerHTML = '<i class="fas fa-exclamation-circle" style="margin-right: 0.5rem;"></i>' + message;
    } else if (type === 'warning') {
        notification.style.backgroundColor = '#f59e0b';
        notification.innerHTML = '<i class="fas fa-exclamation-triangle" style="margin-right: 0.5rem;"></i>' + message;
    } else {
        notification.style.backgroundColor = '#3b82f6';
        notification.innerHTML = '<i class="fas fa-info-circle" style="margin-right: 0.5rem;"></i>' + message;
    }
    
    document.body.appendChild(notification);
    
    // AUTO HIDE NOTIF SETELAH 2 DETIK
    setTimeout(() => {
        notification.style.animation = 'slideOutNotif 0.3s ease-in';
        setTimeout(() => notification.remove(), 300);
    }, 2000);
}

function closeSalesDetailModal() {
    document.getElementById('salesDetailModal').style.display = 'none';
    currentModalUserId = null;
    
    if (filterInfoTimeout) clearTimeout(filterInfoTimeout);
}

// Close when clicking outside
document.getElementById('salesDetailModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeSalesDetailModal();
    }
});
</script>

<style>
@keyframes slideInNotif {
    from {
        opacity: 0;
        transform: translateX(400px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideOutNotif {
    from {
        opacity: 1;
        transform: translateX(0);
    }
    to {
        opacity: 0;
        transform: translateX(400px);
    }
}

/* Input focus styles */
#modalMonthFilter:focus,
#modalStartDate:focus,
#modalEndDate:focus {
    outline: none;
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Date input dengan custom styling untuk range highlight */
input[type="date"] {
    color-scheme: light;
}

input[type="date"]::-webkit-calendar-picker-indicator {
    cursor: pointer;
}

/* Scrollbar styling */
#visitsTable::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

#visitsTable::-webkit-scrollbar-track {
    background: #f1f5f9;
}

#visitsTable::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

#visitsTable::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Responsive */
@media (max-width: 1280px) {
    #salesDetailModal > div {
        width: 98%;
    }
}

@media (max-width: 768px) {
    #salesDetailModal > div {
        width: 99%;
        height: 98vh;
        flex-direction: column;
    }
    
    #salesDetailModal > div > div:nth-child(2) {
        width: 100% !important;
        border-right: none !important;
        border-bottom: 1px solid #e5e7eb;
        max-height: 40%;
    }
    
    #salesDetailModal > div > div:nth-child(3) {
        flex: 1 !important;
        width: 100% !important;
    }
}

@media (max-width: 640px) {
    #salesDetailModal > div {
        width: 100%;
        height: 100vh;
        border-radius: 0;
    }
    
    #salesDetailModal > div > div:nth-child(2) {
        width: 100% !important;
        max-height: 50%;
    }
}
</style>