@props(['salesVisits', 'currentMenuId'])

<!-- Table Only -->
<div class="overflow-x-auto fade-in" style="margin: 0; padding: 0;">
    <table id="salesVisitTable" class="w-full" style="margin: 0; border-collapse: collapse;">
        <thead style="background-color: #f9fafb; border-bottom: 1px solid #e5e7eb;">
            <tr>
                <th style="padding: 0.5rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; white-space: nowrap;">No</th>
                <th style="padding: 0.5rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; white-space: nowrap;">Visit Date</th>
                <th style="padding: 0.5rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; white-space: nowrap;">Company</th>
                <th style="padding: 0.5rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; white-space: nowrap;">PIC Name</th>
                <th style="padding: 0.5rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; white-space: nowrap;">Location</th>
                <th style="padding: 0.5rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; white-space: nowrap;">Purpose</th>
                <th style="padding: 0.5rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; white-space: nowrap;">Sales</th>
                <th style="padding: 0.5rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; white-space: nowrap;">Follow Up</th>
                <th style="padding: 0.5rem 0.75rem; text-align: right; font-size: 0.7rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; white-space: nowrap;">Aksi</th>
            </tr>
        </thead>
        <tbody style="background-color: #ffffff; border-top: 1px solid #e5e7eb;">
            @forelse($salesVisits as $index => $visit)
            <tr style="border-bottom: 1px solid #e5e7eb; transition: background-color 0.15s;" onmouseover="this.style.backgroundColor='#f9fafb'" onmouseout="this.style.backgroundColor='#ffffff'">
                <!-- No -->
                <td style="padding: 0.5rem 0.75rem; font-size: 0.8125rem; color: #111827; white-space: nowrap;">
                    <span style="font-weight: 500;">{{ $salesVisits->firstItem() + $index }}</span>
                </td>
                
                <!-- Visit Date -->
                <td style="padding: 0.5rem 0.75rem; white-space: nowrap;">
                    <div style="font-size: 0.8125rem; color: #111827;">
                        @if($visit->visit_date)
                            <div style="display: flex; align-items: center; gap: 0.375rem;">
                                <i class="fas fa-calendar" style="color: #9ca3af; font-size: 0.6875rem;"></i>
                                <span>{{ $visit->visit_date->format('d/m/Y') }}</span>
                            </div>
                        @else
                            <span style="color: #9ca3af;">-</span>
                        @endif
                    </div>
                </td>
                
                <!-- Company -->
                <td style="padding: 0.5rem 0.75rem;">
                    <div style="font-size: 0.8125rem; color: #111827;">
                        @if($visit->company)
                            <span style="display: flex; align-items: center; gap: 0.25rem;">
                                <i class="fas fa-building" style="color: #9ca3af; font-size: 0.6875rem;"></i>
                                {{ $visit->company->company_name }}
                            </span>
                        @else
                            <span style="color: #9ca3af;">-</span>
                        @endif
                    </div>
                </td>
                
                <!-- PIC Name -->
                <td style="padding: 0.5rem 0.75rem; white-space: nowrap;">
                    <div style="font-size: 0.8125rem; font-weight: 500; color: #111827;">{{ $visit->pic_name ?? '-' }}</div>
                </td>
                
                <!-- Location -->
                <td style="padding: 0.5rem 0.75rem;">
                    <div style="font-size: 0.8125rem; color: #111827;">
                        @php
                            $province = optional($visit->province)->name ?? '';
                            $regency = optional($visit->regency)->name ?? '';
                            $district = optional($visit->district)->name ?? '';
                            $village = optional($visit->village)->name ?? '';
                            
                            $locationMain = $province ?: '-';
                            $locationSub = collect([$regency, $district, $village])
                                ->filter()
                                ->implode(', ');
                        @endphp
                        
                        @if($locationMain !== '-')
                            <div style="display: flex; align-items: center; gap: 0.25rem;">
                                <i class="fas fa-map-marker-alt" style="color: #9ca3af; font-size: 0.6875rem;"></i>
                                <span>{{ $locationMain }}</span>
                            </div>
                            @if($locationSub)
                                <div style="font-size: 0.6875rem; color: #6b7280; margin-top: 0.125rem;">
                                    {{ $locationSub }}
                                </div>
                            @endif
                        @else
                            <span style="color: #9ca3af;">-</span>
                        @endif
                    </div>
                </td>
                
                <!-- Purpose -->
                <td style="padding: 0.5rem 0.75rem;">
                    <div style="font-size: 0.8125rem; color: #374151; max-width: 16rem;">
                        @if($visit->visit_purpose)
                            <span style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;" title="{{ $visit->visit_purpose }}">
                                {{ Str::limit($visit->visit_purpose, 45) }}
                            </span>
                        @else
                            <span style="color: #9ca3af;">-</span>
                        @endif
                    </div>
                </td>
                
                <!-- Sales -->
                <td style="padding: 0.5rem 0.75rem; white-space: nowrap;">
                    <div style="display: flex; align-items: center;">
                        <div style="width: 2rem; height: 2rem; flex-shrink: 0;">
                            <div style="width: 2rem; height: 2rem; border-radius: 9999px; background-color: #e0e7ff; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-user-tie" style="color: #6366f1; font-size: 0.75rem;"></i>
                            </div>
                        </div>
                        <div style="margin-left: 0.5rem;">
                            <div style="font-size: 0.8125rem; font-weight: 500; color: #111827;">{{ $visit->sales->username ?? '-' }}</div>
                            <div style="font-size: 0.6875rem; color: #6b7280;">{{ $visit->sales->email ?? 'No email' }}</div>
                        </div>
                    </div>
                </td>
                
                <!-- Follow Up -->
                <td style="padding: 0.5rem 0.75rem; white-space: nowrap;">
                    @if($visit->is_follow_up)
                        <span style="display: inline-flex; align-items: center; padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 500; background-color: #d1fae5; color: #065f46;">
                            <i class="fas fa-check-circle" style="margin-right: 0.25rem; font-size: 0.625rem;"></i>
                            Ya
                        </span>
                    @else
                        <span style="display: inline-flex; align-items: center; padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 500; background-color: #f3f4f6; color: #374151;">
                            <i class="fas fa-times-circle" style="margin-right: 0.25rem; font-size: 0.625rem;"></i>
                            Tidak
                        </span>
                    @endif
                </td>
                
                <!-- Aksi -->
                <td style="padding: 0.5rem 0.75rem; font-size: 0.8125rem; font-weight: 500; text-align: right; white-space: nowrap;">
                    <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.375rem;">
                        {{-- Show Detail Button --}}
                        @if(auth()->user()->canAccess($currentMenuId, 'view'))
                        <button 
                            onclick="showVisitDetail('{{ $visit->id }}')" 
                            style="color: #059669; background: transparent; border: none; padding: 0.375rem; border-radius: 0.375rem; cursor: pointer; transition: all 0.15s; font-size: 0.875rem;"
                            onmouseover="this.style.backgroundColor='#d1fae5'; this.style.color='#047857';"
                            onmouseout="this.style.backgroundColor='transparent'; this.style.color='#059669';"
                            title="Show Detail">
                            <i class="fas fa-eye"></i>
                        </button>
                        @endif

                        @if(auth()->user()->canAccess($currentMenuId, 'edit'))
                        <button 
                            onclick="openEditVisitModal({{ json_encode([
                                'id' => $visit->id,
                                'salesId' => $visit->sales_id,
                                'picId' => $visit->pic_id,
                                'picName' => $visit->pic_name,
                                'companyId' => $visit->company_id,
                                'companyName' => optional($visit->company)->company_name ?? '',
                                'provinceId' => $visit->province_id,
                                'regencyId' => $visit->regency_id,
                                'districtId' => $visit->district_id,
                                'villageId' => $visit->village_id,
                                'address' => $visit->address ?? '',
                                'visitDate' => $visit->visit_date->format('Y-m-d'),
                                'purpose' => $visit->visit_purpose,
                                'followUp' => $visit->is_follow_up ? 1 : 0
                            ]) }})"
                            style="color: #2563eb; background: transparent; border: none; padding: 0.375rem; border-radius: 0.375rem; cursor: pointer; transition: all 0.15s; font-size: 0.875rem;"
                            onmouseover="this.style.backgroundColor='#dbeafe'; this.style.color='#1e40af';"
                            onmouseout="this.style.backgroundColor='transparent'; this.style.color='#2563eb';"
                            title="Edit Visit">
                            <i class="fas fa-edit"></i>
                        </button>
                        @endif
                        
                        @if(auth()->user()->canAccess($currentMenuId, 'delete'))
                        <button type="button" 
                            onclick="deleteVisit({{ $visit->id }}, '{{ route('salesvisit.destroy', $visit->id) }}', '{{ csrf_token() }}')"
                            style="color: #dc2626; background: transparent; border: none; padding: 0.375rem; border-radius: 0.375rem; cursor: pointer; transition: all 0.15s; font-size: 0.875rem;"
                            onmouseover="this.style.backgroundColor='#fee2e2'; this.style.color='#991b1b';"
                            onmouseout="this.style.backgroundColor='transparent'; this.style.color='#dc2626';"
                            title="Hapus Visit">
                            <i class="fas fa-trash"></i>
                        </button>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="padding: 3rem 1.5rem; text-align: center;">
                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
                        <div style="width: 6rem; height: 6rem; border-radius: 9999px; background-color: #f3f4f6; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                            <i class="fas fa-inbox" style="font-size: 3rem; color: #d1d5db;"></i>
                        </div>
                        <h3 style="font-size: 1.125rem; font-weight: 500; color: #111827; margin: 0 0 0.25rem 0;">Belum Ada Data</h3>
                        <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">Belum ada data kunjungan sales yang tersedia</p>
                        @if(auth()->user()->canAccess($currentMenuId, 'create'))
                        <button onclick="openVisitModal()" style="margin-top: 1rem; padding: 0.5rem 1rem; background-color: #6366f1; color: white; border: none; border-radius: 0.5rem; cursor: pointer; transition: background-color 0.2s;">
                            <i class="fas fa-plus" style="margin-right: 0.5rem;"></i>
                            Tambah Kunjungan
                        </button>
                        @endif
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Visit Detail Modal --}}
<div id="visitDetailModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background-color: white; border-radius: 0.5rem; width: 90%; max-width: 900px; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
        {{-- Modal Header --}}
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.25rem; background: linear-gradient(to right, #6366f1, #8b5cf6);">
            <h3 style="font-size: 1.125rem; font-weight: 600; color: white; margin: 0;">
                <i class="fas fa-handshake" style="margin-right: 0.5rem;"></i>
                Detail Kunjungan Sales
            </h3>
            <button onclick="closeVisitDetailModal()" style="color: white; background: transparent; border: none; font-size: 1.5rem; cursor: pointer; padding: 0; line-height: 1; opacity: 0.9; transition: opacity 0.15s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.9'">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Modal Body --}}
        <div style="padding: 1.25rem;">
            {{-- Visit Info - 2 Columns Layout --}}
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.25rem; margin-bottom: 1.5rem;">
                {{-- Left Column --}}
                <div style="background-color: #f9fafb; padding: 1rem; border-radius: 0.5rem;">
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; font-size: 0.6875rem; font-weight: 500; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Tanggal Kunjungan</label>
                        <p id="detailVisitDate" style="font-size: 0.875rem; font-weight: 600; color: #111827; margin: 0;">-</p>
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; font-size: 0.6875rem; font-weight: 500; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Perusahaan</label>
                        <p id="detailCompanyName" style="font-size: 0.875rem; color: #111827; margin: 0;">-</p>
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; font-size: 0.6875rem; font-weight: 500; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Lokasi Kunjungan</label>
                        <p id="detailLocation" style="font-size: 0.875rem; color: #111827; margin: 0;">-</p>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.6875rem; font-weight: 500; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Alamat Lengkap</label>
                        <p id="detailAddress" style="font-size: 0.875rem; color: #111827; margin: 0;">-</p>
                    </div>
                </div>

                {{-- Right Column --}}
                <div style="background-color: #f9fafb; padding: 1rem; border-radius: 0.5rem;">
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; font-size: 0.6875rem; font-weight: 500; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Sales</label>
                        <p id="detailSalesName" style="font-size: 0.875rem; color: #111827; margin: 0;">-</p>
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; font-size: 0.6875rem; font-weight: 500; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Follow Up</label>
                        <span id="detailFollowUp" style="display: inline-flex; align-items: center; padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 500;">-</span>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.6875rem; font-weight: 500; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Tujuan Kunjungan</label>
                        <p id="detailPurpose" style="font-size: 0.875rem; color: #111827; margin: 0;">-</p>
                    </div>
                </div>
            </div>

            {{-- PIC Section --}}
            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                    <h4 style="font-size: 0.9375rem; font-weight: 600; color: #111827; margin: 0;">
                        <i class="fas fa-user-tie" style="margin-right: 0.375rem; color: #6366f1;"></i>
                        PIC yang Ditemui
                    </h4>
                </div>

                <div id="picDetailContainer">
                    {{-- PIC detail will be loaded here --}}
                </div>
            </div>
        </div>

        {{-- Modal Footer --}}
        <div style="display: flex; justify-content: flex-end; padding: 0.75rem 1rem; border-top: 1px solid #e5e7eb; background-color: #f9fafb;">
            <button onclick="closeVisitDetailModal()" style="padding: 0.5rem 1.25rem; background-color: #6b7280; color: white; border: none; border-radius: 0.375rem; font-size: 0.8125rem; font-weight: 500; cursor: pointer; transition: all 0.15s;">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
