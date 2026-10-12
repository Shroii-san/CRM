import { createDataTable } from '../components/dataTable.js';
import {
    getOrganizations,
    getOrganization,
    getOrganizationPics,
    createOrganization,
    updateOrganization,
    deleteOrganization,
} from '../api/organizations.js';
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

// ─── Helper: sanitasi data form agar string kosong menjadi null ──────────────
function cleanFormData(rawData) {
    const data = {};
    for (const [key, value] of Object.entries(rawData)) {
        if (value === '' || value === undefined) {
            data[key] = null;
        } else {
            data[key] = value;
        }
    }
    return data;
}

// ─── Helper: kumpulkan data dari form add ────────────────────────────────────
function collectAddFormData() {
    return cleanFormData({
        name:        document.getElementById('add_name')?.value?.trim() ?? '',
        industry_id: document.getElementById('add_industry_id')?.value || null,
        tier:        document.getElementById('add_tier')?.value || null,
        is_active:   document.getElementById('add_is_active')?.value ?? '1',
        description: document.getElementById('add_description')?.value?.trim() || null,
        phone:       document.getElementById('add_phone')?.value?.trim() || null,
        email:       document.getElementById('add_email')?.value?.trim() || null,
        website:     document.getElementById('add_website')?.value?.trim() || null,
        province_id: document.getElementById('create-province')?.value || null,
        regency_id:  document.getElementById('create-regency')?.value || null,
        district_id: document.getElementById('create-district')?.value || null,
        village_id:  document.getElementById('create-village')?.value || null,
        address:     document.getElementById('create-address')?.value?.trim() || null,
    });
}

// ─── Helper: kumpulkan data dari form edit ────────────────────────────────────
function collectEditFormData() {
    return cleanFormData({
        name:        document.getElementById('edit_company_name')?.value?.trim() ?? '',
        industry_id: document.getElementById('edit_industry_id')?.value || null,
        tier:        document.getElementById('edit_tier')?.value || null,
        is_active:   document.getElementById('edit_is_active')?.value ?? '1',
        description: document.getElementById('edit_description')?.value?.trim() || null,
        phone:       document.getElementById('edit_company_phone')?.value?.trim() || null,
        email:       document.getElementById('edit_company_email')?.value?.trim() || null,
        website:     document.getElementById('edit_company_website')?.value?.trim() || null,
        province_id: document.getElementById('edit-province')?.value || null,
        regency_id:  document.getElementById('edit-regency')?.value || null,
        district_id: document.getElementById('edit-district')?.value || null,
        village_id:  document.getElementById('edit-village')?.value || null,
        address:     document.getElementById('edit-address')?.value?.trim() || null,
    });
}

// ─── Helper: isi form edit dengan data item ──────────────────────────────────
function populateEditForm(item) {
    const setVal = (id, val) => {
        const el = document.getElementById(id);
        if (el) el.value = val ?? '';
    };

    setVal('edit_company_id',   item.id);
    setVal('edit_company_name', item.name);
    setVal('edit_description',  item.description);
    setVal('edit_company_phone', item.phone);
    setVal('edit_company_email', item.email);
    setVal('edit_company_website', item.website);
    setVal('edit-address',      item.address);

    // Select: industry_id
    const industrySelect = document.getElementById('edit_industry_id');
    if (industrySelect) {
        for (const opt of industrySelect.options) {
            opt.selected = String(opt.value) === String(item.industry_id);
        }
    }

    // Select: tier
    const tierSelect = document.getElementById('edit_tier');
    if (tierSelect) {
        for (const opt of tierSelect.options) {
            opt.selected = opt.value === (item.tier ?? '');
        }
    }

    // Select: is_active
    const statusSelect = document.getElementById('edit_is_active');
    if (statusSelect) {
        for (const opt of statusSelect.options) {
            opt.selected = String(opt.value) === String(item.is_active ? '1' : '0');
        }
    }
}

// ─── Helper: ekstrak pesan error dari response API / validation ──────────────
function formatErrorMessage(err, defaultMsg = 'Terjadi kesalahan') {
    if (err?.payload?.errors && typeof err.payload.errors === 'object') {
        const errorList = Object.values(err.payload.errors).flat();
        if (errorList.length > 0) {
            return errorList.join('<br>');
        }
    }
    return err?.payload?.message || err?.message || defaultMsg;
}

// ─── Helper: tampilkan toast sederhana ───────────────────────────────────────
function showToast(message, type = 'success') {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: type,
            html: message,
            showConfirmButton: false,
            timer: 4500,
            timerProgressBar: true,
        });
    } else {
        alert(message.replace(/<br>/g, '\n'));
    }
}

