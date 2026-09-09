<!-- Modal Edit Company with Two Column Layout - INDEPENDENT SCROLLING -->
<div id="editCompanyModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full h-[95vh] max-w-6xl flex flex-col animate-modal-in">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #3b82f6 100%); padding: 1rem 1.5rem; flex-shrink: 0;">
            <div class="flex justify-between items-center">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 2.5rem; height: 2.5rem; background-color: rgba(255, 255, 255, 0.2); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-building" style="color: white; font-size: 1.125rem;"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-white">Edit Perusahaan</h3>
                </div>
                <button onclick="closeEditCompanyModal()" 
                    class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>

        <!-- Body - Two Column Layout with Independent Scrolling -->
        <div class="flex-1 overflow-hidden flex" style="background-color: #f3f4f6;">
            <form id="editCompanyForm" method="POST" enctype="multipart/form-data"  class="w-full flex overflow-hidden">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_company_id" name="company_id">
                
                <!-- LEFT COLUMN - Company Basic Info & PICs & Address -->
                <div class="w-1/2 overflow-y-auto border-r border-gray-300" style="padding: 1.5rem;">
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        
                        <!-- SECTION 1: Company Info Title -->
                        <div style="padding-bottom: 0.75rem; border-bottom: 2px solid #e5e7eb;">
                            <h4 style="font-size: 0.9375rem; font-weight: 600; color: #1f2937; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fas fa-building" style="color: #4f46e5;"></i>
                                Informasi Perusahaan
                            </h4>
                        </div>

                        <!-- Company Basic Info Fields -->
                        <div style="display: grid; grid-template-columns: 1fr; gap: 0.75rem;">
                            <!-- Nama Perusahaan -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                                    Nama Perusahaan <span style="color: #ef4444;">*</span>
                                </label>
                                <div style="position: relative;">
                                    <i class="fas fa-building" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 0.75rem;"></i>
                                    <input type="text" 
                                        id="edit_company_name"
                                        name="company_name" 
                                        style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 0.75rem 0.5rem 2.25rem; font-size: 0.875rem;" 
                                        placeholder="Masukkan nama perusahaan"
                                        required>
                                </div>
                            </div>
                            
                            <!-- Jenis Perusahaan -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                                    Jenis Perusahaan <span style="color: #ef4444;">*</span>
                                </label>
                                <div style="position: relative;">
                                    <i class="fas fa-tag" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 0.75rem; z-index: 1; pointer-events: none;"></i>
                                    <select id="edit_company_type_id" name="company_type_id" 
                                            style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 2rem 0.5rem 2.25rem; font-size: 0.875rem; appearance: none;" 
                                            required>
                                        <option value="">-- Pilih Jenis --</option>
                                        @foreach($types as $type)
                                        <option value="{{ $type->company_type_id }}">{{ $type->type_name }}</option>
                                        @endforeach
                                    </select>
                                    <i class="fas fa-chevron-down" style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 0.75rem; pointer-events: none;"></i>
                                </div>
                            </div>
                            
                            <!-- Tier -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                                    Tier
                                </label>
                                <div style="position: relative;">
                                    <i class="fas fa-layer-group" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 0.75rem; z-index: 1; pointer-events: none;"></i>
                                    <select id="edit_tier" name="tier" 
                                            style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 2rem 0.5rem 2.25rem; font-size: 0.875rem; appearance: none;">
                                        <option value="">-- Pilih Tier --</option>
                                        <option value="A">Tier A</option>
                                        <option value="B">Tier B</option>
                                        <option value="C">Tier C</option>
                                        <option value="D">Tier D</option>
                                    </select>
                                    <i class="fas fa-chevron-down" style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 0.75rem; pointer-events: none;"></i>
                                </div>
                            </div>
                            
                            <!-- Status -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                                    Status
                                </label>
                                <div style="position: relative;">
                                    <i class="fas fa-toggle-on" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 0.75rem; z-index: 1; pointer-events: none;"></i>
                                    <select id="edit_status" name="status" 
                                            style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 2rem 0.5rem 2.25rem; font-size: 0.875rem; appearance: none;">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                    <i class="fas fa-chevron-down" style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 0.75rem; pointer-events: none;"></i>
                                </div>
                            </div>

                            <!-- Deskripsi -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                                    Deskripsi
                                </label>
                                <div style="position: relative;">
                                    <i class="fas fa-align-left" style="position: absolute; left: 0.75rem; top: 0.625rem; color: #9ca3af; font-size: 0.75rem;"></i>
                                    <textarea id="edit_description" name="description" 
                                            rows="3" 
                                            style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 0.75rem 0.5rem 2.25rem; font-size: 0.875rem; resize: none;" 
                                            placeholder="Tambahkan keterangan tentang perusahaan..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 2: ADDRESS INFORMATION -->
                        <div style="padding-top: 0.75rem; border-top: 2px solid #e5e7eb; border-bottom: 2px solid #e5e7eb; padding-bottom: 0.75rem;">
                            <h4 style="font-size: 0.9375rem; font-weight: 600; color: #1f2937; margin: 0 0 0.75rem 0; display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fas fa-map-marker-alt" style="color: #ef4444;"></i>
                                Informasi Lokasi
                            </h4>

                            <!-- Province -->
                            <div style="margin-bottom: 0.75rem;">
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                                    Provinsi
                                </label>
                                <select id="edit-province" 
                                        name="province_id"
                                        style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem;">
                                    <option value="">-- Pilih Provinsi --</option>
                                            @foreach($provinces as $province)
                                                <option value="{{ $province->id }}">{{ $province->name }}</option>
                                            @endforeach
                                </select>
                            </div>

                            <!-- Regency -->
                            <div style="margin-bottom: 0.75rem;">
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                                    Kabupaten/Kota
                                </label>
                                <select id="edit-regency" 
                                        name="regency_id"
                                        style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem;" disabled>
                                    <option value="">-- Pilih Kabupaten/Kota --</option>
                                </select>
                            </div>

                            <!-- District -->
                            <div style="margin-bottom: 0.75rem;">
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                                    Kecamatan
                                </label>
                                <select id="edit-district" 
                                        name="district_id"
                                        style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem;" disabled>
                                    <option value="">-- Pilih Kecamatan --</option>
                                </select>
                            </div>

                            <!-- Village -->
                            <div style="margin-bottom: 0.75rem;">
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                                    Kelurahan/Desa
                                </label>
                                <select id="edit-village" 
                                        name="village_id"
                                        style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem;" disabled>
                                    <option value="">-- Pilih Kelurahan/Desa --</option>
                                </select>
                            </div>

                            <!-- Full Address -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                                    Alamat Lengkap
                                </label>
                                <textarea id="edit-address"
                                          name="address"
                                          rows="2" 
                                          style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem; resize: none;" 
                                          placeholder="Masukkan alamat lengkap perusahaan..."></textarea>
                            </div>
                        </div>

                        <!-- SECTION 3: PIC INFORMATION -->
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg border border-blue-200 overflow-hidden">
                            <!-- Header - Always Visible -->
                            <div class="p-3 cursor-pointer bg-white hover:bg-blue-100 transition-colors" onclick="toggleEditPICSection()" style="padding: 0.75rem;">
                                <div class="flex items-center justify-between">
                                    <h4 style="font-size: 0.875rem; font-weight: 600; color: #1f2937; display: flex; align-items: center;">
                                        <i class="fas fa-users" style="color: #4f46e5; margin-right: 0.5rem;"></i>
                                        Informasi PIC (Person In Charge)
                                    </h4>
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <span id="edit-pic-status" style="font-size: 0.75rem; color: #6b7280;">Loading...</span>
                                        <i id="edit-pic-toggle-icon" class="fas fa-chevron-down" style="color: #6b7280; transition: transform 0.3s;"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Collapsible Content -->
                            <div id="edit-pic-content" class="hidden" style="padding: 0.75rem;">
                                <!-- Container untuk multiple PICs -->
                                <div id="edit-pic-fields-container" style="display: flex; flex-direction: column; gap: 0.75rem;">
                                    <!-- PIC fields akan diload di sini -->
                                </div>
                                
                                <!-- Button Tambah PIC -->
                                <button type="button" 
                                        onclick="addEditPICField()" 
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
                            <h4 style="font-size: 0.9375rem; font-weight: 600; color: #1f2937; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fas fa-phone" style="color: #059669;"></i>
                                Informasi Kontak & Media
                            </h4>
                        </div>

                        <!-- Contact Information -->
                        <div style="display: grid; grid-template-columns: 1fr; gap: 0.75rem;">
                            <!-- Phone Company -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                                    <i class="fas fa-phone" style="margin-right: 0.375rem; color: #059669;"></i>
                                    Nomor Telepon Perusahaan
                                </label>
                                <input type="tel" 
                                    id="edit_company_phone"
                                    name="company_phone" 
                                    style="width: 100%; background-color: #f9fafb; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem;" 
                                    placeholder="Contoh: 021-12345678 atau +62212345678">
                            </div>

                            <!-- Email Company -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                                    <i class="fas fa-envelope" style="margin-right: 0.375rem; color: #2563eb;"></i>
                                    Email Perusahaan
                                </label>
                                <input type="email" 
                                    id="edit_company_email"
                                    name="company_email" 
                                    style="width: 100%; background-color: #f9fafb; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem;" 
                                    placeholder="Contoh: info@company.com">
                            </div>

                            <!-- Website -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                                    <i class="fas fa-globe" style="margin-right: 0.375rem; color: #7c3aed;"></i>
                                    Website
                                </label>
                                <input type="url" 
                                    id="edit_company_website"
                                    name="company_website" 
                                    style="width: 100%; background-color: #f9fafb; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem;" 
                                    placeholder="Contoh: https://www.company.com">
                            </div>
                        </div>

                        <!-- SECTION 2: SOCIAL MEDIA -->
                        <div style="padding-top: 0.75rem; border-top: 2px solid #e5e7eb; border-bottom: 2px solid #e5e7eb; padding-bottom: 0.75rem;">
                            <h5 style="font-size: 0.8125rem; font-weight: 600; color: #1f2937; margin: 0 0 0.75rem 0; display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fas fa-share-alt" style="color: #2563eb;"></i>
                                Media Sosial
                            </h5>

                            <!-- LinkedIn -->
                            <div style="margin-bottom: 0.75rem;">
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                                    <i class="fab fa-linkedin" style="margin-right: 0.375rem; color: #0a66c2;"></i>
                                    LinkedIn
                                </label>
                                <input type="url" 
                                    id="edit_company_linkedin"
                                    name="company_linkedin" 
                                    style="width: 100%; background-color: #f9fafb; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem;" 
                                    placeholder="https://linkedin.com/company/...">
                            </div>

                            <!-- Instagram -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                                    <i class="fab fa-instagram" style="margin-right: 0.375rem; color: #e1306c;"></i>
                                    Instagram
                                </label>
                                <input type="text" 
                                    id="edit_company_instagram"
                                    name="company_instagram" 
                                    style="width: 100%; background-color: #f9fafb; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem;" 
                                    placeholder="@company_name">
                            </div>
                        </div>

                        <!-- SECTION 3: LOGO UPLOAD -->
                        <div>
                            <h5 style="font-size: 0.8125rem; font-weight: 600; color: #1f2937; margin: 0 0 0.75rem 0; display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fas fa-image" style="color: #f59e0b;"></i>
                                Logo Perusahaan
                            </h5>

                            <!-- File Upload Area -->
                            <div style="border: 2px dashed #d1d5db; border-radius: 0.5rem; padding: 1.5rem; text-align: center; cursor: pointer; transition: all 0.3s; background-color: #fafafa;" 
                                 id="editLogoDropZone"
                                 onmouseover="this.style.borderColor='#3b82f6'; this.style.backgroundColor='#eff6ff';"
                                 onmouseout="this.style.borderColor='#d1d5db'; this.style.backgroundColor='#fafafa';">
                                <input type="file" 
                                    id="edit_company_logo" 
                                    name="company_logo" 
                                    accept="image/*" 
                                    style="display: none;"
                                    onchange="previewEditLogo(event)">
                                
                                <div id="editLogoPreviewContainer" style="display: none;">
                                    <img id="editLogoPreview" src="" style="max-width: 100%; max-height: 150px; margin-bottom: 0.75rem; border-radius: 0.375rem;">
                                    <button type="button" onclick="clearEditLogoPreview()" style="background-color: #ef4444; color: white; border: none; border-radius: 0.375rem; padding: 0.375rem 0.75rem; font-size: 0.6875rem; cursor: pointer;">
                                        <i class="fas fa-trash" style="margin-right: 0.25rem;"></i>Hapus
                                    </button>
                                </div>

                                <div id="editLogoUploadPrompt">
                                    <i class="fas fa-cloud-upload-alt" style="font-size: 2rem; color: #9ca3af; margin-bottom: 0.5rem; display: block;"></i>
                                    <p style="font-size: 0.8125rem; color: #6b7280; margin: 0;">Klik atau drag file gambar di sini</p>
                                    <p style="font-size: 0.7rem; color: #9ca3af; margin: 0.25rem 0 0 0;">PNG, JPG, atau GIF (Max 5MB)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Footer - Fixed at bottom -->
        <div style="display: flex; justify-content: flex-end; gap: 0.5rem; padding: 0.75rem 1.5rem; border-top: 1px solid #e5e7eb; background-color: #f9fafb; flex-shrink: 0;">
            <button type="submit" 
                    form="editCompanyForm"
                    style="background-color: #3b82f6; color: white; border: none; border-radius: 0.5rem; padding: 0.5rem 1.25rem; font-weight: 500; font-size: 0.75rem; cursor: pointer; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); transition: all 0.2s;">
                <i class="fas fa-save" style="margin-right: 0.375rem;"></i>Simpan Perubahan
            </button>
        </div>
    </div>
</div>

<style>
@keyframes modal-in {
    from { opacity: 0; transform: scale(0.95) translateY(-20px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}

.animate-modal-in { animation: modal-in 0.3s ease-out; }

select::-ms-expand { display: none; }

input:focus, select:focus, textarea:focus {
    outline: none;
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
}

.overflow-y-auto::-webkit-scrollbar { width: 6px; }
.overflow-y-auto::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 3px; }
.overflow-y-auto::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
.overflow-y-auto::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

@media (max-width: 768px) {
    .bg-white { max-width: calc(100% - 2rem) !important; }
    div[style*="grid-template-columns: repeat(2, 1fr)"] { grid-template-columns: 1fr !important; }
}
</style>
