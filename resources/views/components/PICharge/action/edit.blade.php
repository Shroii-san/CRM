@props(['companies' => []])

<!-- MODAL EDIT PIC -->
<div id="editPICModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden animate-modal-in">
        <!-- Header -->
        <div style="background: linear-gradient(to right, #4f46e5, #7c3aed); padding: 1.25rem 1.5rem;">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-semibold text-white">Edit PIC</h3>
                    <p class="text-sm text-indigo-100 mt-1">Perbarui informasi Person In Charge</p>
                </div>
                <button onclick="closeEditPICModal()" 
                    class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>

        <!-- Body -->
        <div class="overflow-y-auto max-h-[calc(90vh-140px)]" style="background-color: #f3f4f6; padding: 1.5rem;">
            <form id="editPICForm" action="" method="POST" style="display: flex; flex-direction: column; gap: 1rem;">
                @csrf
                @method('PUT')
                
                <input type="hidden" id="edit_pic_id" name="pic_id">
                
                <!-- Perusahaan -->
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                        Perusahaan <span style="color: #ef4444;">*</span>
                    </label>
                    <div style="position: relative;">
                        <i class="fas fa-building" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #9ca3af;"></i>
                        <select id="edit_company_id" name="company_id" required
                            style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.75rem 1rem 0.75rem 2.5rem; font-size: 0.875rem;">
                            <option value="">-- Pilih Perusahaan --</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->company_id }}">{{ $company->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Nama PIC -->
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                        Nama PIC <span style="color: #ef4444;">*</span>
                    </label>
                    <div style="position: relative;">
                        <i class="fas fa-user-tie" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #9ca3af;"></i>
                        <input type="text" id="edit_pic_name" name="name"
                            style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.75rem 1rem 0.75rem 2.5rem; font-size: 0.875rem;" 
                            required>
                    </div>
                </div>

                <!-- Jabatan -->
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                        Jabatan
                    </label>
                    <div style="position: relative;">
                        <i class="fas fa-briefcase" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #9ca3af;"></i>
                        <input type="text" id="edit_pic_position" name="position"
                            style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.75rem 1rem 0.75rem 2.5rem; font-size: 0.875rem;">
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                        Email
                    </label>
                    <div style="position: relative;">
                        <i class="fas fa-envelope" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #9ca3af;"></i>
                        <input type="email" id="edit_pic_email" name="email"
                            style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.75rem 1rem 0.75rem 2.5rem; font-size: 0.875rem;">
                    </div>
                </div>

                <!-- Telepon -->
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                        Telepon
                    </label>
                    <div style="position: relative;">
                        <i class="fas fa-phone" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #9ca3af;"></i>
                        <input type="text" id="edit_pic_phone" name="phone"
                            style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.75rem 1rem 0.75rem 2.5rem; font-size: 0.875rem;">
                    </div>
                </div>

                <!-- Tombol -->
                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; padding-top: 1rem; border-top: 1px solid #e5e7eb; margin-top: 1.5rem;">
                    <button type="button" 
                            onclick="closeEditPICModal()" 
                            style="background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.625rem 1.5rem; font-weight: 500; font-size: 0.875rem; cursor: pointer; transition: all 0.2s;">
                        Batal
                    </button>
                    <button type="submit" 
                            style="background-color: #4f46e5; color: white; border: none; border-radius: 0.5rem; padding: 0.625rem 1.5rem; font-weight: 500; font-size: 0.875rem; cursor: pointer; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); transition: all 0.2s;">
                        <i class="fas fa-save" style="margin-right: 0.5rem;"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    @keyframes modal-in {
        from {
            opacity: 0;
            transform: scale(0.9) translateY(-20px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    .animate-modal-in {
        animation: modal-in 0.3s ease-out;
    }

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

    #editPICModal input:focus, 
    #editPICModal select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
</style>

<script>
function openEditPICModal(picId, companyId, name, position, email, phone) {
    document.getElementById('edit_pic_id').value = picId || '';
    document.getElementById('edit_pic_name').value = name || '';
    document.getElementById('edit_pic_position').value = position || '';
    document.getElementById('edit_pic_email').value = email || '';
    document.getElementById('edit_pic_phone').value = phone || '';
    document.getElementById('edit_company_id').value = companyId || '';
    
    // Set form action dynamically
    const form = document.getElementById('editPICForm');
    form.action = `/pic/${picId}`;
    
    document.getElementById('editPICModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    setTimeout(() => {
        const firstInput = document.getElementById('edit_pic_name');
        if (firstInput) firstInput.focus();
    }, 300);
}

function closeEditPICModal() {
    document.getElementById('editPICModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    document.getElementById('editPICForm').reset();
}

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('editPICModal');
        if (modal && !modal.classList.contains('hidden')) {
            closeEditPICModal();
        }
    }
});

// Close modal on backdrop click
document.getElementById('editPICModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditPICModal();
    }
});
</script>