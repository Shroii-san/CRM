<!-- Modal Add Company with Two Column Layout - INDEPENDENT SCROLLING -->
<div id="addCompanyModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full h-[95vh] max-w-6xl flex flex-col animate-modal-in">
        <!-- Header -->
        <div style="background: linear-gradient(to right, #4f46e5, #7c3aed); padding: 1rem 1.5rem; flex-shrink: 0;">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-white">Tambah Perusahaan Baru</h3>
                    <p class="text-xs text-indigo-100 mt-0.5">Lengkapi formulir berikut untuk menambahkan perusahaan</p>
                </div>
                <button onclick="closeAddCompanyModal()"
                    class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>

        <!-- Body - Two Column Layout with Independent Scrolling -->
        <div class="flex-1 overflow-hidden flex" style="background-color: #f3f4f6;">
            {{-- form: action dan enctype dihapus, submit ditangani via AJAX (organizations.js) --}}
            <form id="addCompanyForm" class="w-full flex overflow-hidden">
                @csrf

                <!-- LEFT COLUMN - Company Basic Info & PICs & Address -->
                <div class="w-1/2 overflow-y-auto border-r border-gray-300" style="padding: 1.5rem;">
                    <div style="display: flex; flex-direction: column; gap: 1rem;">

                        <!-- SECTION 1: Company Info Title -->
                        <div style="padding-bottom: 0.75rem; border-bottom: 2px solid #e5e7eb;">
                            <h4
                                style="font-size: 0.9375rem; font-weight: 600; color: #1f2937; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fas fa-building" style="color: #4f46e5;"></i>
                                Informasi Perusahaan
                            </h4>
                        </div>

                        <!-- Company Basic Info Fields -->
                        <div style="display: grid; grid-template-columns: 1fr; gap: 0.75rem;">
                            <!-- Nama Perusahaan -->
                            <div>
                                <label
                                    style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                                    Nama Perusahaan <span style="color: #ef4444;">*</span>
                                </label>
                                <input type="text" id="add_name" name="name"
                                    style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem;"
                                    placeholder="Masukkan nama perusahaan" required>
                            </div>

                            <!-- Jenis Perusahaan -->
                            <div>
                                <label
                                    style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                                    Jenis Perusahaan <span style="color: #ef4444;">*</span>
                                </label>
                                <select id="add_industry_id" name="industry_id"
                                    style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem;"
                                    required>
                                    <option value="">-- Pilih Jenis --</option>
                                    @foreach($industries as $industry)
                                        <option value="{{ $industry->id }}">{{ $industry->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Tier -->
                            <div>
                                <label style=" display: block; font-size: 0.75rem; font-weight: 500; color: #374151;
                                        margin-bottom: 0.375rem;">
                                    Tier
                                </label>
                                <select id="add_tier" name="tier"
                                    style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem;">
                                    <option value="">-- Pilih Tier --</option>
                                    <option value="A">Tier A</option>
                                    <option value="B">Tier B</option>
                                    <option value="C">Tier C</option>
                                    <option value="D">Tier D</option>
                                </select>
                            </div>

                            <!-- Status -->
                            <div>
                                <label
                                    style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                                    Status
                                </label>
                                <select id="add_is_active" name="is_active"
                                    style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem;">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>

                            <!-- Deskripsi -->
                            <div>
                                <label
                                    style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                                    Deskripsi
                                </label>
                                <textarea id="add_description" name="description" rows="3"
                                    style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem; resize: none;"
                                    placeholder="Tambahkan keterangan tentang perusahaan..."></textarea>
                            </div>
                        </div>

                        <!-- SECTION 2: ADDRESS INFORMATION -->
                        <div
                            style="padding-top: 0.75rem; border-top: 2px solid #e5e7eb; border-bottom: 2px solid #e5e7eb; padding-bottom: 0.75rem;">
                            <h4
                                style="font-size: 0.9375rem; font-weight: 600; color: #1f2937; margin: 0 0 0.75rem 0; display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fas fa-map-marker-alt" style="color: #ef4444;"></i>
                                Informasi Lokasi
                            </h4>

                            <!-- Province -->
                            <div style="margin-bottom: 0.75rem;">
                                <label
                                    style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                                    Provinsi
                                </label>
                                <select id="create-province" name="province_id"
                                    style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem;">
                                    <option value="">-- Pilih Provinsi --</option>
                                    @foreach($provinces as $province)
                                        <option value="{{ $province->id }}">{{ $province->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Regency -->
                            <div style="margin-bottom: 0.75rem;">
                                <label
                                    style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                                    Kabupaten/Kota
                                </label>
                                <select id="create-regency" name="regency_id"
                                    style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem;"
                                    disabled>
                                    <option value="">-- Pilih Kabupaten/Kota --</option>
                                </select>
                            </div>

                            <!-- District -->
                            <div style="margin-bottom: 0.75rem;">
                                <label
                                    style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                                    Kecamatan
                                </label>
                                <select id="create-district" name="district_id"
                                    style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem;"
                                    disabled>
                                    <option value="">-- Pilih Kecamatan --</option>
                                </select>
                            </div>

                            <!-- Village -->
                            <div style="margin-bottom: 0.75rem;">
                                <label
                                    style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                                    Kelurahan/Desa
                                </label>
                                <select id="create-village" name="village_id"
                                    style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem;"
                                    disabled>
                                    <option value="">-- Pilih Kelurahan/Desa --</option>
                                </select>
                            </div>

                            <!-- Full Address -->
                            <div>
                                <label
                                    style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                                    Alamat Lengkap
                                </label>
                                <textarea id="create-address" name="address" rows="2"
                                    style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem; resize: none;"
                                    placeholder="Masukkan alamat lengkap perusahaan..."></textarea>
                            </div>
                        </div>

                        <!-- SECTION 3: PIC INFORMATION -->
                        <div
                            class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg border border-blue-200 overflow-hidden">
                            <!-- Header - Always Visible -->
                            <div class="p-3 cursor-pointer bg-white hover:bg-blue-100 transition-colors"
                                onclick="togglePICSection()" style="padding: 0.75rem;">
                                <div class="flex items-center justify-between">
                                    <h4
                                        style="font-size: 0.875rem; font-weight: 600; color: #1f2937; display: flex; align-items: center;">
                                        <i class="fas fa-users" style="color: #4f46e5; margin-right: 0.5rem;"></i>
                                        Informasi PIC (Person In Charge)
                                    </h4>
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <span id="pic-status" style="font-size: 0.75rem; color: #6b7280;">Belum
                                            diisi</span>
                                        <i id="pic-toggle-icon" class="fas fa-chevron-down"
                                            style="color: #6b7280; transition: transform 0.3s;"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Collapsible Content -->
                            <div id="pic-content" class="hidden" style="padding: 0.75rem;">
                                <!-- Container untuk multiple PICs -->
                                <div id="pic-fields-container"
                                    style="display: flex; flex-direction: column; gap: 0.75rem;">
                                    <!-- PIC fields akan ditambahkan di sini -->
                                </div>

                                <!-- Button Tambah PIC -->
                                <button type="button" onclick="addPICField()"
                                    style="margin-top: 0.5rem; background-color: #3b82f6; color: white; border: none; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-weight: 500; font-size: 0.75rem; cursor: pointer; display: flex; align-items: center; gap: 0.375rem;">
                                    <i class="fas fa-plus" style="font-size: 0.625rem;"></i>
                                    Tambah PIC
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN - Company Contact & Media Info -->
                <div class="w-1/2 overflow-y-auto" style="padding: 1.5rem; background-color: #ffffff;">
                    <div style="display: flex; flex-direction: column; gap: 1rem;">

                        <!-- SECTION 1: CONTACT INFO Title -->
                        <div style="padding-bottom: 0.75rem; border-bottom: 2px solid #e5e7eb;">
                            <h4
                                style="font-size: 0.9375rem; font-weight: 600; color: #1f2937; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fas fa-phone" style="color: #059669;"></i>
                                Informasi Kontak & Media
                            </h4>
                        </div>

                        <!-- Contact Information -->
                        <div style="display: grid; grid-template-columns: 1fr; gap: 0.75rem;">
                            <!-- Phone Company -->
                            <div>
                                <label
                                    style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                                    <i class="fas fa-phone" style="margin-right: 0.375rem; color: #059669;"></i>
                                    Nomor Telepon Perusahaan
                                </label>
                                <input type="tel" id="add_phone" name="phone"
                                    style="width: 100%; background-color: #f9fafb; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem;"
                                    placeholder="Contoh: 021-12345678 atau +62212345678">
                            </div>

                            <!-- Email Company -->
                            <div>
                                <label
                                    style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                                    <i class="fas fa-envelope" style="margin-right: 0.375rem; color: #2563eb;"></i>
                                    Email Perusahaan
                                </label>
                                <input type="email" id="add_email" name="email"
                                    style="width: 100%; background-color: #f9fafb; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem;"
                                    placeholder="Contoh: info@company.com">
                            </div>

                            <!-- Website -->
                            <div>
                                <label
                                    style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                                    <i class="fas fa-globe" style="margin-right: 0.375rem; color: #7c3aed;"></i>
                                    Website
                                </label>
                                <input type="url" id="add_website" name="website"
                                    style="width: 100%; background-color: #f9fafb; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem;"
                                    placeholder="Contoh: https://www.company.com">
                            </div>
                        </div>

                        <!-- SECTION 2: LOGO PERUSAHAAN -->
                        <div style="padding-top: 0.75rem; border-top: 2px solid #e5e7eb;">
                            <h4
                                style="font-size: 0.9375rem; font-weight: 600; color: #1f2937; margin: 0 0 0.75rem 0; display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fas fa-image" style="color: #4f46e5;"></i>
                                Logo Perusahaan
                            </h4>
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                                    Upload Logo (JPG, PNG, GIF, WebP - Maks 5MB)
                                </label>
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <div id="add_logo_preview_wrap" style="width: 4rem; height: 4rem; border-radius: 0.5rem; background-color: #f3f4f6; border: 1px dashed #d1d5db; display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0;">
                                        <i id="add_logo_placeholder_icon" class="fas fa-building" style="color: #9ca3af; font-size: 1.25rem;"></i>
                                        <img id="add_logo_preview" src="" alt="Preview" style="display: none; width: 100%; height: 100%; object-fit: contain;">
                                    </div>
                                    <div style="flex: 1;">
                                        <input type="file" id="add_logo" name="logo" accept="image/jpeg,image/png,image/gif,image/webp"
                                            onchange="if(this.files && this.files[0]){const r=new FileReader();r.onload=e=>{document.getElementById('add_logo_preview').src=e.target.result;document.getElementById('add_logo_preview').style.display='block';document.getElementById('add_logo_placeholder_icon').style.display='none';};r.readAsDataURL(this.files[0]);}"
                                            style="width: 100%; font-size: 0.75rem; color: #4b5563;">
                                        <p style="font-size: 0.7rem; color: #6b7280; margin-top: 0.25rem; margin-bottom: 0;">
                                            Gambar akan otomatis dikompresi di server untuk efisiensi penyimpanan.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Footer - Fixed at bottom -->
        <div
            style="display: flex; justify-content: flex-end; gap: 0.5rem; padding: 0.75rem 1.5rem; border-top: 1px solid #e5e7eb; background-color: #f9fafb; flex-shrink: 0;">
            <button type="button" onclick="submitAddCompanyModal()"
                style="background-color: #4f46e5; color: white; border: none; border-radius: 0.5rem; padding: 0.5rem 1.25rem; font-weight: 500; font-size: 0.75rem; cursor: pointer; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); transition: all 0.2s;">
                <i class="fas fa-save" style="margin-right: 0.375rem;"></i>Simpan Data
            </button>
        </div>
    </div>
</div>

<style>
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

    /* Custom scrollbar untuk kedua kolom */
    div.overflow-y-auto::-webkit-scrollbar {
        width: 6px;
    }

    div.overflow-y-auto::-webkit-scrollbar-track {
        background: #f1f5f9;
    }

    div.overflow-y-auto::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    div.overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Focus styles for inputs */
    input:focus,
    select:focus,
    textarea:focus {
        outline: none;
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
    }
</style>