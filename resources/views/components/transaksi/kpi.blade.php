<div style="padding: 0.75rem 1rem; border-bottom: 1px solid #e5e7eb; background-color: #f9fafb;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 0.75rem;">
        <!-- Total Transaksi -->
        <div style="background-color: white; padding: 0.75rem; border-radius: 0.3rem; border-left: 3px solid #3b82f6; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 2.5rem; height: 2.5rem; background-color: #dbeafe; border-radius: 0.25rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="fas fa-file-invoice" style="font-size: 1rem; color: #2563eb;"></i>
                </div>
                <div style="min-width: 0;">
                    <p style="font-size: 0.65rem; color: #6b7280; font-weight: 500; margin: 0; text-transform: uppercase;">Total</p>
                    <p style="font-size: 1.5rem; font-weight: 700; color: #111827; margin: 0;">{{ count($transaksi) }}</p>
                </div>
            </div>
        </div>

        <!-- Deals -->
        <div style="background-color: white; padding: 0.75rem; border-radius: 0.3rem; border-left: 3px solid #22c55e; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 2.5rem; height: 2.5rem; background-color: #dcfce7; border-radius: 0.25rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="fas fa-check-circle" style="font-size: 1rem; color: #16a34a;"></i>
                </div>
                <div style="min-width: 0;">
                    <p style="font-size: 0.65rem; color: #6b7280; font-weight: 500; margin: 0; text-transform: uppercase;">Deals</p>
                    <p style="font-size: 1.5rem; font-weight: 700; color: #16a34a; margin: 0;">{{ count($transaksi->where('status', 'Deals')) }}</p>
                </div>
            </div>
        </div>

        <!-- Fails -->
        <div style="background-color: white; padding: 0.75rem; border-radius: 0.3rem; border-left: 3px solid #ef4444; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 2.5rem; height: 2.5rem; background-color: #fee2e2; border-radius: 0.25rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="fas fa-times-circle" style="font-size: 1rem; color: #dc2626;"></i>
                </div>
                <div style="min-width: 0;">
                    <p style="font-size: 0.65rem; color: #6b7280; font-weight: 500; margin: 0; text-transform: uppercase;">Fails</p>
                    <p style="font-size: 1.5rem; font-weight: 700; color: #dc2626; margin: 0;">{{ count($transaksi->where('status', 'Fails')) }}</p>
                </div>
            </div>
        </div>

        <!-- Total Nilai -->
        <div style="background-color: white; padding: 0.75rem; border-radius: 0.3rem; border-left: 3px solid #8b5cf6; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 2.5rem; height: 2.5rem; background-color: #ede9fe; border-radius: 0.25rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="fas fa-money-bill-wave" style="font-size: 1rem; color: #7c3aed;"></i>
                </div>
                <div style="min-width: 0;">
                    <p style="font-size: 0.65rem; color: #6b7280; font-weight: 500; margin: 0; text-transform: uppercase;">Total Nilai</p>
                    <p style="font-size: 1rem; font-weight: 700; color: #7c3aed; margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Rp{{ number_format($transaksi->sum('nilai_proyek'), 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>