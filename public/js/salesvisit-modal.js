// ==================== GLOBAL VARIABLES ====================
let createVisitCascade = null;
let editVisitCascade = null;
let currentEditData = null;

// Company & PIC Dropdown Variables
let companyDropdownTimeout = null;
let currentCompanies = [];
let picDropdownTimeout = null;
let currentPICs = [];
let selectedCompanyId = null;
let editSelectedCompanyId = null;

// ==================== ADD MODAL ====================
function openVisitModal() {
    const modal = document.getElementById('visitModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    initCreateVisitCascade();
    initCompanyDropdown();
    initPICDropdown();
    initializePICInput(); 
    
    setTimeout(() => {
        const firstInput = modal.querySelector('select[name="sales_id"]');
        if (firstInput && !firstInput.disabled) firstInput.focus();
    }, 300);
}

function initCreateVisitCascade() {
    if (createVisitCascade) {
        createVisitCascade.destroy();
    }

    // 🔥 DIPERBAIKI: Ubah baseUrl dari '/salesvisit' ke '/'
    createVisitCascade = new AddressCascade({
        provinceId: 'create-province',
        regencyId: 'create-regency',
        districtId: 'create-district',
        villageId: 'create-village',
        baseUrl: '/' // ← UBAH INI dari '/salesvisit' ke '/'
    });
}

function closeVisitModal() {
    const modal = document.getElementById('visitModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    
    if (createVisitCascade) {
        createVisitCascade.destroy();
        createVisitCascade = null;
    }
    
    const form = modal.querySelector('form');
    if (form) form.reset();
    
    // Reset dropdowns
    document.getElementById('create-regency').innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
    document.getElementById('create-district').innerHTML = '<option value="">Pilih Kecamatan</option>';
    document.getElementById('create-village').innerHTML = '<option value="">Pilih Kelurahan/Desa</option>';
    
    // Reset company & PIC
    document.getElementById('create-company-id').value = '';
    document.getElementById('create-company-search').value = '';
    document.getElementById('create-company-dropdown').classList.add('hidden');
    selectedCompanyId = null;
    
    initializePICInput();
    
    // Reset address state
    const content = document.getElementById('address-content');
    const icon = document.getElementById('address-toggle-icon');
    const statusText = document.getElementById('address-status');
    
    if (content) content.classList.add('hidden');
    if (icon) icon.style.transform = 'rotate(0deg)';
    if (statusText) {
        statusText.textContent = 'Belum diisi';
        statusText.classList.remove('text-green-600', 'font-medium');
        statusText.classList.add('text-gray-500');
    }
}

// ==================== COMPANY DROPDOWN (CREATE) ====================
async function loadCompanies(search = '') {
    try {
        const response = await fetch('/company/get-companies-dropdown');
        const data = await response.json();
        if (data.success) {
            currentCompanies = data.companies;
            updateCompanyDropdown(search);
        }
    } catch (error) {
        console.error('Error loading companies:', error);
    }
}

// Di function updateCompanyDropdown()
function updateCompanyDropdown(search = '') {
    const dropdown = document.getElementById('create-company-options');
    const searchTerm = search.toLowerCase();
    const filteredCompanies = currentCompanies.filter(company => 
        company.name.toLowerCase().includes(searchTerm)
    );
    
    dropdown.innerHTML = '';
    
    if (filteredCompanies.length === 0 && search.trim() !== '') {
        const addOption = document.createElement('div');
        addOption.className = 'px-3 py-2 text-sm text-green-600 hover:bg-green-50 cursor-pointer flex items-center gap-2';
        addOption.innerHTML = `<i class="fas fa-plus text-xs"></i><span>Tambah "${search}" sebagai company baru</span>`;
        addOption.onclick = () => {
            // ✅ Nama sudah akan diambil otomatis dari search input
            showAddCompanyModal();
        };
        dropdown.appendChild(addOption);
    } else {
        filteredCompanies.forEach(company => {
            const option = document.createElement('div');
            option.className = 'px-3 py-2 text-sm text-gray-700 hover:bg-blue-50 cursor-pointer';
            option.textContent = company.name;
            option.onclick = () => selectCompany(company.id, company.name);
            dropdown.appendChild(option);
        });
    }
    
    const dropdownContainer = document.getElementById('create-company-dropdown');
    if (filteredCompanies.length > 0 || search.trim() !== '') {
        dropdownContainer.classList.remove('hidden');
    } else {
        dropdownContainer.classList.add('hidden');
    }
}

function selectCompany(companyId, companyName) {
    console.log('✅ Company selected:', { companyId, companyName });
    
    document.getElementById('create-company-id').value = companyId;
    document.getElementById('create-company-search').value = companyName;
    document.getElementById('create-company-dropdown').classList.add('hidden');
    
    selectedCompanyId = companyId;
    
    enablePICInput();
    loadPICsByCompany(companyId);
}

function initCompanyDropdown() {
    const searchInput = document.getElementById('create-company-search');
    const dropdown = document.getElementById('create-company-dropdown');
    
    if (!searchInput || !dropdown) return;
    
    searchInput.addEventListener('focus', () => loadCompanies(searchInput.value));
    
    searchInput.addEventListener('input', (e) => {
        clearTimeout(companyDropdownTimeout);
        companyDropdownTimeout = setTimeout(() => updateCompanyDropdown(e.target.value), 300);
    });
    
    searchInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            const searchValue = searchInput.value.trim();
            if (searchValue) {
                const matchedCompany = currentCompanies.find(company => 
                    company.name.toLowerCase() === searchValue.toLowerCase()
                );
                if (!matchedCompany) {
                    document.querySelector('#addCompanyModal input[name="company_name"]').value = searchValue;
                    showAddCompanyModal();
                }
            }
        }
    });
    
    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
    
    loadCompanies();
}

