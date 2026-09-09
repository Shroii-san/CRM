<!-- Add User Modal -->
<div id="userModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-hidden animate-modal-in">
        <!-- Header -->
        <div style="background: linear-gradient(to right, #4f46e5, #7c3aed); padding: 1rem 1.25rem;">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-white">Add New User</h3>
                    <p class="text-xs text-indigo-100 mt-0.5">Create a new user account with role assignment</p>
                </div>
                <button onclick="closeUserModal()" 
                    class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>

        <!-- Body -->
        <div class="overflow-y-auto max-h-[calc(90vh-120px)]" style="background-color: #f3f4f6; padding: 1rem;">
            <form action="{{ route('users.store') }}" method="POST" style="display: flex; flex-direction: column; gap: 0.75rem;">
                @csrf
                
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem;">
                    <!-- Username -->
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                            Username <span style="color: #ef4444;">*</span>
                        </label>
                        <div style="position: relative;">
                            <i class="fas fa-user" style="position: absolute; left: 0.625rem; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 0.75rem;"></i>
                            <input type="text" name="username" 
                                style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 0.75rem 0.5rem 2rem; font-size: 0.875rem;" 
                                placeholder="Enter username" required>
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                            Email Address
                        </label>
                        <div style="position: relative;">
                            <i class="fas fa-envelope" style="position: absolute; left: 0.625rem; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 0.75rem;"></i>
                            <input type="email" name="email" 
                                style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 0.75rem 0.5rem 2rem; font-size: 0.875rem;" 
                                placeholder="user@example.com">
                        </div>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                            Phone Number
                        </label>
                        <div style="position: relative;">
                            <i class="fas fa-phone" style="position: absolute; left: 0.625rem; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 0.75rem;"></i>
                            <input type="text" name="phone" 
                                style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 0.75rem 0.5rem 2rem; font-size: 0.875rem;" 
                                placeholder="+62 812-3456-7890">
                        </div>
                    </div>

                    <!-- Birth Date -->
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                            Birth Date
                        </label>
                        <div style="position: relative;">
                            <i class="fas fa-calendar-alt" style="position: absolute; left: 0.625rem; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 0.75rem;"></i>
                            <input type="date" name="birth_date" 
                                style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 0.75rem 0.5rem 2rem; font-size: 0.875rem;" 
                                max="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                            Password <span style="color: #ef4444;">*</span>
                        </label>
                        <div style="position: relative;">
                            <i class="fas fa-lock" style="position: absolute; left: 0.625rem; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 0.75rem;"></i>
                            <input type="password" name="password" id="userPassword"
                                style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 2.25rem 0.5rem 2rem; font-size: 0.875rem;" 
                                placeholder="Minimum 6 characters" required>
                            <button type="button" onclick="toggleUserPassword()" 
                                style="position: absolute; right: 0.625rem; top: 50%; transform: translateY(-50%); color: #9ca3af; background: none; border: none; cursor: pointer;">
                                <i class="fas fa-eye" id="userPasswordToggle" style="font-size: 0.75rem;"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Role -->
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                            Role <span style="color: #ef4444;">*</span>
                        </label>
                        <div style="position: relative;">
                            <i class="fas fa-user-tag" style="position: absolute; left: 0.625rem; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 0.75rem; z-index: 10;"></i>
                            <select name="role_id" 
                                style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 2rem 0.5rem 2rem; font-size: 0.875rem; appearance: none;" 
                                required>
                                <option value="">Select role</option>
                                @foreach($roles as $role)
                                    @php
                                        $isSuperAdmin = auth()->user()->role->role_name === 'Superadmin' || auth()->user()->role->role_name === 'superadmin';
                                        $isProtectedRole = in_array($role->role_name, ['Admin', 'Superadmin', 'admin', 'superadmin']);
                                    @endphp
                                    
                                    @if(!$isProtectedRole || $isSuperAdmin)
                                        <option value="{{ $role->role_id }}">{{ $role->role_name }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down" style="position: absolute; right: 0.625rem; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 0.75rem; pointer-events: none;"></i>
                        </div>
                    </div>
                </div>

                <!-- Address Section -->
                <div style="background: linear-gradient(to bottom right, #eff6ff, #e0e7ff); border: 1px solid #c7d2fe; border-radius: 0.5rem; padding: 0.75rem;">
                    <h4 style="font-size: 0.875rem; font-weight: 600; color: #1f2937; margin-bottom: 0.75rem; display: flex; align-items: center;">
                        <i class="fas fa-map-marker-alt" style="color: #6366f1; margin-right: 0.5rem; font-size: 0.875rem;"></i>
                        Address Information
                    </h4>
                    
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem;">
                        <!-- Provinsi -->
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                                Provinsi <span style="color: #ef4444;">*</span>
                            </label>
                            <select id="create-province" name="province_id" class="cascade-province"
                                    style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem;" 
                                    required>
                                <option value="">-- Pilih Provinsi --</option>
                                @foreach($provinces as $province)
                                    <option value="{{ $province->id }}">{{ $province->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Kabupaten/Kota -->
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                                Kabupaten/Kota <span style="color: #ef4444;">*</span>
                            </label>
                            <select id="create-regency" name="regency_id" class="cascade-regency"
                                    style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem;" 
                                    required disabled>
                                <option value="">-- Pilih Kabupaten/Kota --</option>
                            </select>
                        </div>

                        <!-- Kecamatan -->
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                                Kecamatan <span style="color: #ef4444;">*</span>
                            </label>
                            <select id="create-district" name="district_id" class="cascade-district"
                                    style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem;" 
                                    required disabled>
                                <option value="">-- Pilih Kecamatan --</option>
                            </select>
                        </div>

                        <!-- Kelurahan/Desa -->
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                                Kelurahan/Desa <span style="color: #ef4444;">*</span>
                            </label>
                            <select id="create-village" name="village_id" class="cascade-village"
                                    style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem;" 
                                    required disabled>
                                <option value="">-- Pilih Kelurahan/Desa --</option>
                            </select>
                        </div>

                        <!-- Detail Alamat (full width) -->
                        <div style="grid-column: span 2;">
                            <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                                Detail Alamat
                            </label>
                            <div style="position: relative;">
                                <i class="fas fa-home" style="position: absolute; left: 0.625rem; top: 0.625rem; color: #9ca3af; font-size: 0.75rem;"></i>
                                <textarea id="address" name="address" rows="2" 
                                        placeholder="Contoh: Jl. Merdeka No. 123, RT 01/RW 02" 
                                        style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 0.75rem 0.5rem 2rem; font-size: 0.875rem; resize: none;"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tombol -->
                <div style="display: flex; justify-content: flex-end; gap: 0.5rem; padding-top: 0.75rem; border-top: 1px solid #e5e7eb; margin-top: 0.5rem;">
                    <button type="button" 
                            onclick="closeUserModal()" 
                            style="background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 1.25rem; font-weight: 500; font-size: 0.75rem; cursor: pointer; transition: all 0.2s;">
                        Cancel
                    </button>
                    <button type="submit" 
                            style="background-color: #4f46e5; color: white; border: none; border-radius: 0.5rem; padding: 0.5rem 1.25rem; font-weight: 500; font-size: 0.75rem; cursor: pointer; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); transition: all 0.2s;">
                        <i class="fas fa-user-plus" style="margin-right: 0.375rem;"></i>
                        Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Modal Animation */
    @keyframes modal-in {
        from {
            opacity: 0;
            transform: scale(0.95) translateY(-20px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    .animate-modal-in {
        animation: modal-in 0.3s ease-out;
    }

    /* Custom Scrollbar for Modal */
    .overflow-y-auto::-webkit-scrollbar {
        width: 6px;
    }

    .overflow-y-auto::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }

    .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Enhanced focus states */
    input:focus, textarea:focus, select:focus {
        outline: none;
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
    }

    select:disabled {
        background-color: #f3f4f6;
        cursor: not-allowed;
        opacity: 0.6;
    }

    /* Responsive adjustments */
    @media (max-width: 640px) {
        #userModal .bg-white {
            margin: 1rem;
            max-width: calc(100% - 2rem);
        }
    }
</style>

<script>
function toggleUserPassword() {
    const field = document.getElementById('userPassword');
    const toggle = document.getElementById('userPasswordToggle');
    
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

function closeUserModal() {
    document.getElementById('userModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    const userModal = document.getElementById('userModal');
    if (e.target === userModal) {
        closeUserModal();
    }
});

// Close modal with ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const userModal = document.getElementById('userModal');
        if (userModal && !userModal.classList.contains('hidden')) {
            closeUserModal();
        }
    }
});
</script>