function buildTableComponent() {
    const baseTable = createDataTable({
        fetchData: getOrganizations,
        columns: organizationColumns,
        defaultSort: { field: 'created_at', dir: 'desc' },
        perPage: 10
    });

    return {
        ...baseTable,

        // ── Detail Modal ────────────────────────────────────────────────────
        async showDetail(companyId) {
            const modal = document.getElementById('companyDetailModal');
            if (modal) {
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }

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
                    getOrganizationPics(companyId),
                ]);

                const c    = companyData.data ?? companyData ?? {};
                const pics = Array.isArray(picsData.pics)
                    ? picsData.pics
                    : (Array.isArray(picsData.data) ? picsData.data : []);

                const setEl = (id, val) => {
                    const el = document.getElementById(id);
                    if (el) el.textContent = val || '-';
                };

                setEl('detailCompanyName',        c.name);
                setEl('detailCompanyType',        c.industry?.name);
                setEl('detailCompanyTier',        c.tier);
                setEl('detailCompanyDescription', c.description);
                setEl('detailProvince',           c.province?.name);
                setEl('detailRegency',            c.regency?.name);
                setEl('detailDistrict',           c.district?.name);
                setEl('detailVillage',            c.village?.name);
                setEl('detailFullAddress',        c.address);
                setEl('detailCompanyPhone',       c.phone);
                setEl('detailCompanyEmail',       c.email);

                const statusBadge = document.getElementById('detailCompanyStatus');
                if (statusBadge) {
                    statusBadge.textContent = c.is_active ? 'Active' : 'Inactive';
                    statusBadge.style.backgroundColor = c.is_active ? '#d1fae5' : '#fee2e2';
                    statusBadge.style.color           = c.is_active ? '#065f46' : '#991b1b';
                }

                const websiteWrap = document.getElementById('detailCompanyWebsite');
                if (websiteWrap) {
                    websiteWrap.innerHTML = c.website
                        ? `<a href="${c.website}" target="_blank" class="text-blue-500 no-underline hover:underline">${c.website}</a>`
                        : '<span style="color:#111827">-</span>';
                }

                // Social profiles (dari API data.social_profiles[])
                const socialProfiles = Array.isArray(c.social_profiles) ? c.social_profiles : [];
                const linkedin  = socialProfiles.find(s => s.platform === 'linkedin');
                const instagram = socialProfiles.find(s => s.platform === 'instagram');

                const linkedinWrap = document.getElementById('detailCompanyLinkedin');
                if (linkedinWrap) {
                    linkedinWrap.innerHTML = linkedin?.url
                        ? `<a href="${linkedin.url}" target="_blank" class="text-blue-500 no-underline hover:underline">${linkedin.url}</a>`
                        : '<span style="color:#111827">-</span>';
                }

                const instagramWrap = document.getElementById('detailCompanyInstagram');
                if (instagramWrap) {
                    instagramWrap.innerHTML = instagram?.url
                        ? `<a href="${instagram.url}" target="_blank" class="text-blue-500 no-underline hover:underline">${instagram.url}</a>`
                        : '<span style="color:#111827">-</span>';
                }

                const logoContainer = document.getElementById('detailLogoContainer');
                if (logoContainer) {
                    logoContainer.innerHTML = '<div style="width:120px;height:120px;background-color:#e5e7eb;border-radius:0.75rem;display:flex;align-items:center;justify-content:center;"><i class="fas fa-building" style="font-size:2.5rem;color:#9ca3af;"></i></div>';
                }

                const picCount = document.getElementById('picCount');
                if (picsContainer) {
                    if (pics.length > 0) {
                        if (picCount) picCount.textContent = String(pics.length);
                        picsContainer.innerHTML = pics.map(pic => `
                            <div style="border:1px solid #e5e7eb;border-radius:0.75rem;padding:0.5rem;background-color:#f9fafb;display:flex;gap:0.5rem;align-items:flex-start;">
                                <div style="flex-shrink:0;width:2rem;height:2rem;border-radius:0.75rem;background:linear-gradient(135deg,#4f46e5,#7c3aed);display:flex;align-items:center;justify-content:center;color:white;font-weight:600;font-size:0.875rem;">
                                    ${(pic.name || 'P').charAt(0).toUpperCase()}
                                </div>
                                <div style="flex:1;min-width:0;">
                                    <p style="font-size:0.8125rem;font-weight:600;color:#111827;margin:0 0 0.125rem 0;">${pic.name || '-'}</p>
                                    <p style="font-size:0.75rem;color:#6b7280;margin:0 0 0.25rem 0;">${pic.role || '-'}</p>
                                    <div style="display:flex;gap:0.5rem;font-size:0.7rem;flex-wrap:wrap;">
                                        <a href="tel:${pic.phone || ''}" style="color:#3b82f6;text-decoration:none;">${pic.phone || '-'}</a>
                                        <span style="color:#d1d5db;">•</span>
                                        <a href="mailto:${pic.email || ''}" style="color:#3b82f6;text-decoration:none;">${pic.email || '-'}</a>
                                    </div>
                                </div>
                            </div>
                        `).join('');
                    } else {
                        if (picCount) picCount.textContent = '0';
                        picsContainer.innerHTML = '<p style="text-align:center;color:#9ca3af;font-size:0.875rem;margin:0;">Belum ada PIC</p>';
                    }
                }
            } catch (error) {
                console.error('Error fetching organization detail:', error);
                const picsEl = document.getElementById('picsContainer');
                if (picsEl) {
                    picsEl.innerHTML = '<div style="text-align:center;color:#dc2626;font-size:0.875rem;">Gagal memuat data</div>';
                }
            }
        },

        // ── Add Modal ───────────────────────────────────────────────────────
        openAddCompanyModal() {
            if (typeof window.openAddCompanyModal === 'function') {
                window.openAddCompanyModal();
            }
        },

        async submitAddCompanyModal() {
            try {
                const data = collectAddFormData();
                await createOrganization(data);
                showToast('Organisasi berhasil ditambahkan');
                if (typeof window.closeAddCompanyModal === 'function') {
                    window.closeAddCompanyModal();
                }
                this.refresh();
            } catch (err) {
                console.error('Error creating organization:', err, err?.payload?.errors || err?.payload);
                const msg = formatErrorMessage(err, 'Gagal menambahkan organisasi');
                showToast(msg, 'error');
            }
        },

        // ── Edit Modal ──────────────────────────────────────────────────────
        openEditCompanyModal(item) {
            populateEditForm(item);
            // Inisialisasi modal via company-modal.js (init cascade dsb.)
            if (typeof window.openEditCompanyModal === 'function') {
                // Panggil versi asli yang ada di company-modal.js untuk inisialisasi cascade & PICs
                // Tapi kita sudah populate field manual, jadi cukup tampilkan modal saja
            }
            const modal = document.getElementById('editCompanyModal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
            // Inisialisasi cascade edit dan load data address
            if (typeof window.initEditAddressCascade === 'function') {
                setTimeout(async () => {
                    window.initEditAddressCascade();
                    await new Promise(r => setTimeout(r, 200));
                    if (window.editCompanyCascade && item.province_id) {
                        try {
                            await window.editCompanyCascade.loadWithValues(
                                item.province_id,
                                item.regency_id,
                                item.district_id,
                                item.village_id,
                            );
                        } catch (e) {
                            console.error('Cascade error:', e);
                        }
                    }
                    // Load PICs
                    if (typeof window.loadEditCompanyPICs === 'function') {
                        await window.loadEditCompanyPICs(item.id);
                    }
                }, 100);
            }
        },

        async submitEditCompanyModal() {
            const idEl = document.getElementById('edit_company_id');
            const id   = idEl?.value;
            if (!id) {
                showToast('ID organisasi tidak ditemukan', 'error');
                return;
            }
            try {
                const data = collectEditFormData();
                await updateOrganization(id, data);
                showToast('Organisasi berhasil diperbarui');
                if (typeof window.closeEditCompanyModal === 'function') {
                    window.closeEditCompanyModal();
                }
                this.refresh();
            } catch (err) {
                console.error('Error updating organization:', err, err?.payload?.errors || err?.payload);
                const msg = formatErrorMessage(err, 'Gagal memperbarui organisasi');
                showToast(msg, 'error');
            }
        },

        // ── Delete ──────────────────────────────────────────────────────────
        async deleteCompanyItem(companyId) {
            if (typeof Swal !== 'undefined') {
                const result = await Swal.fire({
                    title: 'Hapus Organisasi?',
                    text: 'Data yang dihapus tidak dapat dikembalikan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                });
                if (!result.isConfirmed) return;
            }

            try {
                await deleteOrganization(companyId);
                showToast('Organisasi berhasil dihapus');
                this.refresh();
            } catch (err) {
                console.error('Error deleting organization:', err);
                showToast('Gagal menghapus organisasi', 'error');
            }
        },
    };
}

// Registrasi komponen Alpine organizationTable secara aman
function registerComponent() {
    if (window.Alpine && typeof window.Alpine.data === 'function') {
        window.Alpine.data('organizationTable', buildTableComponent);
    }
}

document.addEventListener('alpine:init', registerComponent);

if (window.Alpine) {
    registerComponent();
}

// ─── Global helpers ──────────────────────────────────────────────────────────

// Expose showDetail ke luar scope Alpine (misal: onclick di PHP)
window.showCompanyDetail = (id) => {
    const rootEl = document.querySelector('[x-data="organizationTable"]');
    if (rootEl && window.Alpine) {
        const data = window.Alpine.$data(rootEl);
        if (data && typeof data.showDetail === 'function') {
            data.showDetail(id);
        }
    }
};

// Expose submitEditCompanyModal ke luar scope Alpine (dipanggil dari edit.blade.php footer button)
window.submitEditCompanyModal = () => {
    const rootEl = document.querySelector('[x-data="organizationTable"]');
    if (rootEl && window.Alpine) {
        const data = window.Alpine.$data(rootEl);
        if (data && typeof data.submitEditCompanyModal === 'function') {
            data.submitEditCompanyModal();
        }
    }
};

// Expose submitAddCompanyModal ke luar scope Alpine (dipanggil dari action.blade.php)
window.submitAddCompanyModal = () => {
    const rootEl = document.querySelector('[x-data="organizationTable"]');
    if (rootEl && window.Alpine) {
        const data = window.Alpine.$data(rootEl);
        if (data && typeof data.submitAddCompanyModal === 'function') {
            data.submitAddCompanyModal();
        }
    }
};