// ==================== PIC DROPDOWN (CREATE) ====================
function initializePICInput() {
    const picInput = document.getElementById('create-pic-search');
    const picContainer = document.getElementById('pic-input-container');
    
    if (picContainer) {
        // Selalu tampilkan container
        picContainer.classList.remove('hidden');
        
        // Tapi disabled dulu
        if (picInput) {
            picInput.disabled = true;
            picInput.placeholder = 'Pilih company terlebih dahulu...';
            picInput.classList.add('bg-gray-100', 'cursor-not-allowed');
            picInput.classList.remove('bg-white');
        }
        
        // Reset values
        document.getElementById('create-pic-id').value = '';
        document.getElementById('create-pic-name-hidden').value = '';
        document.getElementById('create-pic-search').value = '';
    }
}

function enablePICInput() {
    const picInput = document.getElementById('create-pic-search');
    const picContainer = document.getElementById('pic-input-container');
    
    if (picContainer && picInput) {
        // Tampilkan dan enable
        picContainer.classList.remove('hidden');
        picInput.disabled = false;
        picInput.placeholder = 'Ketik atau pilih PIC...';
        picInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
        picInput.classList.add('bg-white');
        
        // Reset values (biar bisa input baru)
        document.getElementById('create-pic-id').value = '';
        document.getElementById('create-pic-name-hidden').value = '';
        document.getElementById('create-pic-search').value = '';
    }
}

async function loadPICsByCompany(companyId, search = '') {
    try {
        console.log('🔄 Loading PICs for company:', companyId);
        
        const response = await fetch(`/pics/by-company/${companyId}`);
        const data = await response.json();
        
        console.log('📋 PICs response:', data);
        
        if (data.success) {
            currentPICs = data.pics;
            updatePICDropdown(search);
        }
    } catch (error) {
        console.error('Error loading PICs:', error);
    }
}

function updatePICDropdown(search = '') {
    const dropdown = document.getElementById('create-pic-options');
    const searchTerm = search.toLowerCase();
    const filteredPICs = currentPICs.filter(pic => 
        pic.name.toLowerCase().includes(searchTerm)
    );
    
    dropdown.innerHTML = '';
    
    if (filteredPICs.length === 0 && search.trim() !== '') {
        const addOption = document.createElement('div');
        addOption.className = 'px-3 py-2 text-sm text-green-600 hover:bg-green-50 cursor-pointer flex items-center gap-2';
        addOption.innerHTML = `<i class="fas fa-plus text-xs"></i><span>Tambah "${search}" sebagai PIC baru</span>`;
        addOption.onclick = () => {
            // ✅ Nama sudah akan diambil otomatis dari search input
            showAddPICModal();
        };
        dropdown.appendChild(addOption);
    } else {
        filteredPICs.forEach(pic => {
            const option = document.createElement('div');
            option.className = 'px-3 py-2 text-sm text-gray-700 hover:bg-blue-50 cursor-pointer';
            
            let displayText = pic.name;
            if (pic.position) {
                displayText += ` - ${pic.position}`;
            }
            
            option.textContent = displayText;
            option.onclick = () => selectPIC(pic.id, pic.name);
            dropdown.appendChild(option);
        });
    }
    
    const dropdownContainer = document.getElementById('create-pic-dropdown');
    if (filteredPICs.length > 0 || search.trim() !== '') {
        dropdownContainer.classList.remove('hidden');
    } else {
        dropdownContainer.classList.add('hidden');
    }
}

function selectPIC(picId, picName) {
    console.log('✅ PIC selected:', { picId, picName });
    
    document.getElementById('create-pic-id').value = picId;
    document.getElementById('create-pic-name-hidden').value = picName;
    document.getElementById('create-pic-search').value = picName;
    document.getElementById('create-pic-dropdown').classList.add('hidden');
}

function initPICDropdown() {
    const searchInput = document.getElementById('create-pic-search');
    const dropdown = document.getElementById('create-pic-dropdown');
    
    if (!searchInput || !dropdown) return;
    
    searchInput.addEventListener('focus', () => {
        if (selectedCompanyId) {
            loadPICsByCompany(selectedCompanyId, searchInput.value);
        }
    });
    
    searchInput.addEventListener('input', (e) => {
        clearTimeout(picDropdownTimeout);
        picDropdownTimeout = setTimeout(() => updatePICDropdown(e.target.value), 300);
    });
    
    searchInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            const searchValue = searchInput.value.trim();
            if (searchValue && selectedCompanyId) {
                const matchedPIC = currentPICs.find(pic => 
                    pic.name.toLowerCase() === searchValue.toLowerCase()
                );
                if (!matchedPIC) {
                    document.querySelector('#addPICModal input[name="pic_name"]').value = searchValue;
                    showAddPICModal();
                }
            }
        }
    });
    
    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
}

