// ==================== COMPANY MODALS JAVASCRIPT (FINAL FIX) ====================
// File: public/js/company-modals.js
// Dependencies: address-cascade.js (must be loaded first)

// Global variables
let picCounter = 0;
let editPicCounter = 0;
let currentEditCompanyId = null;

// Global cascade instances
window.addCompanyCascade = null;
window.editCompanyCascade = null;

// ==================== ADD COMPANY MODAL ====================

function openAddCompanyModal() {
    console.log('🚀 Opening Add Company Modal...');
    
    document.getElementById('addCompanyModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Initialize address cascade
    setTimeout(() => {
        initAddAddressCascade();
    }, 100);
}

function closeAddCompanyModal() {
    console.log('❌ Closing Add Company Modal...');
    
    const modal = document.getElementById('addCompanyModal');
    if (modal) modal.classList.add('hidden');

    const form = document.getElementById('addCompanyForm');
    if (form) form.reset();

    document.body.style.overflow = 'auto';
    
    // Reset PIC section
    const picContainer = document.getElementById('pic-fields-container');
    if (picContainer) picContainer.innerHTML = '';
    picCounter = 0;
    
    const content = document.getElementById('pic-content');
    const icon = document.getElementById('pic-toggle-icon');
    const statusText = document.getElementById('pic-status');
    
    if (content) content.classList.add('hidden');
    if (icon) icon.style.transform = 'rotate(0deg)';
    if (statusText) {
        statusText.textContent = 'Belum diisi';
        statusText.style.color = '#6b7280';
    }

    // Clear logo preview
    clearLogoPreview();

    // Destroy cascade instance
    if (window.addCompanyCascade) {
        window.addCompanyCascade.destroy();
        window.addCompanyCascade = null;
    }
}

function initAddAddressCascade() {
    console.log('🗺️ Initializing ADD cascade...');
    
    // Cek apakah element ada
    const provinceEl = document.getElementById('create-province');
    if (!provinceEl) {
        console.error('❌ Element create-province not found!');
        return;
    }
    
    console.log('✅ Cascade elements found:', {
        province: document.getElementById('create-province'),
        regency: document.getElementById('create-regency'),
        district: document.getElementById('create-district'),
        village: document.getElementById('create-village')
    });
    
    if (window.addCompanyCascade) {
        window.addCompanyCascade.destroy();
    }

    try {
        window.addCompanyCascade = new AddressCascade({
            provinceId: 'create-province',
            regencyId: 'create-regency',
            districtId: 'create-district',
            villageId: 'create-village'
        });
        console.log('✅ Add company cascade initialized successfully');
    } catch (error) {
        console.error('❌ Error initializing add company cascade:', error);
    }
}

// ==================== EDIT COMPANY MODAL ====================

async function openEditCompanyModal(companyId, companyName, companyTypeId, tier, description, status) {
    console.log('📝 Opening Edit Company Modal...', { companyId });

    const form = document.getElementById('editCompanyForm');
    form.action = `/company/${companyId}`;
    
    currentEditCompanyId = companyId;

    // Set basic fields
    document.getElementById('edit_company_id').value = companyId || '';
    document.getElementById('edit_company_name').value = companyName || '';
    document.getElementById('edit_description').value = description || '';
    document.getElementById('edit_status').value = status || 'active';

    // Set company type
    const typeSelect = document.getElementById('edit_company_type_id');
    for (let opt of typeSelect.options) {
        opt.selected = (opt.value == companyTypeId);
    }

    // Set tier
    const tierSelect = document.getElementById('edit_tier');
    for (let opt of tierSelect.options) {
        opt.selected = (opt.value.toLowerCase() === String(tier).toLowerCase());
    }

    // Show modal
    document.getElementById('editCompanyModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // ✅ Initialize cascade FIRST, then load data
    setTimeout(async () => {
        initEditAddressCascade();
        
        // Wait a bit for cascade to initialize
        await new Promise(resolve => setTimeout(resolve, 200));
        
        // Load full company data
        await loadEditCompanyData(companyId);
        
        // Load PICs
        await loadEditCompanyPICs(companyId);
    }, 100);
}

function closeEditCompanyModal() {
    console.log('❌ Closing Edit Company Modal...');
    
    const modal = document.getElementById('editCompanyModal');
    if (modal) modal.classList.add('hidden');

    document.body.style.overflow = 'auto';

    const form = document.getElementById('editCompanyForm');
    if (form) form.reset();
    
    // Reset PIC section
    const picContainer = document.getElementById('edit-pic-fields-container');
    if (picContainer) picContainer.innerHTML = '';
    editPicCounter = 0;
    currentEditCompanyId = null;
    
    const content = document.getElementById('edit-pic-content');
    const icon = document.getElementById('edit-pic-toggle-icon');
    const statusText = document.getElementById('edit-pic-status');
    
    if (content) content.classList.add('hidden');
    if (icon) icon.style.transform = 'rotate(0deg)';
    if (statusText) {
        statusText.textContent = 'Loading...';
        statusText.style.color = '#6b7280';
    }

    // Clear logo preview
    clearEditLogoPreview();

    // Destroy cascade
    if (window.editCompanyCascade) {
        window.editCompanyCascade.destroy();
        window.editCompanyCascade = null;
    }
}

function initEditAddressCascade() {
    console.log('🗺️ Initializing EDIT cascade...');
    
    if (window.editCompanyCascade) {
        window.editCompanyCascade.destroy();
    }

    // ✅ FIXED: URL cascade sekarang absolut di address-cascade.js
    window.editCompanyCascade = new AddressCascade({
        provinceId: 'edit-province',
        regencyId: 'edit-regency',
        districtId: 'edit-district',
        villageId: 'edit-village',
    });
    
    console.log('✅ Edit cascade initialized');
}

async function loadEditCompanyData(companyId) {
    console.log('📡 Loading company data for edit...');
    
    try {
        const response = await fetch(`/api/v1/organizations/${companyId}`);
        const data = await response.json();
        
        console.log('📦 Company data received:', data);
        
        // API REST mengembalikan { data: {...} }
        const c = data.data ?? data.company ?? data ?? {};
        
        // Populate contact fields (nama field sesuai skema DB)
        document.getElementById('edit_company_phone').value   = c.phone   ?? '';
        document.getElementById('edit_company_email').value   = c.email   ?? '';
        document.getElementById('edit_company_website').value = c.website ?? '';
        
        // Sosial media (opsional, field mungkin tidak ada di form setelah refactor)
        const linkedinEl   = document.getElementById('edit_company_linkedin');
        const instagramEl  = document.getElementById('edit_company_instagram');
        if (linkedinEl || instagramEl) {
            const socialProfiles = Array.isArray(c.social_profiles) ? c.social_profiles : [];
            const linkedin  = socialProfiles.find(s => s.platform === 'linkedin');
            const instagram = socialProfiles.find(s => s.platform === 'instagram');
            if (linkedinEl)  linkedinEl.value  = linkedin?.url  ?? '';
            if (instagramEl) instagramEl.value = instagram?.url ?? '';
        }
        
        // Populate address field
        const addressField = document.getElementById('edit-address');
        if (addressField) {
            addressField.value = c.address ?? '';
        }
        
        // Load address cascade
        if (window.editCompanyCascade && c.province_id) {
            console.log('🗺️ Loading cascade with values:', {
                province_id: c.province_id,
                regency_id:  c.regency_id,
                district_id: c.district_id,
                village_id:  c.village_id,
            });
            
            try {
                await window.editCompanyCascade.loadWithValues(
                    c.province_id,
                    c.regency_id,
                    c.district_id,
                    c.village_id,
                );
                console.log('✅ Address cascade loaded successfully');
            } catch (cascadeError) {
                console.error('❌ Cascade error:', cascadeError);
            }
        } else {
            console.warn('⚠️ No cascade instance or province_id');
        }
        
        console.log('✅ Company data loaded successfully');
    } catch (error) {
        console.error('❌ Error loading company data:', error);
    }
}

async function loadEditCompanyPICs(companyId) {
    console.log('👥 Loading PICs...');
    
    const container = document.getElementById('edit-pic-fields-container');
    const statusText = document.getElementById('edit-pic-status');
    
    statusText.textContent = 'Loading...';
    statusText.style.color = '#6b7280';
    
    try {
        const response = await fetch(`/company/${companyId}/pics`);
        const data = await response.json();
        
        console.log('📦 PICs data:', data);
        
        if (data.success && data.pics) {
            container.innerHTML = '';
            editPicCounter = 0;
            
            if (data.pics.length > 0) {
                data.pics.forEach(pic => {
                    editPicCounter++;
                    const picField = createEditPICField(editPicCounter, pic);
                    container.appendChild(picField);
                });
                
                statusText.textContent = `${data.pics.length} PIC`;
                statusText.style.color = '#059669';
                statusText.style.fontWeight = '500';
                
                // Auto expand
                const content = document.getElementById('edit-pic-content');
                const icon = document.getElementById('edit-pic-toggle-icon');
                content.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
                
                console.log('✅ PICs loaded successfully');
            } else {
                statusText.textContent = 'Belum ada PIC';
                statusText.style.color = '#6b7280';
            }
        }
    } catch (error) {
        console.error('❌ Error loading PICs:', error);
        statusText.textContent = 'Error loading PICs';
        statusText.style.color = '#ef4444';
    }
}

// ==================== PIC MANAGEMENT (ADD) ====================

function togglePICSection() {
    const content = document.getElementById('pic-content');
    const icon = document.getElementById('pic-toggle-icon');
    
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
    } else {
        content.classList.add('hidden');
        icon.style.transform = 'rotate(0deg)';
    }
}

function addPICField() {
    picCounter++;
    const container = document.getElementById('pic-fields-container');
    
    const picFieldGroup = document.createElement('div');
    picFieldGroup.id = `pic-group-${picCounter}`;
    picFieldGroup.style.cssText = 'background-color: white; border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 0.75rem;';
    
    picFieldGroup.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <span style="font-size: 0.75rem; font-weight: 600; color: #4f46e5;">PIC #${picCounter}</span>
            <button type="button" 
                    onclick="removePICField(${picCounter})" 
                    style="background-color: #ef4444; color: white; border: none; border-radius: 0.375rem; padding: 0.25rem 0.5rem; font-size: 0.625rem; cursor: pointer;">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem;">
            <div style="grid-column: span 2;">
                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">
                    Nama PIC <span style="color: #ef4444;">*</span>
                </label>
                <input type="text" 
                       name="pics[${picCounter - 1}][pic_name]" 
                       placeholder="Contoh: John Doe"
                       style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 0.5rem; font-size: 0.75rem;"
                       oninput="checkPICCompletion()"
                       required>
            </div>
            
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Position</label>
                <input type="text" 
                       name="pics[${picCounter - 1}][position]" 
                       placeholder="Contoh: Manager"
                       style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 0.5rem; font-size: 0.75rem;"
                       oninput="checkPICCompletion()">
            </div>
            
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Phone</label>
                <input type="text" 
                       name="pics[${picCounter - 1}][phone]" 
                       placeholder="Contoh: 08123456789"
                       style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 0.5rem; font-size: 0.75rem;"
                       oninput="checkPICCompletion()">
            </div>
            
            <div style="grid-column: span 2;">
                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Email</label>
                <input type="email" 
                       name="pics[${picCounter - 1}][email]" 
                       placeholder="Contoh: john@company.com"
                       style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 0.5rem; font-size: 0.75rem;"
                       oninput="checkPICCompletion()">
            </div>
        </div>
    `;
    
    container.appendChild(picFieldGroup);
    checkPICCompletion();
    
    // Auto-expand
    const content = document.getElementById('pic-content');
    const icon = document.getElementById('pic-toggle-icon');
    content.classList.remove('hidden');
    icon.style.transform = 'rotate(180deg)';
}

function removePICField(picIndex) {
    const field = document.getElementById(`pic-group-${picIndex}`);
    if (field) {
        field.remove();
        checkPICCompletion();
    }
}

function checkPICCompletion() {
    const container = document.getElementById('pic-fields-container');
    const picGroups = container.querySelectorAll('[id^="pic-group-"]');
    const statusText = document.getElementById('pic-status');
    
    if (picGroups.length === 0) {
        statusText.textContent = 'Belum diisi';
        statusText.style.color = '#6b7280';
        statusText.style.fontWeight = 'normal';
    } else {
        let filledCount = 0;
        picGroups.forEach(group => {
            const nameInput = group.querySelector('input[name*="[pic_name]"]');
            if (nameInput && nameInput.value.trim() !== '') {
                filledCount++;
            }
        });
        
        if (filledCount > 0) {
            statusText.textContent = `${filledCount} PIC ditambahkan`;
            statusText.style.color = '#059669';
            statusText.style.fontWeight = '500';
        } else {
            statusText.textContent = `${picGroups.length} PIC (belum diisi)`;
            statusText.style.color = '#f59e0b';
            statusText.style.fontWeight = '500';
        }
    }
}

// ==================== PIC MANAGEMENT (EDIT) ====================

function toggleEditPICSection() {
    const content = document.getElementById('edit-pic-content');
    const icon = document.getElementById('edit-pic-toggle-icon');
    
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
    } else {
        content.classList.add('hidden');
        icon.style.transform = 'rotate(0deg)';
    }
}

function createEditPICField(index, picData = null) {
    const picFieldGroup = document.createElement('div');
    picFieldGroup.id = `edit-pic-group-${index}`;
    picFieldGroup.style.cssText = 'background-color: white; border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 0.75rem;';
    
    const picName = picData?.pic_name || '';
    const position = picData?.position && picData.position !== '-' ? picData.position : '';
    const phone = picData?.phone && picData.phone !== '-' ? picData.phone : '';
    const email = picData?.email && picData.email !== '-' ? picData.email : '';
    
    picFieldGroup.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <span style="font-size: 0.75rem; font-weight: 600; color: #4f46e5;">PIC #${index}</span>
            <button type="button" 
                    onclick="removeEditPICField(${index})" 
                    style="background-color: #ef4444; color: white; border: none; border-radius: 0.375rem; padding: 0.25rem 0.5rem; font-size: 0.625rem; cursor: pointer;">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem;">
            <div style="grid-column: span 2;">
                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">
                    Nama PIC <span style="color: #ef4444;">*</span>
                </label>
                <input type="text" 
                       name="pics[${index - 1}][pic_name]" 
                       value="${picName}"
                       placeholder="Contoh: John Doe"
                       style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 0.5rem; font-size: 0.75rem;"
                       oninput="checkEditPICCompletion()"
                       required>
            </div>
            
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Position</label>
                <input type="text" 
                       name="pics[${index - 1}][position]" 
                       value="${position}"
                       placeholder="Contoh: Manager"
                       style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 0.5rem; font-size: 0.75rem;"
                       oninput="checkEditPICCompletion()">
            </div>
            
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Phone</label>
                <input type="text" 
                       name="pics[${index - 1}][phone]" 
                       value="${phone}"
                       placeholder="Contoh: 08123456789"
                       style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 0.5rem; font-size: 0.75rem;"
                       oninput="checkEditPICCompletion()">
            </div>
            
            <div style="grid-column: span 2;">
                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Email</label>
                <input type="email" 
                       name="pics[${index - 1}][email]" 
                       value="${email}"
                       placeholder="Contoh: john@company.com"
                       style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 0.5rem; font-size: 0.75rem;"
                       oninput="checkEditPICCompletion()">
            </div>
        </div>
    `;
    
    return picFieldGroup;
}

function addEditPICField() {
    editPicCounter++;
    const container = document.getElementById('edit-pic-fields-container');
    const picField = createEditPICField(editPicCounter);
    container.appendChild(picField);
    checkEditPICCompletion();
    
    // Auto-expand
    const content = document.getElementById('edit-pic-content');
    const icon = document.getElementById('edit-pic-toggle-icon');
    content.classList.remove('hidden');
    icon.style.transform = 'rotate(180deg)';
}

function removeEditPICField(picIndex) {
    const field = document.getElementById(`edit-pic-group-${picIndex}`);
    if (field) {
        field.remove();
        checkEditPICCompletion();
    }
}

function checkEditPICCompletion() {
    const container = document.getElementById('edit-pic-fields-container');
    const picGroups = container.querySelectorAll('[id^="edit-pic-group-"]');
    const statusText = document.getElementById('edit-pic-status');
    
    if (picGroups.length === 0) {
        statusText.textContent = 'Belum ada PIC';
        statusText.style.color = '#6b7280';
        statusText.style.fontWeight = 'normal';
    } else {
        let filledCount = 0;
        picGroups.forEach(group => {
            const nameInput = group.querySelector('input[name*="[pic_name]"]');
            if (nameInput && nameInput.value.trim() !== '') {
                filledCount++;
            }
        });
        
        if (filledCount > 0) {
            statusText.textContent = `${filledCount} PIC`;
            statusText.style.color = '#059669';
            statusText.style.fontWeight = '500';
        } else {
            statusText.textContent = `${picGroups.length} PIC (belum diisi)`;
            statusText.style.color = '#f59e0b';
            statusText.style.fontWeight = '500';
        }
    }
}

// ==================== LOGO MANAGEMENT ====================

function previewLogo(event) {
    const file = event?.target?.files?.[0];
    if (file) {
        if (file.size > 5 * 1024 * 1024) {
            alert('Ukuran file terlalu besar! Maksimal 5MB');
            const logoInput = document.getElementById('company_logo');
            if (logoInput) logoInput.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('logoPreview');
            if (preview) preview.src = e.target.result;
            const container = document.getElementById('logoPreviewContainer');
            if (container) container.style.display = 'block';
            const prompt = document.getElementById('logoUploadPrompt');
            if (prompt) prompt.style.display = 'none';
        };
        reader.readAsDataURL(file);
    }
}

function clearLogoPreview() {
    const logoInput = document.getElementById('company_logo');
    if (logoInput) logoInput.value = '';
    const container = document.getElementById('logoPreviewContainer');
    if (container) container.style.display = 'none';
    const prompt = document.getElementById('logoUploadPrompt');
    if (prompt) prompt.style.display = 'block';
}

function previewEditLogo(event) {
    const file = event?.target?.files?.[0];
    if (file) {
        if (file.size > 5 * 1024 * 1024) {
            alert('Ukuran file terlalu besar! Maksimal 5MB');
            const editLogoInput = document.getElementById('edit_company_logo');
            if (editLogoInput) editLogoInput.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('editLogoPreview');
            if (preview) preview.src = e.target.result;
            const container = document.getElementById('editLogoPreviewContainer');
            if (container) container.style.display = 'block';
            const prompt = document.getElementById('editLogoUploadPrompt');
            if (prompt) prompt.style.display = 'none';
        };
        reader.readAsDataURL(file);
    }
}

function clearEditLogoPreview() {
    const editLogoInput = document.getElementById('edit_company_logo');
    if (editLogoInput) editLogoInput.value = '';
    const container = document.getElementById('editLogoPreviewContainer');
    if (container) container.style.display = 'none';
    const prompt = document.getElementById('editLogoUploadPrompt');
    if (prompt) prompt.style.display = 'block';
}

// ==================== DRAG AND DROP SETUP ====================

document.addEventListener('DOMContentLoaded', function() {
    console.log('🎯 Company modals JS loaded');
    
    // Setup drag and drop for ADD modal
    const logoDropZone = document.getElementById('logoDropZone');
    if (logoDropZone) {
        setupDragDrop(logoDropZone, 'company_logo', previewLogo);
    }
    
    // Setup drag and drop for EDIT modal
    const editLogoDropZone = document.getElementById('editLogoDropZone');
    if (editLogoDropZone) {
        setupDragDrop(editLogoDropZone, 'edit_company_logo', previewEditLogo);
    }
});

function setupDragDrop(dropZone, inputId, previewFunction) {
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.style.borderColor = '#3b82f6';
        dropZone.style.backgroundColor = '#eff6ff';
    });

    dropZone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        dropZone.style.borderColor = '#d1d5db';
        dropZone.style.backgroundColor = '#fafafa';
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            document.getElementById(inputId).files = files;
            previewFunction({ target: { files } });
        }
        dropZone.style.borderColor = '#d1d5db';
        dropZone.style.backgroundColor = '#fafafa';
    });

    dropZone.addEventListener('click', () => {
        document.getElementById(inputId).click();
    });
}

// ==================== KEYBOARD SHORTCUTS ====================

document.addEventListener('keydown', function(e) {
    // ESC key closes modals
    if (e.key === 'Escape') {
        const addModal = document.getElementById('addCompanyModal');
        const editModal = document.getElementById('editCompanyModal');
        
        if (addModal && !addModal.classList.contains('hidden')) {
            closeAddCompanyModal();
        }
        if (editModal && !editModal.classList.contains('hidden')) {
            closeEditCompanyModal();
        }
    }
});

console.log('✅ company-modals.js loaded successfully (FINAL FIX)');