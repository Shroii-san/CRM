@props(['companies' => [], 'currentMenuId'])

<!-- Modal Tambah PIC -->
<div id="picModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden animate-modal-in">
        <!-- Header -->
        <div style="background: linear-gradient(to right, #4f46e5, #7c3aed); padding: 1.25rem 1.5rem;">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-semibold text-white">Tambah PIC Baru</h3>
                    <p class="text-sm text-indigo-100 mt-1">Isi informasi Person In Charge</p>
                </div>
                <button onclick="closePICModal()" 
                    class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>

        <!-- Body -->
        <div class="overflow-y-auto max-h-[calc(90vh-140px)]" style="background-color: #f3f4f6; padding: 1.5rem;">
            <form id="picForm" action="{{ route('pics.store') }}" method="POST" style="display: flex; flex-direction: column; gap: 1rem;">
                @csrf
                
                <!-- Perusahaan -->
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                        Perusahaan <span style="color: #ef4444;">*</span>
                    </label>
                    <div style="position: relative;">
                        <i class="fas fa-building" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #9ca3af;"></i>
                        <select name="company_id" required
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
                        <input type="text" name="name"
                            style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.75rem 1rem 0.75rem 2.5rem; font-size: 0.875rem;"
                            placeholder="Masukkan nama PIC" required>
                    </div>
                </div>

                <!-- Jabatan -->
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                        Jabatan
                    </label>
                    <div style="position: relative;">
                        <i class="fas fa-briefcase" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #9ca3af;"></i>
                        <input type="text" name="position"
                            style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.75rem 1rem 0.75rem 2.5rem; font-size: 0.875rem;"
                            placeholder="Contoh: Manager, Supervisor">
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                        Email
                    </label>
                    <div style="position: relative;">
                        <i class="fas fa-envelope" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #9ca3af;"></i>
                        <input type="email" name="email"
                            style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.75rem 1rem 0.75rem 2.5rem; font-size: 0.875rem;"
                            placeholder="contoh@email.com">
                    </div>
                </div>

                <!-- Telepon -->
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                        Telepon
                    </label>
                    <div style="position: relative;">
                        <i class="fas fa-phone" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #9ca3af;"></i>
                        <input type="text" name="phone"
                            style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.75rem 1rem 0.75rem 2.5rem; font-size: 0.875rem;"
                            placeholder="+62 812-3456-7890">
                    </div>
                </div>

                <!-- Tombol -->
                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; padding-top: 1rem; border-top: 1px solid #e5e7eb; margin-top: 1.5rem;">
                    <button type="button" 
                            onclick="closePICModal()" 
                            style="background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.625rem 1.5rem; font-weight: 500; font-size: 0.875rem; cursor: pointer; transition: all 0.2s;">
                        Batal
                    </button>
                    <button type="submit" 
                            style="background-color: #4f46e5; color: white; border: none; border-radius: 0.5rem; padding: 0.625rem 1.5rem; font-weight: 500; font-size: 0.875rem; cursor: pointer; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); transition: all 0.2s;">
                        <i class="fas fa-save" style="margin-right: 0.5rem;"></i>
                        Simpan Data
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

    #picModal input:focus, 
    #picModal select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
</style>

<script>
function openPICModal() {
    document.getElementById('picModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    setTimeout(() => {
        const firstInput = document.querySelector('#picModal input[name="name"]');
        if (firstInput) firstInput.focus();
    }, 300);
}

function closePICModal() {
    document.getElementById('picModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    document.getElementById('picForm').reset();
}

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !document.getElementById('picModal').classList.contains('hidden')) {
        closePICModal();
    }
});

// Close modal on backdrop click
document.getElementById('picModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closePICModal();
    }
});
</script>