// ==================== COMPANY & PIC MODALS ====================
function showAddCompanyModal() {
    // ✅ Ambil nama yang sudah diketik di search input
    const searchInput = document.getElementById('create-company-search');
    const searchValue = searchInput ? searchInput.value.trim() : '';
    
    // Reset form
    document.getElementById('addCompanyForm').reset();
    
    // ✅ AUTO-FILL: Set nama company dari search
    if (searchValue) {
        const companyNameInput = document.querySelector('#addCompanyModal input[name="company_name"]');
        if (companyNameInput) {
            companyNameInput.value = searchValue;
            console.log('✅ Auto-filled company name:', searchValue);
        }
    }
    
    // Show modal
    document.getElementById('addCompanyModal').classList.remove('hidden');
    
    setTimeout(() => {
        // Focus ke field berikutnya (company type) jika nama sudah terisi
        if (searchValue) {
            const companyTypeSelect = document.querySelector('#addCompanyModal select[name="company_type_id"]');
            if (companyTypeSelect) companyTypeSelect.focus();
        } else {
            document.querySelector('#addCompanyModal input[name="company_name"]').focus();
        }
    }, 300);
}

function showAddPICModal() {
    if (!selectedCompanyId && !editSelectedCompanyId) {
        alert('Silakan pilih company terlebih dahulu!');
        return;
    }
    
    const companyId = selectedCompanyId || editSelectedCompanyId;
    
    // ✅ Ambil nama PIC yang sudah diketik di search input
    const isEditMode = !document.getElementById('editVisitModal').classList.contains('hidden');
    const searchInput = isEditMode 
        ? document.getElementById('edit-pic-search')
        : document.getElementById('create-pic-search');
    const searchValue = searchInput ? searchInput.value.trim() : '';
    
    // Set company ID
    document.getElementById('pic-form-company-id').value = companyId;
    
    // Reset form
    document.getElementById('addPICForm').reset();
    
    // ✅ AUTO-FILL: Set nama PIC dari search
    if (searchValue) {
        const picNameInput = document.querySelector('#addPICModal input[name="pic_name"]');
        if (picNameInput) {
            picNameInput.value = searchValue;
            console.log('✅ Auto-filled PIC name:', searchValue);
        }
    }
    
    // Show modal
    document.getElementById('addPICModal').classList.remove('hidden');
    
    setTimeout(() => {
        // Focus ke field berikutnya (position) jika nama sudah terisi
        if (searchValue) {
            const positionInput = document.querySelector('#addPICModal input[name="pic_position"]');
            if (positionInput) positionInput.focus();
        } else {
            document.querySelector('#addPICModal input[name="pic_name"]').focus();
        }
    }, 300);
}
// ✅ TAMBAHKAN FUNCTION INI
function closeAddCompanyModal() {
    const modal = document.getElementById('addCompanyModal');
    if (modal) {
        modal.classList.add('hidden');
    }
    
    // Reset form
    const form = document.getElementById('addCompanyForm');
    if (form) {
        form.reset();
    }
    
    console.log('✅ Add Company modal closed');
}

// ✅ TAMBAHKAN FUNCTION INI JUGA
function closeAddPICModal() {
    const modal = document.getElementById('addPICModal');
    if (modal) {
        modal.classList.add('hidden');
    }
    
    // Reset form
    const form = document.getElementById('addPICForm');
    if (form) {
        form.reset();
    }
    
    console.log('✅ Add PIC modal closed');
}
// ==================== FORM HANDLERS (AJAX) ====================
async function handleCompanySubmit(e) {
    e.preventDefault(); 
    e.stopPropagation();
    
    console.log('🔥 Company form submitted - AJAX mode');
    
    const form = e.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
    
    const formData = new FormData(form);
    
    try {
        console.log('📤 Sending request to /company/store-company-ajax');
        console.log('📋 FormData:', Object.fromEntries(formData));
        
        const response = await fetch('/company/store-company-ajax', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest' // ✅ Tambahan header
            },
            body: formData
        });
        
        console.log('📥 Response status:', response.status);
        console.log('📥 Response headers:', response.headers.get('content-type'));
        
        // ✅ Cek apakah response adalah JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            console.error('❌ Response bukan JSON:', text.substring(0, 500));
            throw new Error('Server tidak mengembalikan JSON. Cek console untuk detail.');
        }
        
        const data = await response.json();
        console.log('📥 Response data:', data);
        
        if (response.status === 422) {
            let errorMessages = [];
            if (data.errors) {
                for (const field in data.errors) {
                    errorMessages.push(`${field}: ${data.errors[field].join(', ')}`);
                }
            }
            alert('Validasi gagal:\n' + errorMessages.join('\n'));
            return;
        }
        
        if (!response.ok) {
            throw new Error(data.message || `HTTP error! status: ${response.status}`);
        }
        
        if (data.success) {
            const newCompany = data.company;
            
            const isEditMode = !document.getElementById('editVisitModal').classList.contains('hidden');
            
            if (isEditMode) {
                selectEditCompany(newCompany.id, newCompany.name);
            } else {
                selectCompany(newCompany.id, newCompany.name);
            }
            
            await loadCompanies();
            closeAddCompanyModal();
            showNotification('Company berhasil ditambahkan!', 'success');
            
            console.log('✅ Company added successfully:', newCompany);
        } else {
            throw new Error(data.message || 'Gagal menambahkan company');
        }
    } catch (error) {
        console.error('❌ Error adding company:', error);
        showNotification('Gagal menambahkan company: ' + error.message, 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    }
}

