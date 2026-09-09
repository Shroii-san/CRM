// ===================================
// CUSTOMER MANAGEMENT SYSTEM
// Consistent with SalesVisit Modal Style
// ===================================

// Global Variables
let customersData = [];
let selectedCustomers = [];
let customerAddressCascade = null;
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

// ==================== CUSTOMER MODAL (ADD/EDIT) ====================
function openCustomerModal(mode = 'add', customerId = null) {
    console.log('🚀 Opening Customer Modal:', { mode, customerId });
    
    const modal = document.getElementById('customerModal');
    const title = document.getElementById('modalTitle');
    const titleIcon = title.previousElementSibling?.querySelector('i');
    
    if (!modal) {
        console.error('❌ Customer modal element not found');
        return;
    }
    
    // Show modal
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
    
    // Reset form
    const form = document.getElementById('customerForm');
    if (form) form.reset();
    
    if (mode === 'add') {
        console.log('✅ Opening Add Customer Modal');
        title.textContent = 'Tambah Customer';
        if (titleIcon) titleIcon.className = 'fas fa-user-plus text-white text-lg';
        
        document.getElementById('customerId').value = '';
        document.getElementById('formMethod').value = 'POST';
        
        // Set default type
        document.querySelector('input[name="customerType"][value="Personal"]').checked = true;
        toggleCompanyFields();
        
        // Initialize address cascade for add
        initCustomerAddressCascade();
        
    } else if (mode === 'edit' && customerId) {
        console.log('✅ Opening Edit Customer Modal for ID:', customerId);
        title.textContent = 'Edit Customer';
        if (titleIcon) titleIcon.className = 'fas fa-user-edit text-white text-lg';
        
        document.getElementById('formMethod').value = 'PUT';
        
        // Initialize cascade first, then load data
        initCustomerAddressCascade();
        loadCustomerData(customerId);
    }
    
    // Focus first input after modal animation
    setTimeout(() => {
        const firstInput = modal.querySelector('input[type="text"]');
        if (firstInput) firstInput.focus();
    }, 300);
}

