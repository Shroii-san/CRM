@if ($transaksi->count() > 0)
<div style="overflow-x: auto;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead style="background-color: #f9fafb; border-bottom: 2px solid #e5e7eb;">
            <tr>
                <th style="padding: 0.625rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 600; color: #4b5563; text-transform: uppercase; letter-spacing: 0.5px;">Nama Sales</th>
                <th style="padding: 0.625rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 600; color: #4b5563; text-transform: uppercase; letter-spacing: 0.5px;">Perusahaan</th>
                <th style="padding: 0.625rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 600; color: #4b5563; text-transform: uppercase; letter-spacing: 0.5px;">PIC</th>
                <th style="padding: 0.625rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 600; color: #4b5563; text-transform: uppercase; letter-spacing: 0.5px;">Nilai Proyek</th>
                <th style="padding: 0.625rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 600; color: #4b5563; text-transform: uppercase; letter-spacing: 0.5px;">Status</th>
                <th style="padding: 0.625rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 600; color: #4b5563; text-transform: uppercase; letter-spacing: 0.5px;">Tanggal</th>
                <th style="padding: 0.625rem 0.75rem; text-align: right; font-size: 0.7rem; font-weight: 600; color: #4b5563; text-transform: uppercase; letter-spacing: 0.5px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaksi as $item)
            <tr style="border-bottom: 1px solid #e5e7eb; transition: background-color 0.2s;"
                onmouseover="this.style.backgroundColor='#f9fafb'"
                onmouseout="this.style.backgroundColor='white'">
                <td style="padding: 0.625rem 0.75rem; font-weight: 500; color: #111827; font-size: 0.8rem;">{{ $item->nama_sales }}</td>
                <td style="padding: 0.625rem 0.75rem; color: #6b7280; font-size: 0.75rem;">{{ $item->nama_perusahaan }}</td>
                <td style="padding: 0.625rem 0.75rem; color: #111827; font-size: 0.75rem; font-weight: 500;">
                    @if($item->pic)
                        <span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.25rem 0.5rem; background-color: #e0f2fe; color: #0369a1; border-radius: 0.25rem; font-weight: 600;">
                            <i class="fas fa-user-tie" style="font-size: 0.65rem;"></i>
                            {{ $item->pic->pic_name }}
                        </span>
                    @elseif($item->pic_name)
                        <span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.25rem 0.5rem; background-color: #fef3c7; color: #92400e; border-radius: 0.25rem; font-weight: 600;">
                            <i class="fas fa-user-tie" style="font-size: 0.65rem;"></i>
                            {{ $item->pic_name }}
                        </span>
                    @else
                        <span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.25rem 0.5rem; background-color: #e5e7eb; color: #6b7280; border-radius: 0.25rem; font-weight: 600;">
                            <i class="fas fa-user-slash" style="font-size: 0.65rem;"></i>
                            Tidak ada
                        </span>
                    @endif
                </td>
                <td style="padding: 0.625rem 0.75rem; font-weight: 600; color: #111827; font-size: 0.8rem;">Rp{{ number_format($item->nilai_proyek, 0, ',', '.') }}</td>
                <td style="padding: 0.625rem 0.75rem;">
                    @if ($item->status === 'Deals')
                        <span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.25rem 0.5rem; background-color: #dcfce7; color: #166534; border-radius: 9999px; font-size: 0.7rem; font-weight: 600;">
                            <i class="fas fa-check-circle"></i> Deals
                        </span>
                    @else
                        <span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.25rem 0.5rem; background-color: #fee2e2; color: #991b1b; border-radius: 9999px; font-size: 0.7rem; font-weight: 600;">
                            <i class="fas fa-times-circle"></i> Fails
                        </span>
                    @endif
                </td>
                <td style="padding: 0.625rem 0.75rem; color: #6b7280; font-size: 0.75rem;">{{ $item->created_at->format('d/m/Y') }}</td>
                <td style="padding: 0.625rem 0.75rem; text-align: right;">
                    <div style="display: flex; gap: 0.375rem; justify-content: flex-end;">
                        <button onclick="viewTransaksi({{ $item->id }})"
                            style="display: inline-flex; align-items: center; justify-content: center; width: 1.75rem; height: 1.75rem; background-color: #dbeafe; color: #2563eb; border: none; border-radius: 0.3rem; cursor: pointer; transition: all 0.2s; font-size: 0.75rem;"
                            onmouseover="this.style.backgroundColor='#bfdbfe'; this.style.transform='scale(1.05)'"
                            onmouseout="this.style.backgroundColor='#dbeafe'; this.style.transform='scale(1)'"
                            title="Lihat Detail">
                            <i class="fas fa-eye"></i>
                        </button>
                        @if(auth()->user()->canAccess($currentMenuId ?? 17, 'update'))
                        <button onclick="editTransaksi({{ $item->id }})"
                            style="display: inline-flex; align-items: center; justify-content: center; width: 1.75rem; height: 1.75rem; background-color: #fef3c7; color: #d97706; border: none; border-radius: 0.3rem; cursor: pointer; transition: all 0.2s; font-size: 0.75rem;"
                            onmouseover="this.style.backgroundColor='#fde68a'; this.style.transform='scale(1.05)'"
                            onmouseout="this.style.backgroundColor='#fef3c7'; this.style.transform='scale(1)'"
                            title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        @endif
                        @if(auth()->user()->canAccess($currentMenuId ?? 17, 'delete'))
                        <form action="{{ route('transaksi.destroy', $item->id) }}" method="POST" style="display: inline;"
                            onsubmit="return confirm('Yakin ingin menghapus transaksi ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                style="display: inline-flex; align-items: center; justify-content: center; width: 1.75rem; height: 1.75rem; background-color: #fee2e2; color: #dc2626; border: none; border-radius: 0.3rem; cursor: pointer; transition: all 0.2s; font-size: 0.75rem;"
                                onmouseover="this.style.backgroundColor='#fecaca'; this.style.transform='scale(1.05)'"
                                onmouseout="this.style.backgroundColor='#fee2e2'; this.style.transform='scale(1)'"
                                title="Hapus">
                                <i class="fas fa-trash-alt"></i>
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
@else
<div style="padding: 2rem; text-align: center;">
    <i class="fas fa-inbox" style="font-size: 2.5rem; color: #d1d5db; margin-bottom: 0.75rem;"></i>
    <h3 style="font-size: 1rem; font-weight: 600; color: #111827; margin-bottom: 0.375rem;">Belum ada transaksi</h3>
    <p style="color: #6b7280; margin-bottom: 1rem; font-size: 0.875rem;">Mulai tambahkan transaksi dari sales visit</p>
    <button onclick="openTransaksiModal()"
        style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 0.875rem; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; border: none; border-radius: 0.375rem; font-weight: 500; font-size: 0.75rem; cursor: pointer;">
        <i class="fas fa-plus"></i> Tambah Transaksi
    </button>
</div>
@endif