async function handlePICSubmit(e) {
    e.preventDefault();
    e.stopPropagation();
    
    console.log('🔥 PIC form submitted - AJAX mode');
    
    const form = e.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
    
    const formData = new FormData(form);
    
    try {
        const response = await fetch('/pics/store-pic-ajax', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: formData
        });
        
        if (response.status === 422) {
            const errorData = await response.json();
            let errorMessages = [];
            if (errorData.errors) {
                for (const field in errorData.errors) {
                    errorMessages.push(`${field}: ${errorData.errors[field].join(', ')}`);
                }
            }
            alert('Validasi gagal:\n' + errorMessages.join('\n'));
            return;
        }
        
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        
        const data = await response.json();
        
        if (data.success) {
            const newPIC = data.pic;
            
            const isEditMode = !document.getElementById('editVisitModal').classList.contains('hidden');
            
            if (isEditMode) {
                selectEditPIC(newPIC.id, newPIC.name);
                if (editSelectedCompanyId) {
                    await loadEditPICsByCompany(editSelectedCompanyId);
                }
            } else {
                selectPIC(newPIC.id, newPIC.name);
                if (selectedCompanyId) {
                    await loadPICsByCompany(selectedCompanyId);
                }
            }
            
            closeAddPICModal();
            showNotification('PIC berhasil ditambahkan!', 'success');
            
            console.log('✅ PIC added successfully:', newPIC);
        } else {
            throw new Error(data.message || 'Gagal menambahkan PIC');
        }
    } catch (error) {
        console.error('❌ Error adding PIC:', error);
        showNotification('Gagal menambahkan PIC: ' + error.message, 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    }
}

// ==================== EDIT MODAL ====================
function openEditVisitModal(visitData) {
    console.log('🚀 Opening Edit Visit Modal with data:', visitData);

    if (!visitData || !visitData.id) {
        console.error('❌ Invalid visit data:', visitData);
        alert('Data kunjungan tidak valid');
        return;
    }

    try {
        const parsedData = {
            id: parseInt(visitData.id),
            salesId: parseInt(visitData.salesId),
            companyId: visitData.companyId ? parseInt(visitData.companyId) : null,
            picId: visitData.picId ? parseInt(visitData.picId) : null,
            picName: visitData.picName || '',
            companyName: visitData.companyName || '',
            provinceId: visitData.provinceId ? String(visitData.provinceId) : '',
            regencyId: visitData.regencyId ? String(visitData.regencyId) : '',
            districtId: visitData.districtId ? String(visitData.districtId) : '',
            villageId: visitData.villageId ? String(visitData.villageId) : '',
            address: visitData.address || '',
            visitDate: visitData.visitDate || '',
            purpose: visitData.purpose || '',
            followUp: parseInt(visitData.followUp) || 0
        };

        console.log('✅ Parsed visit data:', parsedData);

        const modal = document.getElementById('editVisitModal');
        if (!modal) {
            console.error('❌ Edit modal element not found');
            alert('Modal edit tidak ditemukan');
            return;
        }
        
        // 🔥 ROBUST FIX: Remove .hidden dan gunakan display inline-block sebagai fallback
        modal.classList.remove('hidden');
        
        // Force display dengan inline style sebagai fail-safe
        modal.style.display = 'flex';
        modal.style.opacity = '1';
        modal.style.visibility = 'visible';
        
        document.body.style.overflow = 'hidden';
        
        console.log('✅ Modal classes after remove hidden:', modal.classList);
        console.log('✅ Modal computed style:', window.getComputedStyle(modal).display);
        
        // Set form action
        const form = document.getElementById('editVisitForm');
        if (form) {
            form.action = `/salesvisit/${parsedData.id}`;
        }

        // Isi field dasar
        document.getElementById('editVisitId').value = parsedData.id;
        document.getElementById('editVisitDate').value = parsedData.visitDate;
        document.getElementById('editAddress').value = parsedData.address;
        document.getElementById('editPurpose').value = parsedData.purpose;
        
        // Set follow up
        if (parsedData.followUp === 1) {
            document.getElementById('editFollowUpYes').checked = true;
        } else {
            document.getElementById('editFollowUpNo').checked = true;
        }

        // Store data untuk nanti
        currentEditData = parsedData;

        // Load data dropdown (sales, provinces, dll)
        loadEditVisitData(parsedData.id);
        
    } catch (error) {
        console.error('❌ Error opening edit modal:', error);
        alert('Gagal membuka modal edit: ' + error.message);
    }
}