function closeCustomerModal() {
    console.log('🚪 Closing Customer Modal');
    
    const modal = document.getElementById('customerModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = 'auto';
    
    // Destroy address cascade instance
    if (customerAddressCascade) {
        customerAddressCascade.destroy();
        customerAddressCascade = null;
    }
    
    // Reset form
    const form = document.getElementById('customerForm');
    if (form) form.reset();
    
    // Reset dropdowns
    document.getElementById('create-regency').innerHTML = '<option value="">-- Pilih Kabupaten/Kota --</option>';
    document.getElementById('create-district').innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
    document.getElementById('create-village').innerHTML = '<option value="">-- Pilih Kelurahan/Desa --</option>';
}

function initCustomerAddressCascade() {
    console.log('🔄 Initializing Customer Address Cascade');
    
    if (customerAddressCascade) {
        customerAddressCascade.destroy();
    }
    
    customerAddressCascade = new AddressCascade({
        provinceId: 'create-province',
        regencyId: 'create-regency',
        districtId: 'create-district',
        villageId: 'create-village',
        baseUrl: '/customers'
    });
    
    console.log('✅ Address cascade initialized');
}

function toggleCompanyFields() {
    const type = document.querySelector('input[name="customerType"]:checked')?.value;
    const companyFields = document.getElementById('companyFields');
    const nameLabel = document.getElementById('nameLabel');
    
    if (type === 'Company') {
        companyFields.classList.remove('hidden');
        nameLabel.textContent = 'Nama Perusahaan';
    } else {
        companyFields.classList.add('hidden');
        nameLabel.textContent = 'Nama Lengkap';
    }
}

// ==================== IMPORT MODAL ====================
function openImportModal() {
    console.log('📥 Opening Import Modal');
    
    const modal = document.getElementById('importModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeImportModal() {
    console.log('🚪 Closing Import Modal');
    
    const modal = document.getElementById('importModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = 'auto';
    
    // Reset form
    const form = document.getElementById('importForm');
    if (form) form.reset();
}

// ==================== LOAD & DISPLAY CUSTOMERS ====================
async function loadCustomers() {
    console.log('📋 Loading customers...');
    
    try {
        const response = await fetch('/customers/list', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        if (!response.ok) throw new Error('Failed to load customers');
        
        const data = await response.json();
        customersData = Array.isArray(data) ? data : (data.customers || data.data || []);
        
        console.log(`✅ Loaded ${customersData.length} customers`);
        
        displayCustomers(customersData);
        updateKPIs();
    } catch (error) {
        console.error('❌ Error loading customers:', error);
        showNotification('Gagal memuat data customer', 'error');
    }
}

function displayCustomers(customers) {
    const tbody = document.getElementById('customerTableBody');
    
    if (!tbody) {
        console.error('❌ Table body element not found');
        return;
    }
    
    if (!customers || customers.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="px-3 py-6 text-center text-gray-500">
                    <i class="fas fa-users text-3xl mb-2"></i>
                    <p class="text-xs">Belum ada data customer</p>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = customers.map((customer, index) => `
        <tr class="hover:bg-gray-50 transition-colors border-b">
            <td class="px-3 py-2 whitespace-nowrap">
                <input type="checkbox" 
                    class="customer-checkbox rounded border-gray-300 w-3.5 h-3.5" 
                    value="${customer.id}"
                    onchange="handleCheckboxChange()">
            </td>
            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900">
                ${index + 1}
            </td>
            <td class="px-3 py-2 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-blue-600 text-xs"></i>
                    </div>
                    <div class="ml-3">
                        <div class="text-xs font-medium text-gray-900">${customer.name}</div>
                        <div class="text-[10px] text-gray-500">${customer.customer_id || ''}</div>
                    </div>
                </div>
            </td>
            <td class="px-3 py-2 whitespace-nowrap">
                <span class="px-2 py-0.5 text-[10px] font-medium rounded-full ${
                    customer.type === 'Company' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'
                }">
                    ${customer.type}
                </span>
            </td>
            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-500">
                <i class="fas fa-envelope mr-1 text-[10px]"></i>${customer.email}
            </td>
            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-500">
                <i class="fas fa-phone mr-1 text-[10px]"></i>${customer.phone}
            </td>
            <td class="px-3 py-2 whitespace-nowrap">
                ${getStatusBadge(customer.status)}
            </td>
            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-500">
                <i class="fas fa-user-tie mr-1 text-[10px]"></i>${customer.pic}
            </td>
            <td class="px-3 py-2 whitespace-nowrap text-right text-xs font-medium">
                <button onclick="viewCustomer(${customer.id})" 
                    class="text-blue-600 hover:text-blue-900 p-1.5 transition" title="View">
                    <i class="fas fa-eye text-xs"></i>
                </button>
                <button onclick="editCustomer(${customer.id})" 
                    class="text-green-600 hover:text-green-900 p-1.5 transition" title="Edit">
                    <i class="fas fa-edit text-xs"></i>
                </button>
                <button onclick="deleteCustomer(${customer.id}, '${customer.name}')" 
                    class="text-red-600 hover:text-red-900 p-1.5 transition" title="Delete">
                    <i class="fas fa-trash text-xs"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

function getStatusBadge(status) {
    const badges = {
        'Lead': 'bg-yellow-100 text-yellow-800',
        'Prospect': 'bg-blue-100 text-blue-800',
        'Active': 'bg-green-100 text-green-800',
        'Inactive': 'bg-gray-100 text-gray-800'
    };
    
    return `<span class="px-2 py-0.5 text-[10px] font-medium rounded-full ${badges[status] || 'bg-gray-100 text-gray-800'}">${status}</span>`;
}

// ==================== LOAD CUSTOMER DATA FOR EDIT ====================
async function loadCustomerData(id) {
    console.log('📥 Loading customer data for ID:', id);
    
    try {
        const response = await fetch(`/customers/${id}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        if (!response.ok) throw new Error('Failed to load customer');
        
        const result = await response.json();
        const customer = result.customer || result.data || result;
        
        console.log('✅ Customer data loaded:', customer);
        
        // Fill basic fields
        document.getElementById('customerId').value = customer.id;
        document.getElementById('customerName').value = customer.name || '';
        document.getElementById('customerEmail').value = customer.email || '';
        document.getElementById('customerPhone').value = customer.phone || '';
        document.getElementById('customerAddress').value = customer.address || '';
        document.getElementById('customerStatus').value = customer.status || 'Lead';
        document.getElementById('customerSource').value = customer.source || 'Website';
        document.getElementById('customerPIC').value = customer.pic || '';
        document.getElementById('customerNotes').value = customer.notes || '';
        
        // Set customer type
        const typeRadio = document.querySelector(`input[name="customerType"][value="${customer.type}"]`);
        if (typeRadio) {
            typeRadio.checked = true;
            toggleCompanyFields();
        }
        
        // Fill company fields if company type
        if (customer.type === 'Company') {
            document.getElementById('contactPersonName').value = customer.contact_person_name || '';
            document.getElementById('contactPersonEmail').value = customer.contact_person_email || '';
            document.getElementById('contactPersonPhone').value = customer.contact_person_phone || '';
        }
        
        // Load address cascade
        if (customer.province_id) {
            console.log('🔄 Loading address cascade for province:', customer.province_id);
            
            const provinceSelect = document.getElementById('create-province');
            provinceSelect.value = String(customer.province_id);
            
            // Trigger province change
            provinceSelect.dispatchEvent(new Event('change', { bubbles: true }));
            
            // Wait and set regency
            if (customer.regency_id) {
                setTimeout(() => {
                    const regencySelect = document.getElementById('create-regency');
                    regencySelect.value = String(customer.regency_id);
                    regencySelect.dispatchEvent(new Event('change', { bubbles: true }));
                    
                    // Wait and set district
                    if (customer.district_id) {
                        setTimeout(() => {
                            const districtSelect = document.getElementById('create-district');
                            districtSelect.value = String(customer.district_id);
                            districtSelect.dispatchEvent(new Event('change', { bubbles: true }));
                            
                            // Wait and set village
                            if (customer.village_id) {
                                setTimeout(() => {
                                    const villageSelect = document.getElementById('create-village');
                                    villageSelect.value = String(customer.village_id);
                                }, 300);
                            }
                        }, 300);
                    }
                }, 300);
            }
        }
        
    } catch (error) {
        console.error('❌ Error loading customer data:', error);
        showNotification('Gagal memuat data customer', 'error');
    }
}

// ==================== CRUD OPERATIONS ====================
function editCustomer(id) {
    console.log('✏️ Edit customer:', id);
    openCustomerModal('edit', id);
}

async function deleteCustomer(id, name) {
    console.log('🗑️ Delete customer:', { id, name });
    
    if (!confirm(`Apakah Anda yakin ingin menghapus customer "${name}"?`)) {
        return;
    }
    
    try {
        const response = await fetch(`/customers/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || 'Network response was not ok');
        }
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message || 'Customer berhasil dihapus', 'success');
            
            // Reload after 1 second
            setTimeout(() => {
                loadCustomers();
            }, 1000);
        } else {
            throw new Error(data.message || 'Gagal menghapus customer');
        }
    } catch (error) {
        console.error('❌ Error deleting customer:', error);
        showNotification('Gagal menghapus customer: ' + error.message, 'error');
    }
}

// ==================== FORM SUBMIT HANDLERS ====================
async function handleCustomerFormSubmit(e) {
    e.preventDefault();
    
    console.log('💾 Submitting customer form...');
    
    const form = e.target;
    const customerId = document.getElementById('customerId').value;
    const method = document.getElementById('formMethod').value;
    const formData = new FormData(form);
    
    // Add customer type
    const type = document.querySelector('input[name="customerType"]:checked')?.value || 'Personal';
    formData.append('type', type);
    
    // Convert to JSON
    const data = {};
    formData.forEach((value, key) => {
        if (key !== 'customerType') {
            data[key] = value;
        }
    });
    
    // Show loading
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
    
    try {
        const url = customerId ? `/customers/${customerId}` : '/customers';
        const fetchMethod = method === 'PUT' ? 'PUT' : 'POST';
        
        const response = await fetch(url, {
            method: fetchMethod,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (response.ok && result.success) {
            showNotification(result.message || 'Customer berhasil disimpan', 'success');
            closeCustomerModal();
            
            setTimeout(() => {
                loadCustomers();
            }, 500);
        } else {
            showNotification(result.message || 'Gagal menyimpan customer', 'error');
        }
    } catch (error) {
        console.error('❌ Error saving customer:', error);
        showNotification('Gagal menyimpan customer: ' + error.message, 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
}

async function handleImportFormSubmit(e) {
    e.preventDefault();
    
    console.log('📥 Submitting import form...');
    
    const form = e.target;
    const formData = new FormData(form);
    
    // Show loading
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengupload...';
    
    try {
        const response = await fetch('/customers/import', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        });
        
        const result = await response.json();
        
        if (response.ok && result.success) {
            showNotification(result.message || 'Import berhasil', 'success');
            closeImportModal();
            
            setTimeout(() => {
                loadCustomers();
            }, 500);
        } else {
            showNotification(result.message || 'Gagal import data', 'error');
        }
    } catch (error) {
        console.error('❌ Error importing:', error);
        showNotification('Gagal import data: ' + error.message, 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
}

// ==================== VIEW CUSTOMER DETAIL ====================
async function viewCustomer(id) {
    console.log('👁️ View customer:', id);
    
    try {
        const response = await fetch(`/customers/${id}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        if (!response.ok) throw new Error('Failed to load customer');
        
        const result = await response.json();
        const customer = result.customer || result.data || result;
        
        showCustomerDetail(customer);
    } catch (error) {
        console.error('❌ Error viewing customer:', error);
        showNotification('Gagal memuat detail customer', 'error');
    }
}

function showCustomerDetail(customer) {
    const typeIcon = customer.type === 'Company' ? 'fa-building' : 'fa-user';
    
    const html = `
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" onclick="if(event.target === this) this.remove()">
            <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden animate-fadeIn">
                <!-- Header -->
                <div class="px-6 py-5 border-b border-gray-200" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #3b82f6 100%);">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                <i class="fas ${typeIcon} text-white text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-white">${customer.name}</h3>
                                <p class="text-indigo-100 text-sm">${customer.customer_id || 'No ID'}</p>
                            </div>
                        </div>
                        <button onclick="this.closest('.fixed').remove()" 
                            class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2 transition-colors">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Content -->
                <div class="px-6 py-5 overflow-y-auto max-h-[calc(90vh-180px)] space-y-5">
                    <!-- Status & Type -->
                    <div class="flex gap-3">
                        ${getStatusBadge(customer.status)}
                        <span class="px-3 py-1 text-xs font-medium rounded-full ${
                            customer.type === 'Company' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'
                        }">
                            <i class="fas ${typeIcon} mr-1"></i>${customer.type}
                        </span>
                    </div>
                    
                    <!-- Contact Info -->
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase mb-3 flex items-center">
                            <i class="fas fa-id-card mr-2"></i>Informasi Kontak
                        </h4>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg">
                                <div class="w-9 h-9 bg-blue-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-envelope text-white text-sm"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs text-gray-600">Email</p>
                                    <p class="font-medium text-gray-900 truncate">${customer.email}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg">
                                <div class="w-9 h-9 bg-green-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-phone text-white text-sm"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs text-gray-600">Telepon</p>
                                    <p class="font-medium text-gray-900">${customer.phone}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    ${customer.address ? `
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase mb-2 flex items-center">
                            <i class="fas fa-map-marker-alt mr-2"></i>Alamat
                        </h4>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-700">${customer.address}</p>
                        </div>
                    </div>
                    ` : ''}
                    
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase mb-2 flex items-center">
                            <i class="fas fa-user-tie mr-2"></i>Person In Charge
                        </h4>
                        <div class="flex items-center gap-3 p-3 bg-purple-50 rounded-lg">
                            <div class="w-9 h-9 bg-purple-500 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-user text-white text-sm"></i>
                            </div>
                            <p class="font-medium text-gray-900">${customer.pic}</p>
                        </div>
                    </div>
                    
                    ${customer.notes ? `
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase mb-2 flex items-center">
                            <i class="fas fa-sticky-note mr-2"></i>Catatan
                        </h4>
                        <div class="p-3 bg-yellow-50 rounded-lg border-l-4 border-yellow-400">
                            <p class="text-sm text-gray-700">${customer.notes}</p>
                        </div>
                    </div>
                    ` : ''}
                </div>
                
                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                    <button onclick="this.closest('.fixed').remove()" 
                        class="px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors">
                        <i class="fas fa-times mr-1"></i>Tutup
                    </button>
                    <button onclick="this.closest('.fixed').remove(); editCustomer(${customer.id})" 
                        class="px-5 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors shadow-lg shadow-blue-500/30">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', html);
}

// ==================== BULK ACTIONS ====================
function handleSelectAll(e) {
    const checkboxes = document.querySelectorAll('.customer-checkbox');
    checkboxes.forEach(cb => cb.checked = e.target.checked);
    handleCheckboxChange();
}

function handleCheckboxChange() {
    const checkboxes = document.querySelectorAll('.customer-checkbox:checked');
    selectedCustomers = Array.from(checkboxes).map(cb => cb.value);
    
    const bulkActions = document.getElementById('bulkActions');
    if (bulkActions) {
        bulkActions.classList.toggle('hidden', selectedCustomers.length === 0);
    }
}

// ==================== FILTER FUNCTIONS ====================
function filterCustomers() {
    const search = document.getElementById('searchInput')?.value.toLowerCase() || '';
    const type = document.getElementById('filterType')?.value || '';
    const status = document.getElementById('filterStatus')?.value || '';
    
    const filtered = customersData.filter(customer => {
        const matchSearch = !search || 
            customer.name.toLowerCase().includes(search) ||
            customer.email.toLowerCase().includes(search) ||
            customer.phone.toLowerCase().includes(search);
        
        const matchType = !type || customer.type === type;
        const matchStatus = !status || customer.status === status;
        
        return matchSearch && matchType && matchStatus;
    });
    
    displayCustomers(filtered);
}

function resetFilters() {
    document.getElementById('searchInput').value = '';
    if (document.getElementById('filterType')) document.getElementById('filterType').value = '';
    if (document.getElementById('filterStatus')) document.getElementById('filterStatus').value = '';
    displayCustomers(customersData);
}

// ==================== KPI UPDATE ====================
function updateKPIs() {
    const total = customersData.length;
    const active = customersData.filter(c => c.status === 'Active').length;
    const leads = customersData.filter(c => c.status === 'Lead').length;
    const prospects = customersData.filter(c => c.status === 'Prospect').length;
    
    const kpiElements = {
        'totalCustomers': total,
        'activeCustomers': active,
        'leadCustomers': leads,
        'prospectCustomers': prospects
    };
    
    Object.entries(kpiElements).forEach(([id, value]) => {
        const element = document.getElementById(id);
        if (element) element.textContent = value;
    });
}

// ==================== NOTIFICATION SYSTEM ====================
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
        warning: 'bg-yellow-500',
        info: 'bg-blue-500'
    };
    
    const icon = {
        success: 'fa-check-circle',
        error: 'fa-times-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };
    
    notification.className = `fixed top-4 right-4 z-[1000] p-4 rounded-lg shadow-lg text-white transform transition-all duration-300 ${bgColor[type]}`;
    notification.innerHTML = `
        <div class="flex items-center gap-2">
            <i class="fas ${icon[type]}"></i>
            <span>${message}</span>
        </div>
    `;
    
    // Show notification
    notification.style.opacity = '0';
    setTimeout(() => {
        notification.style.opacity = '1';
    }, 100);
    
    // Hide after 3 seconds
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// ==================== EVENT LISTENERS ====================
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Customer Management System Loaded');
    
    // Check AddressCascade availability
    if (typeof AddressCascade === 'undefined') {
        console.error('❌ AddressCascade class not found! Make sure address-cascade.js is loaded.');
    } else {
        console.log('✅ AddressCascade class found');
    }
    
    // Load initial data
    loadCustomers();
    
    // Customer Form Submit
    const customerForm = document.getElementById('customerForm');
    if (customerForm) {
        customerForm.addEventListener('submit', handleCustomerFormSubmit);
    }
    
    // Import Form Submit
    const importForm = document.getElementById('importForm');
    if (importForm) {
        importForm.addEventListener('submit', handleImportFormSubmit);
    }
    
    // Modal Buttons - Customer Modal
    const openAddModalBtn = document.getElementById('openAddModalBtn');
    if (openAddModalBtn) {
        openAddModalBtn.addEventListener('click', () => openCustomerModal('add'));
    }
    
    const closeModalBtn = document.getElementById('closeModalBtn');
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', closeCustomerModal);
    }
    
    const cancelModalBtn = document.getElementById('cancelModalBtn');
    if (cancelModalBtn) {
        cancelModalBtn.addEventListener('click', closeCustomerModal);
    }
    
    // Modal Buttons - Import Modal
    const openImportBtn = document.getElementById('openImportBtn');
    if (openImportBtn) {
        openImportBtn.addEventListener('click', openImportModal);
    }
    
    const closeImportModalBtn = document.getElementById('closeImportModalBtn');
    if (closeImportModalBtn) {
        closeImportModalBtn.addEventListener('click', closeImportModal);
    }
    
    const cancelImportBtn = document.getElementById('cancelImportBtn');
    if (cancelImportBtn) {
        cancelImportBtn.addEventListener('click', closeImportModal);
    }
    
    // Customer Type Toggle
    const customerTypeRadios = document.querySelectorAll('input[name="customerType"]');
    customerTypeRadios.forEach(radio => {
        radio.addEventListener('change', toggleCompanyFields);
    });
    
    // Select All Checkbox
    const selectAllCheckbox = document.getElementById('selectAll');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', handleSelectAll);
    }
    
    // Filter Inputs
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', filterCustomers);
    }
    
    const filterType = document.getElementById('filterType');
    if (filterType) {
        filterType.addEventListener('change', filterCustomers);
    }
    
    const filterStatus = document.getElementById('filterStatus');
    if (filterStatus) {
        filterStatus.addEventListener('change', filterCustomers);
    }
});

// Close modals on ESC key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        const customerModal = document.getElementById('customerModal');
        const importModal = document.getElementById('importModal');
        
        if (customerModal && !customerModal.classList.contains('hidden')) {
            closeCustomerModal();
        }
        if (importModal && !importModal.classList.contains('hidden')) {
            closeImportModal();
        }
    }
});

// Close modals on backdrop click
document.addEventListener('click', (e) => {
    if (e.target.id === 'customerModal') {
        closeCustomerModal();
    }
    if (e.target.id === 'importModal') {
        closeImportModal();
    }
});