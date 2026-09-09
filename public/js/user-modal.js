// ========================================
// CREATE MODAL FUNCTIONS
// ========================================

let createCascadeInstance = null; // Track instance

function openUserModal() {
    document.getElementById('userModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    console.log('Modal opened - Initializing cascade');
    setTimeout(() => {
        // Destroy previous instance if exists
        if (createCascadeInstance) {
            createCascadeInstance.destroy();
        }
        
        createCascadeInstance = new AddressCascade({
            provinceId: 'create-province',
            regencyId: 'create-regency',
            districtId: 'create-district',
            villageId: 'create-village'
        });
        const usernameInput = document.querySelector('#userModal input[name="username"]');
        if (usernameInput) usernameInput.focus();
    }, 300);
}

function closeUserModal() {
    document.getElementById('userModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    
    const form = document.querySelector('#userModal form');
    if (form) form.reset();
    clearUserValidation();
    resetAddressDropdowns();
    hideRolePreview();
    
    // Destroy cascade instance
    if (createCascadeInstance) {
        createCascadeInstance.destroy();
        createCascadeInstance = null;
    }
}

function resetAddressDropdowns() {
    const regencySelect = document.getElementById('create-regency');
    const districtSelect = document.getElementById('create-district');
    const villageSelect = document.getElementById('create-village');
    
    if (regencySelect) regencySelect.innerHTML = '<option value="">-- Pilih Kabupaten/Kota --</option>';
    if (districtSelect) districtSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
    if (villageSelect) villageSelect.innerHTML = '<option value="">-- Pilih Kelurahan/Desa --</option>';
}

function toggleUserPassword() {
    const field = document.getElementById('userPassword');
    const toggle = document.getElementById('userPasswordToggle');
    
    if (!field || !toggle) return;
    
    if (field.type === 'password') {
        field.type = 'text';
        toggle.classList.remove('fa-eye');
        toggle.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        toggle.classList.remove('fa-eye-slash');
        toggle.classList.add('fa-eye');
    }
}

function validateUserForm() {
    const form = document.querySelector('#userModal form');
    if (!form) return false;
    
    const requiredInputs = form.querySelectorAll('input[required], select[required]');
    let isValid = true;
    
    requiredInputs.forEach(input => {
        if (!input.value.trim()) {
            showUserFieldError(input, 'This field is required');
            isValid = false;
        } else {
            clearUserFieldError(input);
        }
    });
    
    const email = form.querySelector('input[name="email"]');
    if (email && email.value && !isValidEmail(email.value)) {
        showUserFieldError(email, 'Please enter a valid email address');
        isValid = false;
    }
    
    const password = form.querySelector('input[name="password"]');
    if (password && password.value && password.value.length < 6) {
        showUserFieldError(password, 'Password must be at least 6 characters');
        isValid = false;
    }
    
    return isValid;
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function showUserFieldError(field, message) {
    field.classList.add('error');
    field.classList.remove('success');
    
    const existingError = field.parentNode.querySelector('.error-message');
    if (existingError) existingError.remove();
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message text-red-500 text-xs mt-1';
    errorDiv.textContent = message;
    field.parentNode.appendChild(errorDiv);
}

function clearUserFieldError(field) {
    field.classList.remove('error');
    field.classList.add('success');
    
    const errorMessage = field.parentNode.querySelector('.error-message');
    if (errorMessage) errorMessage.remove();
}

function clearUserValidation() {
    const fields = document.querySelectorAll('#userModal input, #userModal textarea, #userModal select');
    fields.forEach(field => {
        field.classList.remove('error', 'success');
    });
    
    const errorMessages = document.querySelectorAll('#userModal .error-message');
    errorMessages.forEach(msg => msg.remove());
}

function showRolePreview(roleId) {
    const rolePreview = document.getElementById('rolePreview');
    const rolePermissions = document.getElementById('rolePermissions');
    
    if (!rolePreview || !rolePermissions) return;
    
    if (roleId) {
        const mockPermissions = {
            1: ['View Dashboard', 'Manage Users', 'System Settings'],
            2: ['View Dashboard', 'Manage Content'],
            3: ['View Dashboard', 'Basic Operations']
        };
        
        if (mockPermissions[roleId]) {
            rolePermissions.innerHTML = mockPermissions[roleId].map(perm => 
                `<span class="inline-block bg-indigo-100 text-indigo-800 px-2 py-1 rounded text-xs mr-2 mb-1">${perm}</span>`
            ).join('');
            rolePreview.classList.remove('hidden');
        }
    } else {
        hideRolePreview();
    }
}

function hideRolePreview() {
    const rolePreview = document.getElementById('rolePreview');
    if (rolePreview) rolePreview.classList.add('hidden');
}

function setupFormSubmission() {
    const form = document.querySelector('#userModal form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (!submitBtn) return;
            
            const originalText = submitBtn.innerHTML;

            submitBtn.classList.add('btn-loading');
            submitBtn.innerHTML = 'Creating User...';
            submitBtn.disabled = true;

            if (!validateUserForm()) {
                e.preventDefault();
                submitBtn.classList.remove('btn-loading');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                return;
            }
        });
    }
}

// ========================================
// EDIT MODAL FUNCTIONS
// ========================================

let editCascadeInstance = null;

function openEditModal(userId, username, email, roleId, isActive, phone, birthDate, alamat, provinceId, regencyId, districtId, villageId) {
    document.getElementById('editUserId').value = userId;
    document.getElementById('editUsername').value = username;
    document.getElementById('editEmail').value = email;
    document.getElementById('editRole').value = roleId;
    document.getElementById('editIsActive').checked = isActive == 1 || isActive == true;
    document.getElementById('editPhone').value = phone || '';
    document.getElementById('editBirthDate').value = birthDate || '';
    document.getElementById('editAlamat').value = alamat || '';
    document.getElementById('editPassword').value = '';
    document.getElementById('editPasswordConfirm').value = '';
    
    document.getElementById('editUserModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    setTimeout(() => {
        initEditAddressCascade(provinceId, regencyId, districtId, villageId);
    }, 300);
}