// Show Visit Detail Modal
function showVisitDetail(visitId) {
    const modal = document.getElementById('visitDetailModal');
    modal.style.display = 'flex';
    
    // Show loading
    document.getElementById('picDetailContainer').innerHTML = `
        <div style="text-align: center; padding: 2rem; color: #6b7280;">
            <i class="fas fa-spinner fa-spin" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
            <p style="margin: 0;">Loading...</p>
        </div>
    `;
    
    // Fetch visit detail
    fetch(`/salesvisit/${visitId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const visit = data.visit;
                
                // Fill visit info
                document.getElementById('detailVisitDate').textContent = visit.visit_date;
                document.getElementById('detailSalesName').textContent = visit.sales_name;
                document.getElementById('detailCompanyName').textContent = visit.company_name;
                document.getElementById('detailLocation').textContent = visit.location;
                document.getElementById('detailAddress').textContent = visit.address || '-';
                document.getElementById('detailPurpose').textContent = visit.visit_purpose;
                
                // Follow Up badge
                const followUpBadge = document.getElementById('detailFollowUp');
                if (visit.is_follow_up) {
                    followUpBadge.innerHTML = '<i class="fas fa-check-circle" style="margin-right: 0.25rem; font-size: 0.625rem;"></i> Ya';
                    followUpBadge.style.backgroundColor = '#d1fae5';
                    followUpBadge.style.color = '#065f46';
                } else {
                    followUpBadge.innerHTML = '<i class="fas fa-times-circle" style="margin-right: 0.25rem; font-size: 0.625rem;"></i> Tidak';
                    followUpBadge.style.backgroundColor = '#f3f4f6';
                    followUpBadge.style.color = '#374151';
                }
                
                // Fill PIC Detail
                const picContainer = document.getElementById('picDetailContainer');
                
                if (data.pic) {
                    const pic = data.pic;
                    picContainer.innerHTML = `
                        <div style="border: 1px solid #e5e7eb; border-radius: 0.375rem; padding: 0.75rem; background-color: white;">
                            <div style="display: flex; align-items: start; gap: 0.75rem;">
                                <div style="flex-shrink: 0; width: 2.5rem; height: 2.5rem; border-radius: 9999px; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 1rem;">
                                    ${pic.pic_name.charAt(0).toUpperCase()}
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <h5 style="font-size: 0.875rem; font-weight: 600; color: #111827; margin: 0 0 0.125rem 0;">${pic.pic_name}</h5>
                                    <p style="font-size: 0.75rem; color: #6b7280; margin: 0 0 0.375rem 0;">${pic.position}</p>
                                    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                                        <div style="display: flex; align-items: center; gap: 0.375rem;">
                                            <i class="fas fa-phone" style="color: #059669; font-size: 0.6875rem;"></i>
                                            <span style="font-size: 0.75rem; color: #374151;">${pic.phone}</span>
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 0.375rem;">
                                            <i class="fas fa-envelope" style="color: #6366f1; font-size: 0.6875rem;"></i>
                                            <span style="font-size: 0.75rem; color: #374151;">${pic.email}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                } else {
                    picContainer.innerHTML = `
                        <div style="text-align: center; padding: 3rem; background-color: #f9fafb; border-radius: 0.5rem; border: 2px dashed #e5e7eb;">
                            <div style="width: 4rem; height: 4rem; border-radius: 9999px; background-color: #f3f4f6; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                                <i class="fas fa-user-slash" style="font-size: 1.5rem; color: #d1d5db;"></i>
                            </div>
                            <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">Data PIC tidak tersedia</p>
                        </div>
                    `;
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('picDetailContainer').innerHTML = `
                <div style="text-align: center; padding: 2rem; color: #dc2626; background-color: #fee2e2; border-radius: 0.5rem;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
                    <p style="margin: 0;">Gagal memuat data</p>
                </div>
            `;
        });
}

// Close Modal
function closeVisitDetailModal() {
    document.getElementById('visitDetailModal').style.display = 'none';
}

// Close modal when clicking outside
document.getElementById('visitDetailModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeVisitDetailModal();
    }
});
</script>

<style>
/* Responsive improvements */
@media (max-width: 1024px) {
    #salesVisitTable {
        font-size: 0.8125rem;
    }
    
    #salesVisitTable th,
    #salesVisitTable td {
        padding: 0.4rem 0.65rem;
    }
}

@media (max-width: 768px) {
    #salesVisitTable {
        font-size: 0.75rem;
    }
    
    #salesVisitTable th,
    #salesVisitTable td {
        padding: 0.375rem 0.5rem;
    }
    
    #visitDetailModal > div {
        width: 95%;
        margin: 1rem;
    }
    
    /* Mobile: Stack columns vertically */
    #visitDetailModal > div > div:nth-child(2) > div:first-child {
        grid-template-columns: 1fr !important;
    }
}
</style>