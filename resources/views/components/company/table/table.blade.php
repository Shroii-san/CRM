@props(['companies', 'currentMenuId'])

<div class="fade-in" style="margin: 0; padding: 0;">
    <!-- Table ONLY -->
    <div class="overflow-x-auto" style="margin: 0; padding: 0;">
        <table id="companyTable" class="w-full" style="margin: 0; border-collapse: collapse;">
            <thead style="background-color: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                <tr>
                    <th
                        style="padding: 0.5rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; white-space: nowrap;">
                        No</th>
                    <th
                        style="padding: 0.5rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; white-space: nowrap;">
                        Nama Perusahaan</th>
                    <th
                        style="padding: 0.5rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; white-space: nowrap;">
                        Jenis</th>
                    <th
                        style="padding: 0.5rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; white-space: nowrap;">
                        Tier</th>
                    <th
                        style="padding: 0.5rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; white-space: nowrap;">
                        Deskripsi</th>
                    <th
                        style="padding: 0.5rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; white-space: nowrap;">
                        Status</th>
                    <th
                        style="padding: 0.5rem 0.75rem; text-align: right; font-size: 0.7rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; white-space: nowrap;">
                        Aksi</th>
                </tr>
            </thead>
            <tbody id="organizationTableBody" class="divide-y divide-gray-200"></tbody>
        </table>
    </div>
</div>