function closeEditVisitModal() {
    const modal = document.getElementById('editVisitModal');
    
    // 🔥 ROBUST CLOSE: Set display none dan add back .hidden class
    modal.classList.add('hidden');
    modal.style.display = 'none';
    modal.style.opacity = '0';
    modal.style.visibility = 'hidden';
    
    document.body.style.overflow = 'auto';

    if (editVisitCascade) {
        editVisitCascade.destroy();
        editVisitCascade = null;
    }

    currentEditData = null;
    editSelectedCompanyId = null;

    const form = document.getElementById('editVisitForm');
    if (form) form.reset();

    const salesSelect = document.getElementById('editSalesId');
    salesSelect.disabled = false;
    salesSelect.innerHTML = '<option value="">Pilih Sales</option>';

    // Reset dropdowns
    document.getElementById('edit-province').innerHTML = '<option value="">Pilih Provinsi</option>';
    document.getElementById('edit-regency').innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
    document.getElementById('edit-district').innerHTML = '<option value="">Pilih Kecamatan</option>';
    document.getElementById('edit-village').innerHTML = '<option value="">Pilih Kelurahan/Desa</option>';
    
    // Reset company & PIC
    document.getElementById('edit-company-id').value = '';
    document.getElementById('edit-company-search').value = '';
    document.getElementById('edit-company-dropdown').classList.add('hidden');
    
    initializeEditPICInput();
    
    // Reset address state
    const content = document.getElementById('edit-address-content');
    const icon = document.getElementById('edit-address-toggle-icon');
    const statusText = document.getElementById('edit-address-status');
    
    if (content) content.classList.add('hidden');
    if (icon) icon.style.transform = 'rotate(0deg)';
    if (statusText) {
        statusText.textContent = 'Belum diisi';
        statusText.classList.remove('text-green-600', 'font-medium');
        statusText.classList.add('text-gray-500');
    }
}

function loadEditVisitData(visitId) {
    console.log('🔥 Loading edit data for visit:', visitId);
    
    const salesSelect = document.getElementById('editSalesId');
    
    if (!salesSelect) {
        console.error('❌ Sales select not found!');
        return;
    }
    
    // Reset select options
    salesSelect.innerHTML = '<option value="">Loading sales...</option>';
    salesSelect.disabled = true;

    // Load sales data dari API
    fetch('/salesvisit/get-sales')
        .then(response => response.json())
        .then(salesData => {
            // Populate Sales dropdown
            salesSelect.innerHTML = '<option value="">Pilih Sales</option>';
            
            let salesList = salesData.users || salesData.data || salesData;
            if (Array.isArray(salesList)) {
                salesList.forEach(sales => {
                    const option = document.createElement('option');
                    option.value = sales.user_id;
                    option.textContent = `${sales.username} - ${sales.email}`;
                    if (sales.user_id == currentEditData.salesId) {
                        option.selected = true;
                        console.log('✅ Sales selected:', sales.user_id);
                    }
                    salesSelect.appendChild(option);
                });
            }
            salesSelect.disabled = false;
            
            // ✅ LANGSUNG INIT FORM (jangan tunggu province API)
            initializeEditForm();
        })
        .catch(error => {
            console.error('❌ Error loading sales:', error);
            salesSelect.innerHTML = '<option value="">Error loading sales</option>';
            salesSelect.disabled = false;
            initializeEditForm();
        });
}

function initializeEditForm() {
    console.log('🔄 Initializing edit form...');
    
    // Initialize company dropdown
    initEditCompanyDropdown();
    
    // Initialize PIC dropdown
    initEditPICDropdown();
    
    // Initialize address cascade
    initEditVisitCascade();
    
    // Set company data jika ada
    setTimeout(() => {
        if (currentEditData.companyId && currentEditData.companyName) {
            console.log('✅ Setting company:', currentEditData.companyId, currentEditData.companyName);
            selectEditCompany(currentEditData.companyId, currentEditData.companyName);
        }
        
        // Set PIC data jika ada
        if (currentEditData.picId && currentEditData.picName) {
            setTimeout(() => {
                console.log('✅ Setting PIC:', currentEditData.picId, currentEditData.picName);
                selectEditPIC(currentEditData.picId, currentEditData.picName);
            }, 1000);
        }
    }, 500);
}

// ==================== EDIT COMPANY DROPDOWN ====================
let editCompanyDropdownTimeout = null;
let editCurrentCompanies = [];

async function loadEditCompanies(search = '') {
    try {
        const response = await fetch('/company/get-companies-dropdown');
        const data = await response.json();
        if (data.success) {
            editCurrentCompanies = data.companies;
            updateEditCompanyDropdown(search);
        }
    } catch (error) {
        console.error('Error loading edit companies:', error);
    }
}

function updateEditCompanyDropdown(search = '') {
    const dropdown = document.getElementById('edit-company-options');
    const searchTerm = search.toLowerCase();
    const filteredCompanies = editCurrentCompanies.filter(company => 
        company.name.toLowerCase().includes(searchTerm)
    );
    
    dropdown.innerHTML = '';
    
    filteredCompanies.forEach(company => {
        const option = document.createElement('div');
        option.className = 'px-3 py-2 text-sm text-gray-700 hover:bg-blue-50 cursor-pointer';
        option.textContent = company.name;
        option.onclick = () => selectEditCompany(company.id, company.name);
        dropdown.appendChild(option);
    });
    
    const dropdownContainer = document.getElementById('edit-company-dropdown');
    if (filteredCompanies.length > 0) {
        dropdownContainer.classList.remove('hidden');
    } else {
        dropdownContainer.classList.add('hidden');
    }
}

