@props([
    'divisions' => [],
    'modalId' => 'modal',
    'formId' => 'addForm',
    'action' => '',
    'title' => 'Tambah Calon Karyawan',
    'icon' => 'fa-user-plus',
    'submitText' => 'Simpan calon karyawan',
    'isEdit' => false,
    'isUpdate' => false,
])

@php
    $defaultDivisions = ['Direktur', 'Keuangan', 'Multimedia', 'IT', 'Administrasi'];
    $allDivisions = collect($divisions)->merge($defaultDivisions)->unique()->filter()->values();
@endphp

<div id="{{ $modalId }}" x-data="{
        open: false,
        step: 1,
        totalSteps: 3,
        goToStep(s) {
            if (s > this.step) {
                const currentSec = this.$el.querySelector('section:not([style*=\'display: none\'])');
                if (currentSec) {
                    const inv = Array.from(currentSec.querySelectorAll('input, select, textarea')).find(el => !el.checkValidity());
                    if (inv) {
                        inv.reportValidity();
                        return;
                    }
                }
            }
            this.step = s;
        },
        nextStep() {
            const currentSec = this.$el.querySelector('section:not([style*=\'display: none\'])');
            if (currentSec) {
                const inv = Array.from(currentSec.querySelectorAll('input, select, textarea')).find(el => !el.checkValidity());
                if (inv) {
                    inv.reportValidity();
                    return;
                }
            }
            if (this.step < this.totalSteps) {
                this.step++;
            }
        },
        prevStep() {
            if (this.step > 1) {
                this.step--;
            }
        },
        closeModal() {
            this.open = false;
        }
    }" x-show="open" x-cloak @if($isEdit) @open-edit-modal.window="
        open = true;
        step = 1;
        $nextTick(() => window.populateCandidateEditForm?.($event.detail));
    " @else @open-modal.window="
        open = true;
        step = 1;
        $nextTick(() => {
            const f = document.getElementById('{{ $formId }}');
            if (f) f.reset();
            window.kosongkanPendidikan?.('pendidikanRowsAdd', false);
        });
    " @endif @keydown.escape.window="closeModal()" @click.self="closeModal()"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-5xl max-h-[94vh] flex min-h-0 flex-col overflow-hidden rounded-xl bg-white shadow-2xl">
        <div class="flex shrink-0 items-center justify-between rounded-t-xl px-5 py-3 text-white"
            style="background: linear-gradient(to right, #7c3aed, #3b82f6);">
            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/20">
                    <i class="fas {{ $icon }} text-sm text-white"></i>
                </div>
                <h2 class="text-sm font-semibold">{{ $title }}</h2>
            </div>
            <button type="button" @click="closeModal()"
                class="flex h-7 w-7 items-center justify-center rounded-full text-xl leading-none text-white transition-colors hover:bg-white/20">
                &times;
            </button>
        </div>

        <form id="{{ $formId }}" action="{{ $action }}" method="POST" enctype="multipart/form-data"
            class="flex min-h-0 flex-1 flex-col">
            @csrf
            @if($isUpdate)
                @method('PUT')
            @endif

            <div class="flex shrink-0 gap-2 overflow-x-auto border-b px-4 py-3">
                <button type="button"
                    class="px-3 py-2 rounded-lg whitespace-nowrap text-sm border border-transparent transition-all duration-150"
                    :class="step === 1 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                    @click="goToStep(1)">
                    1. Data Diri
                </button>
                <button type="button"
                    class="px-3 py-2 rounded-lg whitespace-nowrap text-sm border border-transparent transition-all duration-150"
                    :class="step === 2 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                    @click="goToStep(2)">
                    2. Pendidikan
                </button>
                <button type="button"
                    class="px-3 py-2 rounded-lg whitespace-nowrap text-sm border border-transparent transition-all duration-150"
                    :class="step === 3 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                    @click="goToStep(3)">
                    3. Dokumen
                </button>
            </div>

            <div class="min-h-0 flex-1 overflow-y-auto p-4">
                <section x-show="step === 1" class="space-y-4">
                    <div>
                        <h3 class="mb-2 text-sm font-medium text-gray-700"><i
                                class="fas fa-user mr-1 text-blue-600"></i> Informasi Dasar</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Full name *</label>
                                <input type="text" name="full_name" placeholder="Full name" required
                                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">NIK *</label>
                                <input type="text" name="nik" required maxlength="16" placeholder="16 digit"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Phone number *</label>
                                <input type="text" name="phone_number" required placeholder="08xxxxxxxxxx"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="mb-2 text-sm font-medium text-gray-700"><i
                                class="fas fa-briefcase mr-1 text-blue-600"></i> Position applied</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Division *</label>
                                <select name="division" @if($isEdit) required @endif
                                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                    <option value="">{{ $isEdit ? 'Pilih Divisi' : 'Semua Divisi' }}</option>
                                    @foreach($allDivisions as $division)
                                        <option value="{{ $division }}">{{ $division }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Application date *</label>
                                <input type="date" name="application_date" required
                                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            </div>
                        </div>
                    </div>
                </section>

                <section x-show="step === 2" class="space-y-4">
                    <h3 class="mb-2 text-sm font-medium text-gray-700"><i
                            class="fas fa-graduation-cap mr-1 text-blue-600"></i> Riwayat Pendidikan
                        @if(!$isEdit)
                            <span class="text-xs font-normal text-gray-400">(boleh dikosongkan)</span>
                        @endif
                    </h3>
                    <div id="{{ $isEdit ? 'pendidikanRowsEdit' : 'pendidikanRowsAdd' }}" class="space-y-2"
                        data-index="0"></div>
                    <button type="button"
                        @click="window.tambahPendidikan?.('{{ $isEdit ? 'pendidikanRowsEdit' : 'pendidikanRowsAdd' }}')"
                        class="text-sm text-blue-600 hover:text-blue-800"><i class="fas fa-plus mr-1"></i> Tambah
                        Pendidikan</button>
                </section>

                <section x-show="step === 3" class="space-y-4">
                    <div>
                        <h3 class="mb-2 text-sm font-medium text-gray-700"><i
                                class="fas fa-file-alt mr-1 text-blue-600"></i> Dokumen
                            @if($isEdit)
                                <span class="text-xs font-normal text-gray-400">(kosongkan kalau tidak diganti)</span>
                            @endif
                        </h3>
                        <div class="space-y-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Foto <span
                                        class="text-xs font-normal text-gray-400">{{ $isEdit ? '(kosongkan kalau tidak diganti, maksimal 2 MB)' : '(JPG, PNG, maksimal 2 MB)' }}</span></label>
                                @if($isEdit)
                                    <div id="editFotoWrap" class="mb-1 hidden">
                                        <img id="editFotoImg" class="h-14 w-14 rounded-lg border object-cover">
                                    </div>
                                @endif
                                <input type="file" name="foto" accept="image/*"
                                    class="block w-full text-xs text-gray-500 file:mr-3 file:rounded-lg file:border-0 file:bg-blue-50 file:px-3 file:py-1.5 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Scan KTP</label>
                                @if($isEdit)
                                    <a id="editKtpLink" href="#" target="_blank"
                                        class="hidden text-xs text-blue-600 hover:underline">Lihat KTP saat ini</a>
                                @endif
                                <input type="file" name="scan_ktp" accept="image/*,.pdf"
                                    class="block w-full text-xs text-gray-500 file:mr-3 file:rounded-lg file:border-0 file:bg-blue-50 file:px-3 file:py-1.5 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">CV</label>
                                @if($isEdit)
                                    <a id="editCvLink" href="#" target="_blank"
                                        class="hidden text-xs text-indigo-600 hover:underline">Lihat CV saat ini</a>
                                @endif
                                <input type="file" name="cv" accept=".pdf,.doc,.docx"
                                    class="block w-full text-xs text-gray-500 file:mr-3 file:rounded-lg file:border-0 file:bg-blue-50 file:px-3 file:py-1.5 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="mb-2 text-sm font-medium text-gray-700"><i class="fas fa-cog mr-1 text-blue-600"></i>
                            {{ $isEdit ? 'Pengaturan Lainnya' : 'Opsional' }}
                        </h3>
                        <div class="grid {{ $isEdit ? 'grid-cols-3' : 'grid-cols-2' }} gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">NPWP</label>
                                <input type="text" name="npwp"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">No. BPJS</label>
                                <input type="text" name="no_bpjs"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            @if($isEdit)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status</label>
                                    <select name="status"
                                        class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                        <option value="1">Pending</option>
                                        <option value="2">Accepted</option>
                                        <option value="3">Rejected</option>
                                    </select>
                                </div>
                            @endif
                        </div>
                    </div>
                </section>
            </div>

            <div class="flex shrink-0 items-center justify-between gap-2 border-t px-4 py-3">
                <button type="button" x-show="step > 1"
                    class="rounded-lg bg-gray-100 px-4 py-2 text-sm text-gray-600 hover:bg-gray-200"
                    @click="prevStep()">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </button>
                <div x-show="step === 1" class="w-16"></div>

                <div class="flex gap-2">
                    <button type="button" x-show="step < totalSteps"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700"
                        @click="nextStep()">
                        Berikutnya <i class="fas fa-arrow-right ml-1"></i>
                    </button>
                    <button type="submit" x-show="step === totalSteps"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700">
                        <i class="fas fa-save mr-1"></i> {{ $submitText }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>