function initEditAddressCascade(provinceId, regencyId, districtId, villageId) {
    console.log('Initializing EDIT cascade with data:', {provinceId, regencyId, districtId, villageId});
    
    // Destroy previous instance if exists
    if (editCascadeInstance) {
        editCascadeInstance.destroy();
    }
    
    editCascadeInstance = new AddressCascade({
        provinceId: 'edit-province',
        regencyId: 'edit-regency',
        districtId: 'edit-district',
        villageId: 'edit-village'
    });
    
    if (provinceId) {
        document.getElementById('edit-province').value = provinceId;
        document.getElementById('edit-province').dispatchEvent(new Event('change'));
        
        if (regencyId) {
            setTimeout(() => {
                document.getElementById('edit-regency').value = regencyId;
                document.getElementById('edit-regency').dispatchEvent(new Event('change'));
                
                if (districtId) {
                    setTimeout(() => {
                        document.getElementById('edit-district').value = districtId;
                        document.getElementById('edit-district').dispatchEvent(new Event('change'));
                        
                        if (villageId) {
                            setTimeout(() => {
                                document.getElementById('edit-village').value = villageId;
                            }, 500);
                        }
                    }, 500);
                }
            }, 500);
        }
    }
}

function closeEditModal() {
    document.getElementById('editUserModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    
    // Destroy cascade instance
    if (editCascadeInstance) {
        editCascadeInstance.destroy();
        editCascadeInstance = null;
    }
}

function resetEditAddressDropdowns() {
    const regencySelect = document.getElementById('edit-regency');
    const districtSelect = document.getElementById('edit-district');
    const villageSelect = document.getElementById('edit-village');
    
    if (regencySelect) regencySelect.innerHTML = '<option value="">-- Pilih Kabupaten/Kota --</option>';
    if (districtSelect) districtSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
    if (villageSelect) villageSelect.innerHTML = '<option value="">-- Pilih Kelurahan/Desa --</option>';
}

function toggleEditPassword() {
    const field = document.getElementById('editPassword');
    const toggle = document.getElementById('editPasswordToggle');
    
    if (!field || !toggle) return;
    
    if (field.type === 'password') {
        field.type = 'text';
        toggle.classList.remove('fa-eye');
        toggle.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        toggle.classList.remove('fa-eye-slash');
        toggle.classList.add('fa-eye');
    }
}

// ========================================
// EVENT LISTENERS - SEMUA DIGABUNG
// ========================================

document.addEventListener('DOMContentLoaded', function() {
    console.log('User Modal JS loaded');
    
    // Setup CREATE form submission
    setupFormSubmission();
    
    // Setup EDIT form submission
    const editForm = document.getElementById('editUserForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const password = document.getElementById('editPassword').value;
            const passwordConfirm = document.getElementById('editPasswordConfirm').value;
            
            if (password && password !== passwordConfirm) {
                alert('Passwords do not match!');
                return;
            }
            
            const userId = document.getElementById('editUserId').value;
            this.action = `/users/${userId}`;
            this.submit();
        });
    }
    
    // Close edit modal when clicking outside
    const editModal = document.getElementById('editUserModal');
    if (editModal) {
        editModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });
    }
    
    // Uppercase username for CREATE modal
    const createUsernameInput = document.querySelector('#userModal input[name="username"]');
    if (createUsernameInput) {
        createUsernameInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.toUpperCase();
        });
    }
    
    // Uppercase username for EDIT modal  
    const editUsernameInput = document.querySelector('#editUsername');
    if (editUsernameInput) {
        editUsernameInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.toUpperCase();
        });
    }
});

// Keyboard events
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const userModal = document.getElementById('userModal');
        const editModal = document.getElementById('editUserModal');
        
        if (userModal && !userModal.classList.contains('hidden')) {
            closeUserModal();
        }
        if (editModal && !editModal.classList.contains('hidden')) {
            closeEditModal();
        }
    }
});

// Click outside to close
document.addEventListener('click', function(e) {
    const userModal = document.getElementById('userModal');
    const editModal = document.getElementById('editUserModal');
    
    if (userModal && e.target === userModal) {
        closeUserModal();
    }
    if (editModal && e.target === editModal) {
        closeEditModal();
    }
});

// Real-time validation
document.addEventListener('input', function(e) {
    if (e.target.matches('#userModal input, #userModal textarea')) {
        clearUserFieldError(e.target);
    }
});

// Role selection change
document.addEventListener('change', function(e) {
    if (e.target.matches('#userModal select[name="role_id"]')) {
        showRolePreview(e.target.value);
    }
});