function selectEditCompany(companyId, companyName) {
    console.log('✅ Edit company selected:', { companyId, companyName });
    
    document.getElementById('edit-company-id').value = companyId;
    document.getElementById('edit-company-search').value = companyName;
    document.getElementById('edit-company-dropdown').classList.add('hidden');
    
    editSelectedCompanyId = companyId;
    
    enableEditPICInput();
    loadEditPICsByCompany(companyId);
}

function initEditCompanyDropdown() {
    const searchInput = document.getElementById('edit-company-search');
    const dropdown = document.getElementById('edit-company-dropdown');
    
    if (!searchInput || !dropdown) return;
    
    searchInput.addEventListener('focus', () => loadEditCompanies(searchInput.value));
    
    searchInput.addEventListener('input', (e) => {
        clearTimeout(editCompanyDropdownTimeout);
        editCompanyDropdownTimeout = setTimeout(() => updateEditCompanyDropdown(e.target.value), 300);
    });
    
    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
    
    loadEditCompanies();
}

// ==================== EDIT PIC DROPDOWN ====================
let editPICDropdownTimeout = null;
let editCurrentPICs = [];

function initializeEditPICInput() {
    const picInput = document.getElementById('edit-pic-search');
    const picContainer = document.getElementById('edit-pic-input-container');
    
    if (picContainer) {
        // Selalu tampilkan container
        picContainer.classList.remove('hidden');
        
        // Tapi disabled dulu
        if (picInput) {
            picInput.disabled = true;
            picInput.placeholder = 'Pilih company terlebih dahulu...';
            picInput.classList.add('bg-gray-100', 'cursor-not-allowed');
            picInput.classList.remove('bg-white');
        }
        
        // Reset values
        document.getElementById('edit-pic-id').value = '';
        document.getElementById('edit-pic-name-hidden').value = '';
        document.getElementById('edit-pic-search').value = '';
    }
}

function enableEditPICInput() {
    const picInput = document.getElementById('edit-pic-search');
    const picContainer = document.getElementById('edit-pic-input-container');
    
    if (picContainer && picInput) {
        // Tampilkan dan enable
        picContainer.classList.remove('hidden');
        picInput.disabled = false;
        picInput.placeholder = 'Ketik atau pilih PIC...';
        picInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
        picInput.classList.add('bg-white');
        
        // Reset values
        document.getElementById('edit-pic-id').value = '';
        document.getElementById('edit-pic-name-hidden').value = '';
        document.getElementById('edit-pic-search').value = '';
    }
}

async function loadEditPICsByCompany(companyId, search = '') {
    try {
        console.log('🔄 Loading edit PICs for company:', companyId);
        
        const response = await fetch(`/pics/by-company/${companyId}`);
        const data = await response.json();
        
        console.log('📋 Edit PICs response:', data);
        
        if (data.success) {
            editCurrentPICs = data.pics;
            updateEditPICDropdown(search);
        }
    } catch (error) {
        console.error('Error loading edit PICs:', error);
    }
}

function updateEditPICDropdown(search = '') {
    const dropdown = document.getElementById('edit-pic-options');
    const searchTerm = search.toLowerCase();
    const filteredPICs = editCurrentPICs.filter(pic => 
        pic.name.toLowerCase().includes(searchTerm)
    );
    
    dropdown.innerHTML = '';
    
    filteredPICs.forEach(pic => {
        const option = document.createElement('div');
        option.className = 'px-3 py-2 text-sm text-gray-700 hover:bg-blue-50 cursor-pointer';
        
        let displayText = pic.name;
        if (pic.position) {
            displayText += ` - ${pic.position}`;
        }
        
        option.textContent = displayText;
        option.onclick = () => selectEditPIC(pic.id, pic.name);
        dropdown.appendChild(option);
    });
    
    const dropdownContainer = document.getElementById('edit-pic-dropdown');
    if (filteredPICs.length > 0) {
        dropdownContainer.classList.remove('hidden');
    } else {
        dropdownContainer.classList.add('hidden');
    }
}

function selectEditPIC(picId, picName) {
    console.log('✅ Edit PIC selected:', { picId, picName });
    
    document.getElementById('edit-pic-id').value = picId;
    document.getElementById('edit-pic-name-hidden').value = picName;
    document.getElementById('edit-pic-search').value = picName;
    document.getElementById('edit-pic-dropdown').classList.add('hidden');
}

function initEditPICDropdown() {
    const searchInput = document.getElementById('edit-pic-search');
    const dropdown = document.getElementById('edit-pic-dropdown');
    
    if (!searchInput || !dropdown) return;
    
    searchInput.addEventListener('focus', () => {
        if (editSelectedCompanyId) {
            loadEditPICsByCompany(editSelectedCompanyId, searchInput.value);
        }
    });
    
    searchInput.addEventListener('input', (e) => {
        clearTimeout(editPICDropdownTimeout);
        editPICDropdownTimeout = setTimeout(() => updateEditPICDropdown(e.target.value), 300);
    });
    
    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
}

