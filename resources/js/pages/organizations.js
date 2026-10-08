import { createDataTable } from '../components/dataTable.js';
import { getOrganizations, getOrganization, getOrganizationContacts, deleteOrganization } from '../api/organizations.js';
import { getNestedValue } from '../utils/getNestedValue.js';

// Definisi Kolom untuk Tabel Organization
const organizationColumns = [
    {
        key: 'no',
        label: 'No',
        render: (row, index) => index + 1
    },
    {
        key: 'name',
        label: 'Nama Perusahaan',
        sortable: true,
        render: (row) => row.name || '-'
    },
    {
        key: 'industry',
        label: 'Jenis',
        render: (row) => getNestedValue(row, 'industry.name', '-')
    },
    {
        key: 'tier',
        label: 'Tier',
        sortable: true,
        render: (row) => row.tier || '-'
    },
    {
        key: 'description',
        label: 'Deskripsi',
        render: (row) => row.description || '-'
    },
    {
        key: 'is_active',
        label: 'Status',
        sortable: true,
        render: (row) => (row.is_active ? 'Active' : 'Inactive')
    },
    {
        key: 'actions',
        label: 'Aksi'
    }
];

function buildTableComponent() {
    const baseTable = createDataTable({
        fetchData: getOrganizations,
        columns: organizationColumns,
        defaultSort: { field: 'created_at', dir: 'desc' },
        perPage: 10
    });

    return {
        ...baseTable,

        // Modal Detail Organization Handler
        async showDetail(companyId) {
            const modal = document.getElementById('companyDetailModal');
            if (modal) modal.style.display = 'flex';

            const picsContainer = document.getElementById('picsContainer');
            if (picsContainer) {
                picsContainer.innerHTML = `
                    <div style="text-align: center; padding: 1rem; color: #6b7280;">
                        <i class="fas fa-spinner fa-spin"></i> Loading...
                    </div>
                `;
            }

            try {
                const [companyData, picsData] = await Promise.all([
                    getOrganization(companyId),
                    getOrganizationContacts(companyId)
                ]);

                const c = companyData.company || companyData.data || companyData || {};
                const pics = Array.isArray(picsData.pics) ? picsData.pics : (Array.isArray(picsData.data) ? picsData.data : []);

                const elName = document.getElementById('detailCompanyName');
                const elType = document.getElementById('detailCompanyType');
                const elTier = document.getElementById('detailCompanyTier');
                const elDesc = document.getElementById('detailCompanyDescription');
                const statusBadge = document.getElementById('detailCompanyStatus');

                if (elName) elName.textContent = c.name || '-';
                if (elType) elType.textContent = c.industry?.name || '-';
                if (elTier) elTier.textContent = c.tier || '-';
                if (elDesc) elDesc.textContent = c.description || '-';

                if (statusBadge) {
                    statusBadge.textContent = c.is_active ? 'Active' : 'Inactive';
                    if (c.is_active) {
                        statusBadge.style.backgroundColor = '#d1fae5';
                        statusBadge.style.color = '#065f46';
                    } else {
                        statusBadge.style.backgroundColor = '#fee2e2';
                        statusBadge.style.color = '#991b1b';
                    }
                }

                const fields = {
                    detailProvince: c.province?.name,
                    detailRegency: c.regency?.name,
                    detailDistrict: c.district?.name,
                    detailVillage: c.village?.name,
                    detailFullAddress: c.address,
                    detailCompanyPhone: c.phone,
                    detailCompanyEmail: c.email
                };
                Object.entries(fields).forEach(([id, value]) => {
                    const el = document.getElementById(id);
                    if (el) el.textContent = value || '-';
                });

                const logoContainer = document.getElementById('detailLogoContainer');
                if (logoContainer) {
                    logoContainer.innerHTML = '<div style="width: 120px; height: 120px; background-color: #e5e7eb; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center;"><i class="fas fa-building" style="font-size: 2.5rem; color: #9ca3af;"></i></div>';
                }

                const picCount = document.getElementById('picCount');
                if (picsContainer) {
                    if (pics.length > 0) {
                        if (picCount) picCount.textContent = String(pics.length);
                        picsContainer.innerHTML = pics.map(pic => `
                            <div style="border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 0.5rem; background-color: #f9fafb; display: flex; gap: 0.5rem; align-items: flex-start;">
                                <div style="flex-shrink: 0; width: 2rem; height: 2rem; border-radius: 0.75rem; background: linear-gradient(135deg, #4f46e5, #7c3aed); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.875rem;">
                                    ${(pic.name || 'P').charAt(0).toUpperCase()}
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <p style="font-size: 0.8125rem; font-weight: 600; color: #111827; margin: 0 0 0.125rem 0;">${pic.name || '-'}</p>
                                    <p style="font-size: 0.75rem; color: #6b7280; margin: 0 0 0.25rem 0;">${pic.role || '-'}</p>
                                    <div style="display: flex; gap: 0.5rem; font-size: 0.7rem; flex-wrap: wrap;">
                                        <a href="tel:${pic.phone || ''}" style="color: #3b82f6; text-decoration: none;">${pic.phone || '-'}</a>
                                        <span style="color: #d1d5db;">•</span>
                                        <a href="mailto:${pic.email || ''}" style="color: #3b82f6; text-decoration: none;">${pic.email || '-'}</a>
                                    </div>
                                </div>
                            </div>
                        `).join('');
                    } else {
                        if (picCount) picCount.textContent = '0';
                        picsContainer.innerHTML = '<p style="text-align: center; color: #9ca3af; font-size: 0.875rem; margin: 0;">Belum ada PIC</p>';
                    }
                }
            } catch (error) {
                console.error('Error fetching organization detail:', error);
                const picsContainer = document.getElementById('picsContainer');
                if (picsContainer) {
                    picsContainer.innerHTML = '<div style="text-align: center; color: #dc2626; font-size: 0.875rem;">Gagal memuat data</div>';
                }
            }
        },

        async deleteCompanyItem(companyId, deleteRoute, csrfToken) {
            if (typeof window.deleteRecord === 'function') {
                window.deleteRecord(companyId, deleteRoute, csrfToken, () => {
                    this.refresh();
                });
            } else {
                try {
                    await deleteOrganization(companyId);
                    this.refresh();
                } catch (err) {
                    console.error('Error deleting organization:', err);
                }
            }
        }
    };
}

// Cara resmi Alpine.js: register komponen di event alpine:init.
// Script ini diload SETELAH Alpine CDN di layout utama, sehingga event selalu tertangkap.
document.addEventListener('alpine:init', () => {
    Alpine.data('organizationTable', buildTableComponent);
});

// Expose global helper untuk trigger detail dari luar scope Alpine (misal: onclick di PHP)
window.showCompanyDetail = (id) => {
    const rootEl = document.querySelector('[x-data="organizationTable"]');
    if (rootEl && window.Alpine) {
        const data = window.Alpine.$data(rootEl);
        if (data && typeof data.showDetail === 'function') {
            data.showDetail(id);
        }
    }
};
