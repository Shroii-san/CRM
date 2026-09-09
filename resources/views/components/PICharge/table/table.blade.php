@props(['currentMenuId' => 10, 'pics', 'companies'])

<!-- Table Only (No Header, No Pagination) -->
<div class="overflow-x-auto" style="margin: 0; padding: 0;">
    <table id="picTable" class="w-full" style="margin: 0; border-collapse: collapse;">
        <thead style="background-color: #f9fafb; border-bottom: 1px solid #e5e7eb;">
            <tr>
                <th style="padding: 0.5rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; white-space: nowrap;">No</th>
                <th style="padding: 0.5rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; white-space: nowrap;">Nama PIC</th>
                <th style="padding: 0.5rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; white-space: nowrap;">Jabatan</th>
                <th style="padding: 0.5rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; white-space: nowrap;">Email</th>
                <th style="padding: 0.5rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; white-space: nowrap;">Telepon</th>
                <th style="padding: 0.5rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; white-space: nowrap;">Perusahaan</th>
                <th style="padding: 0.5rem 0.75rem; text-align: right; font-size: 0.7rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; white-space: nowrap;">Aksi</th>
            </tr>
        </thead>
        <tbody style="background-color: #ffffff; border-top: 1px solid #e5e7eb;">
            @forelse($pics as $index => $pic)
            <tr style="border-bottom: 1px solid #e5e7eb; transition: background-color 0.15s;" onmouseover="this.style.backgroundColor='#f9fafb'" onmouseout="this.style.backgroundColor='#ffffff'">
                <td style="padding: 0.5rem 0.75rem; font-size: 0.8125rem; color: #111827; white-space: nowrap;">
                    <span style="font-weight: 500;">{{ $pics->firstItem() + $index }}</span>
                </td>
                
                <td style="padding: 0.5rem 0.75rem; white-space: nowrap;">
                    <div style="display: flex; align-items: center;">
                        <div style="width: 2rem; height: 2rem; flex-shrink: 0;">
                            <div style="width: 2rem; height: 2rem; border-radius: 9999px; background-color: #e0e7ff; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-user-tie" style="color: #6366f1; font-size: 0.75rem;"></i>
                            </div>
                        </div>
                        <div style="margin-left: 0.5rem;">
                            <div style="font-size: 0.8125rem; font-weight: 500; color: #111827;">{{ $pic->name }}</div>
                        </div>
                    </div>
                </td>
                
                <td style="padding: 0.5rem 0.75rem; white-space: nowrap;">
                    @if($pic->position)
                        <span style="display: inline-flex; align-items: center; padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 500; background-color: #f3e8ff; color: #7c3aed;">
                            {{ $pic->position }}
                        </span>
                    @else
                        <span style="color: #9ca3af;">-</span>
                    @endif
                </td>
                
                <td style="padding: 0.5rem 0.75rem; white-space: nowrap;">
                    @if($pic->email)
                        <a href="mailto:{{ $pic->email }}" style="font-size: 0.8125rem; color: #6366f1; text-decoration: none; display: flex; align-items: center; gap: 0.375rem;" onmouseover="this.style.color='#4f46e5'" onmouseout="this.style.color='#6366f1'">
                            <i class="fas fa-envelope" style="font-size: 0.6875rem;"></i>
                            {{ $pic->email }}
                        </a>
                    @else
                        <span style="color: #9ca3af;">-</span>
                    @endif
                </td>
                
                <td style="padding: 0.5rem 0.75rem; white-space: nowrap;">
                    @if($pic->phone)
                        <a href="tel:{{ $pic->phone }}" style="font-size: 0.8125rem; color: #6366f1; text-decoration: none; display: flex; align-items: center; gap: 0.375rem;" onmouseover="this.style.color='#4f46e5'" onmouseout="this.style.color='#6366f1'">
                            <i class="fas fa-phone" style="font-size: 0.6875rem;"></i>
                            {{ $pic->phone }}
                        </a>
                    @else
                        <span style="color: #9ca3af;">-</span>
                    @endif
                </td>
                
                <td style="padding: 0.5rem 0.75rem;">
                    <div style="font-size: 0.8125rem; color: #111827;">
                        <div style="display: flex; align-items: center; gap: 0.25rem;">
                            <i class="fas fa-building" style="color: #9ca3af; font-size: 0.6875rem;"></i>
                            <span>{{ $pic->company->company_name ?? '-' }}</span>
                        </div>
                    </div>
                </td>
                
                <td style="padding: 0.5rem 0.75rem; font-size: 0.8125rem; font-weight: 500; text-align: right; white-space: nowrap;">
                    <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.375rem;">
                        @if(auth()->user()->canAccess($currentMenuId, 'edit'))
                        <button 
                            onclick="openEditPICModal('{{ $pic->pic_id }}', '{{ $pic->company_id }}', '{{ addslashes($pic->name) }}', '{{ addslashes($pic->position ?? '') }}', '{{ addslashes($pic->email ?? '') }}', '{{ addslashes($pic->phone ?? '') }}')"
                            style="color: #2563eb; background: transparent; border: none; padding: 0.375rem; border-radius: 0.375rem; cursor: pointer; transition: all 0.15s; font-size: 0.875rem;"
                            onmouseover="this.style.backgroundColor='#dbeafe'; this.style.color='#1e40af';"
                            onmouseout="this.style.backgroundColor='transparent'; this.style.color='#2563eb';"
                            title="Edit PIC">
                            <i class="fas fa-edit"></i>
                        </button>
                        @endif
                        
                        @if(auth()->user()->canAccess($currentMenuId, 'delete'))
                        <button type="button" 
                            onclick="deletePIC('{{ $pic->pic_id }}', '{{ route('pics.destroy', $pic->pic_id) }}', '{{ csrf_token() }}')"
                            style="color: #dc2626; background: transparent; border: none; padding: 0.375rem; border-radius: 0.375rem; cursor: pointer; transition: all 0.15s; font-size: 0.875rem;"
                            onmouseover="this.style.backgroundColor='#fee2e2'; this.style.color='#991b1b';"
                            onmouseout="this.style.backgroundColor='transparent'; this.style.color='#dc2626';"
                            title="Hapus PIC">
                            <i class="fas fa-trash"></i>
                        </button>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="padding: 3rem 1.5rem; text-align: center;">
                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
                        <div style="width: 6rem; height: 6rem; border-radius: 9999px; background-color: #f3f4f6; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                            <i class="fas fa-inbox" style="font-size: 3rem; color: #d1d5db;"></i>
                        </div>
                        <h3 style="font-size: 1.125rem; font-weight: 500; color: #111827; margin: 0 0 0.25rem 0;">Belum Ada Data</h3>
                        <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">Belum ada data PIC yang tersedia</p>
                        @if(auth()->user()->canAccess($currentMenuId, 'create'))
                        <button onclick="openPICModal()" style="margin-top: 1rem; padding: 0.5rem 1rem; background-color: #6366f1; color: white; border: none; border-radius: 0.5rem; cursor: pointer; transition: background-color 0.2s;">
                            <i class="fas fa-plus" style="margin-right: 0.5rem;"></i>
                            Tambah PIC
                        </button>
                        @endif
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<style>
/* Responsive improvements */
@media (max-width: 1024px) {
    #picTable {
        font-size: 0.8125rem;
    }
    
    #picTable th,
    #picTable td {
        padding: 0.4rem 0.65rem;
    }
}

@media (max-width: 768px) {
    #picTable {
        font-size: 0.75rem;
    }
    
    #picTable th,
    #picTable td {
        padding: 0.375rem 0.5rem;
    }
}
</style>