// ==================== EDIT CASCADE ====================
function initEditVisitCascade() {
    console.log('🔄 Initializing edit cascade with data:', currentEditData);
    
    if (editVisitCascade) {
        editVisitCascade.destroy();
    }

    // 🔥 DIPERBAIKI: Ubah baseUrl dari '/salesvisit' ke '/'
    editVisitCascade = new AddressCascade({
        provinceId: 'edit-province',
        regencyId: 'edit-regency',
        districtId: 'edit-district',
        villageId: 'edit-village',
        baseUrl: '/' // ← UBAH INI dari '/salesvisit' ke '/'
    });

    // Load cascade data jika ada provinceId
    if (currentEditData.provinceId) {
        console.log('🔄 Loading regencies for province:', currentEditData.provinceId);
        
        const provinceSelect = document.getElementById('edit-province');
        provinceSelect.value = String(currentEditData.provinceId);
        
        const changeEvent = new Event('change', { bubbles: true });
        provinceSelect.dispatchEvent(changeEvent);
        
        // Load regency jika ada
        if (currentEditData.regencyId) {
            console.log('⏳ Waiting for regencies to load...');
            
            let regencyWaitCount = 0;
            const maxRegencyWait = 50;
            
            const waitForRegencies = setInterval(() => {
                const regencySelect = document.getElementById('edit-regency');
                regencyWaitCount++;
                
                if (regencySelect.options.length > 1) {
                    clearInterval(waitForRegencies);
                    console.log('✅ Regencies loaded, setting value:', currentEditData.regencyId);
                    
                    regencySelect.value = String(currentEditData.regencyId);
                    
                    if (regencySelect.value == currentEditData.regencyId) {
                        console.log('✅ Regency value set successfully');
                        regencySelect.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                    
                    // Load district jika ada
                    if (currentEditData.districtId) {
                        console.log('⏳ Waiting for districts to load...');
                        
                        let districtWaitCount = 0;
                        const maxDistrictWait = 50;
                        
                        const waitForDistricts = setInterval(() => {
                            const districtSelect = document.getElementById('edit-district');
                            districtWaitCount++;
                            
                            if (districtSelect.options.length > 1) {
                                clearInterval(waitForDistricts);
                                console.log('✅ Districts loaded, setting value:', currentEditData.districtId);
                                
                                districtSelect.value = String(currentEditData.districtId);
                                
                                if (districtSelect.value == currentEditData.districtId) {
                                    console.log('✅ District value set successfully');
                                    districtSelect.dispatchEvent(new Event('change', { bubbles: true }));
                                    
                                    // Load village jika ada
                                    if (currentEditData.villageId) {
                                        console.log('⏳ Waiting for villages to load...');
                                        
                                        let villageWaitCount = 0;
                                        const maxVillageWait = 50;
                                        
                                        const waitForVillages = setInterval(() => {
                                            const villageSelect = document.getElementById('edit-village');
                                            villageWaitCount++;
                                            
                                            if (villageSelect.options.length > 1) {
                                                clearInterval(waitForVillages);
                                                console.log('✅ Villages loaded, setting value:', currentEditData.villageId);
                                                
                                                villageSelect.value = String(currentEditData.villageId);
                                                
                                                if (villageSelect.value == currentEditData.villageId) {
                                                    console.log('✅ Village value set successfully');
                                                }
                                            }
                                            
                                            if (villageWaitCount >= maxVillageWait) {
                                                clearInterval(waitForVillages);
                                                console.warn('⚠️ Timeout waiting for villages');
                                            }
                                        }, 200);
                                    }
                                }
                            }
                            
                            if (districtWaitCount >= maxDistrictWait) {
                                clearInterval(waitForDistricts);
                                console.warn('⚠️ Timeout waiting for districts');
                            }
                        }, 200);
                    }
                }
                
                if (regencyWaitCount >= maxRegencyWait) {
                    clearInterval(waitForRegencies);
                    console.warn('⚠️ Timeout waiting for regencies');
                }
            }, 200);
        }
    }
}



// ==================== DELETE ====================
function deleteVisit(visitId, deleteRoute, csrfToken) {
    console.log('🗑️ Delete visit:', visitId);

    if (!confirm('Apakah Anda yakin ingin menghapus data kunjungan ini?')) {
        return;
    }

    // Gunakan route yang benar
    const correctRoute = `/salesvisit/${visitId}`;
    console.log('🚨 Delete route:', correctRoute);

    fetch(correctRoute, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('📨 Delete response status:', response.status);
        
        if (response.status === 404) {
            throw new Error('Endpoint tidak ditemukan (404)');
        }
        
        if (!response.ok) {
            return response.text().then(text => {
                console.error('❌ Delete error response:', text);
                throw new Error('Network response was not ok');
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('✅ Delete success:', data);
        if (data.success) {
            showNotification(data.message || 'Data berhasil dihapus', 'success');
            
            // Refresh halaman setelah 1 detik
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            throw new Error(data.message || 'Gagal menghapus data');
        }
    })
    .catch(error => {
        console.error('❌ Delete error:', error);
        showNotification('Gagal menghapus data: ' + error.message, 'error');
    });
}

// ==================== ADDRESS SECTION TOGGLE ====================
function toggleAddressSection() {
    const content = document.getElementById('address-content');
    const icon = document.getElementById('address-toggle-icon');
    
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
    } else {
        content.classList.add('hidden');
        icon.style.transform = 'rotate(0deg)';
    }
}

function checkAddressCompletion() {
    const province = document.getElementById('create-province').value;
    const address = document.getElementById('create-address').value.trim();
    const statusText = document.getElementById('address-status');
    
    if (province && address) {
        statusText.textContent = 'Sudah diisi';
        statusText.classList.remove('text-gray-500');
        statusText.classList.add('text-green-600', 'font-medium');
    } else {
        statusText.textContent = 'Belum diisi';
        statusText.classList.remove('text-green-600', 'font-medium');
        statusText.classList.add('text-gray-500');
    }
}

function closeAddressSectionOnEnter() {
    const province = document.getElementById('create-province').value;
    const address = document.getElementById('create-address').value.trim();
    const content = document.getElementById('address-content');
    const icon = document.getElementById('address-toggle-icon');
    
    if (province && address) {
        if (!content.classList.contains('hidden')) {
            content.classList.add('hidden');
            icon.style.transform = 'rotate(0deg)';
        }
    }
}

function toggleEditAddressSection() {
    const content = document.getElementById('edit-address-content');
    const icon = document.getElementById('edit-address-toggle-icon');
    
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
    } else {
        content.classList.add('hidden');
        icon.style.transform = 'rotate(0deg)';
    }
}

function checkEditAddressCompletion() {
    const province = document.getElementById('edit-province').value;
    const address = document.getElementById('editAddress').value.trim();
    const statusText = document.getElementById('edit-address-status');
    
    if (province && address) {
        statusText.textContent = 'Sudah diisi';
        statusText.classList.remove('text-gray-500');
        statusText.classList.add('text-green-600', 'font-medium');
    } else {
        statusText.textContent = 'Belum diisi';
        statusText.classList.remove('text-green-600', 'font-medium');
        statusText.classList.add('text-gray-500');
    }
}

function closeEditAddressSectionOnEnter() {
    const province = document.getElementById('edit-province').value;
    const address = document.getElementById('editAddress').value.trim();
    const content = document.getElementById('edit-address-content');
    const icon = document.getElementById('edit-address-toggle-icon');
    
    if (province && address) {
        if (!content.classList.contains('hidden')) {
            content.classList.add('hidden');
            icon.style.transform = 'rotate(0deg)';
        }
    }
}

// ==================== NOTIFICATION SYSTEM (Enhanced) ====================
function showNotification(message, type = 'info') {
    let notification = document.getElementById('global-notification');
    if (!notification) {
        notification = document.createElement('div');
        notification.id = 'global-notification';
        document.body.appendChild(notification);
    }
    
    const bgColor = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500'
    };
    
    const icon = {
        success: 'check',
        error: 'times',
        info: 'info'
    };
    
    notification.className = `fixed top-4 right-4 z-[9999] p-4 rounded-lg shadow-lg text-white transform transition-all duration-300 ${bgColor[type]}`;
    notification.innerHTML = `
        <div class="flex items-center gap-2">
            <i class="fas fa-${icon[type]}-circle"></i>
            <span>${message}</span>
        </div>
    `;
    
    notification.style.opacity = '0';
    notification.style.transform = 'translateX(100%)';
    
    setTimeout(() => {
        notification.style.opacity = '1';
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// ==================== EVENT LISTENERS ====================
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        if (!document.getElementById('addPICModal').classList.contains('hidden')) {
            closeAddPICModal();
        } else if (!document.getElementById('addCompanyModal').classList.contains('hidden')) {
            closeAddCompanyModal();
        } else if (!document.getElementById('visitModal').classList.contains('hidden')) {
            closeVisitModal();
        } else if (!document.getElementById('editVisitModal').classList.contains('hidden')) {
            closeEditVisitModal();
        }
    }
});