{{-- Company Detail Modal - ROUNDED KONSISTEN --}}
<div id="companyDetailModal"
    style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div
        style="background-color: white; border-radius: 1.5rem; width: 95%; max-width: 700px; max-height: 90vh; display: flex; flex-direction: column; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); overflow: hidden;">

        {{-- Modal Header - FIXED --}}
        <div
            style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; background: linear-gradient(to right, #4f46e5, #7c3aed); flex-shrink: 0;">
            <h3 style="font-size: 1.25rem; font-weight: 600; color: white; margin: 0;">Detail Perusahaan</h3>
            <button onclick="closeCompanyDetailModal()"
                style="color: white; background: transparent; border: none; font-size: 1.5rem; cursor: pointer; padding: 0;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Modal Body - SCROLLABLE WITH BORDER RADIUS AT BOTTOM --}}
        <div
            style="overflow-y: auto; flex: 1; padding: 1.5rem; display: flex; flex-direction: column; gap: 1.5rem; border-radius: 0 0 1.5rem 1.5rem;">

            {{-- Logo --}}
            <div style="text-align: center;">
                <div id="detailLogoContainer"
                    style="display: flex; justify-content: center; align-items: center; min-height: 120px;">
                    <p style="color: #9ca3af; font-size: 0.875rem;">Loading...</p>
                </div>
            </div>

            {{-- Info Grid --}}
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                <div>
                    <label
                        style="display: block; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; margin-bottom: 0.375rem;">Nama</label>
                    <p id="detailCompanyName"
                        style="font-size: 0.9375rem; font-weight: 500; color: #111827; margin: 0;">-</p>
                </div>
                <div>
                    <label
                        style="display: block; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; margin-bottom: 0.375rem;">Jenis</label>
                    <p id="detailCompanyType" style="font-size: 0.9375rem; color: #111827; margin: 0;">-</p>
                </div>
                <div>
                    <label
                        style="display: block; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; margin-bottom: 0.375rem;">Tier</label>
                    <p id="detailCompanyTier" style="font-size: 0.9375rem; color: #111827; margin: 0;">-</p>
                </div>
                <div>
                    <label
                        style="display: block; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; margin-bottom: 0.375rem;">Status</label>
                    <span id="detailCompanyStatus"
                        style="display: inline-flex; align-items: center; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500;">-</span>
                </div>
            </div>

            {{-- Deskripsi --}}
            <div>
                <label
                    style="display: block; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; margin-bottom: 0.375rem;">Deskripsi</label>
                <p id="detailCompanyDescription"
                    style="font-size: 0.9375rem; color: #111827; margin: 0; line-height: 1.5;">-</p>
            </div>

            {{-- Address --}}
            <div style="border-top: 1px solid #e5e7eb; padding-top: 1rem;">
                <h4 style="font-size: 0.875rem; font-weight: 600; color: #111827; margin: 0 0 0.75rem 0;">Lokasi</h4>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                    <div>
                        <label
                            style="font-size: 0.75rem; font-weight: 600; color: #6b7280; margin-bottom: 0.25rem;">Provinsi</label>
                        <p id="detailProvince" style="font-size: 0.875rem; color: #111827; margin: 0;">-</p>
                    </div>
                    <div>
                        <label
                            style="font-size: 0.75rem; font-weight: 600; color: #6b7280; margin-bottom: 0.25rem;">Kabupaten</label>
                        <p id="detailRegency" style="font-size: 0.875rem; color: #111827; margin: 0;">-</p>
                    </div>
                    <div>
                        <label
                            style="font-size: 0.75rem; font-weight: 600; color: #6b7280; margin-bottom: 0.25rem;">Kecamatan</label>
                        <p id="detailDistrict" style="font-size: 0.875rem; color: #111827; margin: 0;">-</p>
                    </div>
                    <div>
                        <label
                            style="font-size: 0.75rem; font-weight: 600; color: #6b7280; margin-bottom: 0.25rem;">Kelurahan</label>
                        <p id="detailVillage" style="font-size: 0.875rem; color: #111827; margin: 0;">-</p>
                    </div>
                </div>
                <div style="margin-top: 0.75rem;">
                    <label style="font-size: 0.75rem; font-weight: 600; color: #6b7280; margin-bottom: 0.25rem;">Alamat
                        Lengkap</label>
                    <p id="detailFullAddress" style="font-size: 0.875rem; color: #111827; margin: 0; line-height: 1.5;">
                        -</p>
                </div>
            </div>

            {{-- Contact Info --}}
            <div style="border-top: 1px solid #e5e7eb; padding-top: 1rem;">
                <h4 style="font-size: 0.875rem; font-weight: 600; color: #111827; margin: 0 0 0.75rem 0;">Kontak & Media
                </h4>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                    <div>
                        <label
                            style="font-size: 0.75rem; font-weight: 600; color: #6b7280; margin-bottom: 0.25rem;">Telepon</label>
                        <p id="detailCompanyPhone" style="font-size: 0.875rem; color: #111827; margin: 0;">-</p>
                    </div>
                    <div>
                        <label
                            style="font-size: 0.75rem; font-weight: 600; color: #6b7280; margin-bottom: 0.25rem;">Email</label>
                        <p id="detailCompanyEmail" style="font-size: 0.875rem; color: #111827; margin: 0;">-</p>
                    </div>
                    <div>
                        <label
                            style="font-size: 0.75rem; font-weight: 600; color: #6b7280; margin-bottom: 0.25rem;">Website</label>
                        <p id="detailCompanyWebsite" style="font-size: 0.875rem; margin: 0;"><a
                                id="detailCompanyWebsiteLink" href="#" target="_blank"
                                style="color: #3b82f6; text-decoration: none;">-</a></p>
                    </div>
                    <div>
                        <label
                            style="font-size: 0.75rem; font-weight: 600; color: #6b7280; margin-bottom: 0.25rem;">LinkedIn</label>
                        <p id="detailCompanyLinkedin" style="font-size: 0.875rem; margin: 0;"><a
                                id="detailCompanyLinkedinLink" href="#" target="_blank"
                                style="color: #3b82f6; text-decoration: none;">-</a></p>
                    </div>
                    <div style="grid-column: 1 / -1;">
                        <label
                            style="font-size: 0.75rem; font-weight: 600; color: #6b7280; margin-bottom: 0.25rem;">Instagram</label>
                        <p id="detailCompanyInstagram" style="font-size: 0.875rem; margin: 0;"><a
                                id="detailCompanyInstagramLink" href="#" target="_blank"
                                style="color: #3b82f6; text-decoration: none;">-</a></p>
                    </div>
                </div>
            </div>

            {{-- PICs - COMPACT & ROUNDED --}}
            <div style="border-top: 1px solid #e5e7eb; padding-top: 1rem;">
                <div
                    style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                    <h4 style="font-size: 0.875rem; font-weight: 600; color: #111827; margin: 0;">PIC</h4>
                    <span id="picCount"
                        style="font-size: 0.75rem; color: #ffffff; background-color: #4f46e5; padding: 0.25rem 0.625rem; border-radius: 9999px; font-weight: 600;">0</span>
                </div>
                <div id="picsContainer"
                    style="display: flex; flex-direction: column; gap: 0.5rem; max-height: 300px; overflow-y: auto;">
                    {{-- PICs will be loaded here --}}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function showCompanyDetail(companyId) {
        const modal = document.getElementById('companyDetailModal');
        modal.style.display = 'flex';

        document.getElementById('picsContainer').innerHTML = `
        <div style="text-align: center; padding: 1rem; color: #6b7280;">
            <i class="fas fa-spinner fa-spin"></i> Loading...
        </div>
    `;

        Promise.all([
            fetch(`/organization/${companyId}`),
            fetch(`/organization/${companyId}/pics`)
        ])
            .then(async ([companyResponse, picsResponse]) => {
                const companyData = await companyResponse.json();
                const picsData = await picsResponse.json();
                const c = companyData.company || {};
                const pics = Array.isArray(picsData.pics) ? picsData.pics : [];

                document.getElementById('detailCompanyName').textContent = c.name || '-';
                document.getElementById('detailCompanyType').textContent = c.industry?.name || '-';
                document.getElementById('detailCompanyTier').textContent = c.tier || '-';
                document.getElementById('detailCompanyDescription').textContent = c.description || '-';

                const statusBadge = document.getElementById('detailCompanyStatus');
                const statusText = c.is_active ? 'Active' : 'Inactive';
                statusBadge.textContent = statusText;
                if (c.is_active) {
                    statusBadge.style.backgroundColor = '#d1fae5';
                    statusBadge.style.color = '#065f46';
                } else {
                    statusBadge.style.backgroundColor = '#fee2e2';
                    statusBadge.style.color = '#991b1b';
                }

                document.getElementById('detailProvince').textContent = c.province?.name || '-';
                document.getElementById('detailRegency').textContent = c.regency?.name || '-';
                document.getElementById('detailDistrict').textContent = c.district?.name || '-';
                document.getElementById('detailVillage').textContent = c.village?.name || '-';
                document.getElementById('detailFullAddress').textContent = c.address || '-';
                document.getElementById('detailCompanyPhone').textContent = c.phone || '-';
                document.getElementById('detailCompanyEmail').textContent = c.email || '-';

                const websiteLink = document.getElementById('detailCompanyWebsiteLink');
                const websiteWrap = document.getElementById('detailCompanyWebsite');
                if (c.website) {
                    websiteLink.href = c.website;
                    websiteLink.textContent = c.website;
                    websiteWrap.innerHTML = '';
                } else {
                    websiteWrap.innerHTML = '<span style="color: #111827;">-</span>';
                }

                const linkedinLink = document.getElementById('detailCompanyLinkedinLink');
                const linkedinWrap = document.getElementById('detailCompanyLinkedin');
                linkedinLink.href = '#';
                linkedinLink.textContent = '-';
                linkedinWrap.innerHTML = '<span style="color: #111827;">-</span>';

                const instagramLink = document.getElementById('detailCompanyInstagramLink');
                const instagramWrap = document.getElementById('detailCompanyInstagram');
                instagramLink.href = '#';
                instagramLink.textContent = '-';
                instagramWrap.innerHTML = '<span style="color: #111827;">-</span>';

                const logoContainer = document.getElementById('detailLogoContainer');
                logoContainer.innerHTML = '<div style="width: 120px; height: 120px; background-color: #e5e7eb; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center;"><i class="fas fa-building" style="font-size: 2.5rem; color: #9ca3af;"></i></div>';

                const picsContainer = document.getElementById('picsContainer');
                const picCount = document.getElementById('picCount');

                if (pics.length > 0) {
                    picCount.textContent = String(pics.length);
                    picsContainer.innerHTML = pics.map(pic => `
                    <div style="border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 0.5rem; background-color: #f9fafb; display: flex; gap: 0.5rem; align-items: flex-start;">
                        <div style="flex-shrink: 0; width: 2rem; height: 2rem; border-radius: 0.75rem; background: linear-gradient(135deg, #4f46e5, #7c3aed); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.875rem;">
                            ${(pic.name || 'P').charAt(0).toUpperCase()}
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <p style="font-size: 0.8125rem; font-weight: 600; color: #111827; margin: 0 0 0.125rem 0;">${pic.name || '-'}</p>
                            <p style="font-size: 0.75rem; color: #6b7280; margin: 0 0 0.25rem 0;">${pic.role || '-'}</p>
                            <div style="display: flex; gap: 0.5rem; font-size: 0.7rem; flex-wrap: wrap;">
                                <a href="tel:${pic.phone || ''}" style="color: #3b82f6; text-decoration: none; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${pic.phone || '-'}</a>
                                <span style="color: #d1d5db;">•</span>
                                <a href="mailto:${pic.email || ''}" style="color: #3b82f6; text-decoration: none; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${pic.email || '-'}</a>
                            </div>
                        </div>
                    </div>
                `).join('');
                } else {
                    picCount.textContent = '0';
                    picsContainer.innerHTML = '<p style="text-align: center; color: #9ca3af; font-size: 0.875rem; margin: 0;">Belum ada PIC</p>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('picsContainer').innerHTML = '<div style="text-align: center; color: #dc2626; font-size: 0.875rem;">Gagal memuat data</div>';
            });
    }
</script>

<style>
    @media (max-width: 1024px) {
        #companyTable {
            font-size: 0.8125rem;
        }

        #companyTable th,
        #companyTable td {
            padding: 0.4rem 0.65rem;
        }
    }

    @media (max-width: 768px) {
        #companyTable {
            font-size: 0.75rem;
        }

        #companyTable th,
        #companyTable td {
            padding: 0.375rem 0.5rem;
        }

        #companyDetailModal>div {
            width: 95%;
        }
    }
</style>