document.addEventListener('click', (e) => {
    if (e.target.id === 'visitModal') {
        closeVisitModal();
    }
    if (e.target.id === 'editVisitModal') {
        closeEditVisitModal();
    }
    if (e.target.id === 'addCompanyModal') {
        closeAddCompanyModal();
    }
    if (e.target.id === 'addPICModal') {
        closeAddPICModal();
    }
});

// ==================== INIT ON PAGE LOAD ====================
document.addEventListener('DOMContentLoaded', () => {
    console.log('✅ SalesVisit Modal JS Loaded');
    
    if (typeof AddressCascade === 'undefined') {
        console.error('❌ AddressCascade class not found! Make sure address-cascade.js is loaded.');
    } else {
        console.log('✅ AddressCascade class found');
    }
    
    // ✅ ATTACH FORM HANDLERS
    const companyForm = document.getElementById('addCompanyForm');
    if (companyForm) {
        // Remove existing listeners
        const newCompanyForm = companyForm.cloneNode(true);
        companyForm.parentNode.replaceChild(newCompanyForm, companyForm);
        
        // Add new listener
        newCompanyForm.addEventListener('submit', handleCompanySubmit);
        console.log('✅ Company form handler attached');
    }
    
    const picForm = document.getElementById('addPICForm');
    if (picForm) {
        // Remove existing listeners
        const newPicForm = picForm.cloneNode(true);
        picForm.parentNode.replaceChild(newPicForm, picForm);
        
        // Add new listener
        newPicForm.addEventListener('submit', handlePICSubmit);
        console.log('✅ PIC form handler attached');
    }
});