const hrdKeluargaState = { karyawanId: null, records: [] };

function hrdKeluargaStatusClass(status) {
    return status === "Suami"
        ? "bg-blue-100 text-blue-800"
        : status === "Istri"
          ? "bg-pink-100 text-pink-800"
          : "bg-amber-100 text-amber-800";
}

function hrdFormatTanggal(value) {
    const normalized = window.normalisasiTanggal(value);
    return normalized
        ? normalized.split("-").reverse().join("/")
        : value || "-";
}

function renderHrdKeluargaList() {
    const list = document.getElementById("hrdKeluargaList");
    if (!list) return;

    const csrfToken =
        document.querySelector('meta[name="csrf-token"]')?.content || "";

    if (!hrdKeluargaState.records.length) {
        list.innerHTML =
            '<div class="rounded-lg border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500">Belum ada data keluarga.</div>';
        return;
    }

    list.innerHTML = hrdKeluargaState.records
        .map(
            (record) => `
        <div class="flex items-center justify-between gap-3 rounded-lg border border-gray-200 bg-white p-3 shadow-sm">
            <div class="min-w-0">
                <div class="text-sm font-medium text-gray-800">${window.escapeHtml(record.nama)}</div>
                <div class="text-xs text-gray-500">${window.escapeHtml(record.tempat_lahir)}, ${hrdFormatTanggal(record.tanggal_lahir)} &middot; ${window.hitungUsia(record.tanggal_lahir)}</div>
            </div>
            <div class="flex shrink-0 items-center gap-3">
                <span class="rounded-full px-2 py-1 text-xs font-medium ${hrdKeluargaStatusClass(record.status)}">${window.escapeHtml(record.status)}</span>
                <button type="button" onclick="editHrdKeluarga(${record.id})" class="text-blue-600 hover:text-blue-800" title="Edit"><i class="fas fa-edit"></i></button>
                <form action="/keluarga/${record.id}" method="POST" class="inline" onsubmit="openHrdKeluargaDelete(event, this)">
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus"><i class="fas fa-trash"></i></button>
                </form>
            </div>
        </div>
    `,
        )
        .join("");
}

function resetHrdKeluargaForm() {
    const form = document.getElementById("hrdKeluargaForm");
    if (!form) return;

    form.reset();
    form.action = `/karyawan/${hrdKeluargaState.karyawanId}/keluarga`;
    document.getElementById("hrdKeluargaMethod").value = "POST";
    document.getElementById("hrdKeluargaFormTitle").innerHTML =
        '<i class="fas fa-plus-circle mr-2"></i>Tambah Anggota Keluarga';
    document.getElementById("hrdKeluargaUsia").value = "";
}

function getAlpineData(el) {
    if (!el) return null;
    if (window.Alpine && typeof window.Alpine.$data === "function") {
        return window.Alpine.$data(el);
    }
    return el._x_dataStack?.[0] || null;
}

function openHrdKeluargaModal(buttonOrItem) {
    if (buttonOrItem instanceof HTMLElement) {
        hrdKeluargaState.karyawanId = buttonOrItem.dataset.karyawanId;
        try {
            hrdKeluargaState.records = JSON.parse(buttonOrItem.dataset.keluarga || "[]");
        } catch (error) {
            hrdKeluargaState.records = [];
        }
        const pemilik = document.getElementById("hrdKeluargaPemilik");
        if (pemilik)
            pemilik.textContent = `Karyawan: ${buttonOrItem.dataset.karyawanNama}`;
    } else {
        const item = buttonOrItem || {};
        const emp = item.employee || item;
        hrdKeluargaState.karyawanId = emp.id;
        hrdKeluargaState.records = emp.families || item.families || [];
        const pemilik = document.getElementById("hrdKeluargaPemilik");
        if (pemilik)
            pemilik.textContent = `Karyawan: ${item.full_name || emp.full_name || ''}`;
    }

    resetHrdKeluargaForm();
    renderHrdKeluargaList();

    const modal = document.getElementById("hrdKeluargaModal");
    const data = getAlpineData(modal);
    if (data) {
        data.open = true;
    }
    modal?.classList.remove("hidden");
    if (modal) modal.style.display = "flex";
}

function openHrdKeluargaFormModal() {
    const modal = document.getElementById("hrdKeluargaFormModal");
    const data = getAlpineData(modal);
    if (data) {
        data.open = true;
    }
    modal?.classList.remove("hidden");
    if (modal) modal.style.display = "flex";
}

function editHrdKeluarga(id) {
    const record = hrdKeluargaState.records.find((item) => item.id === id);
    if (!record) return;

    document.getElementById("hrdKeluargaForm").action = `/keluarga/${id}`;
    document.getElementById("hrdKeluargaMethod").value = "PUT";
    document.getElementById("hrdKeluargaFormTitle").innerHTML =
        '<i class="fas fa-edit mr-2"></i>Edit Anggota Keluarga';
    document.getElementById("hrdKeluargaNama").value = record.nama;
    document.getElementById("hrdKeluargaTempat").value = record.tempat_lahir;
    document.getElementById("hrdKeluargaTanggal").value =
        window.normalisasiTanggal(record.tanggal_lahir);
    document.getElementById("hrdKeluargaUsia").value = window.hitungUsia(
        record.tanggal_lahir,
    );
    document.getElementById("hrdKeluargaStatus").value = record.status;

    openHrdKeluargaFormModal();
}

function closeHrdKeluargaForm() {
    const modal = document.getElementById("hrdKeluargaFormModal");
    const data = getAlpineData(modal);
    if (data) {
        data.open = false;
    }
    modal?.classList.add("hidden");
    if (modal) modal.style.display = "none";
}

function closeHrdKeluargaModal() {
    const modal = document.getElementById("hrdKeluargaModal");
    const data = getAlpineData(modal);
    if (data) {
        data.open = false;
    }
    modal?.classList.add("hidden");
    if (modal) modal.style.display = "none";
    closeHrdKeluargaForm();
}

function showHrdKeluargaToast(message, type = "success") {
    const toast = document.getElementById("hrdKeluargaToast");
    if (!toast) return;

    toast.textContent = message;
    toast.className =
        type === "success"
            ? "mx-5 mt-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 opacity-0 -translate-y-2 transition-all duration-300"
            : "mx-5 mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 opacity-0 -translate-y-2 transition-all duration-300";
    requestAnimationFrame(() =>
        toast.classList.remove("opacity-0", "-translate-y-2"),
    );
    clearTimeout(window.hrdKeluargaToastTimer);
    window.hrdKeluargaToastTimer = setTimeout(() => {
        toast.classList.add("opacity-0", "-translate-y-2");
        setTimeout(() => toast.classList.add("hidden"), 300);
    }, 3500);
}

async function readHrdKeluargaResponse(response, fallback) {
    let result = {};
    try {
        result = await response.json();
    } catch (error) {
        // response is not JSON
    }

    if (response.status === 401) {
        throw new Error("Sesi login sudah berakhir. Silakan login kembali.");
    }

    if (!response.ok) {
        const validation = result.errors
            ? Object.values(result.errors).flat().join(" ")
            : "";
        throw new Error(result.message || validation || fallback);
    }

    return result;
}

let hrdKeluargaDeleteForm = null;

function openHrdKeluargaDelete(event, form) {
    event.preventDefault();
    hrdKeluargaDeleteForm = form;

    const modal = document.getElementById("hrdKeluargaDeleteModal");
    const data = getAlpineData(modal);
    if (data) {
        data.open = true;
    }
    modal?.classList.remove("hidden");
    if (modal) modal.style.display = "flex";
}

function closeHrdKeluargaDelete() {
    const modal = document.getElementById("hrdKeluargaDeleteModal");
    const data = getAlpineData(modal);
    if (data) {
        data.open = false;
    }
    modal?.classList.add("hidden");
    if (modal) modal.style.display = "none";
    hrdKeluargaDeleteForm = null;
}

async function confirmHrdKeluargaDelete() {
    const form = hrdKeluargaDeleteForm;
    if (!form) return;

    closeHrdKeluargaDelete();

    try {
        const response = await fetch(form.action, {
            method: "POST",
            body: new FormData(form),
            headers: { Accept: "application/json" },
        });

        await readHrdKeluargaResponse(
            response,
            "Gagal menghapus data keluarga.",
        );
        hrdKeluargaState.records = hrdKeluargaState.records.filter(
            (record) => `/keluarga/${record.id}` !== form.action,
        );
        renderHrdKeluargaList();
        showHrdKeluargaToast("Data keluarga berhasil dihapus.");
        if (typeof window.refreshHrdTable === 'function') {
            window.refreshHrdTable();
        }
    } catch (error) {
        showHrdKeluargaToast(error.message, "error");
    }
}

function initHrdFamily() {
    const setup = () => {
        const familyDateInput = document.getElementById("hrdKeluargaTanggal");
        familyDateInput?.addEventListener("input", function () {
            document.getElementById("hrdKeluargaUsia").value =
                window.hitungUsia(this.value);
            this.setCustomValidity(
                window.normalisasiTanggal(this.value)
                    ? ""
                    : "Gunakan format tanggal DD/MM/YYYY yang valid.",
            );
        });

        const familyForm = document.getElementById("hrdKeluargaForm");
        familyForm?.addEventListener("submit", async function (event) {
            event.preventDefault();
            const input = document.getElementById("hrdKeluargaTanggal");
            const normalized = window.normalisasiTanggal(input.value);
            input.setCustomValidity(
                normalized
                    ? ""
                    : "Gunakan format tanggal DD/MM/YYYY yang valid.",
            );
            if (!normalized || !this.checkValidity()) {
                this.reportValidity();
                return;
            }

            input.value = normalized;

            try {
                const response = await fetch(this.action, {
                    method: "POST",
                    body: new FormData(this),
                    headers: { Accept: "application/json" },
                });
                const result = await readHrdKeluargaResponse(
                    response,
                    "Data keluarga gagal disimpan.",
                );

                const saved = result.data;
                const savedRecord = {
                    id: saved.id,
                    nama: saved.nama,
                    tempat_lahir: saved.tempat_lahir,
                    tanggal_lahir: saved.tanggal_lahir,
                    status: saved.status,
                };
                const existingIndex = hrdKeluargaState.records.findIndex(
                    (record) => record.id === savedRecord.id,
                );
                if (existingIndex >= 0)
                    hrdKeluargaState.records[existingIndex] = savedRecord;
                else hrdKeluargaState.records.unshift(savedRecord);

                renderHrdKeluargaList();
                closeHrdKeluargaForm();
                showHrdKeluargaToast(
                    existingIndex >= 0
                        ? "Data keluarga berhasil diperbarui."
                        : "Data keluarga berhasil ditambahkan.",
                );
                if (typeof window.refreshHrdTable === 'function') {
                    window.refreshHrdTable();
                }
            } catch (error) {
                showHrdKeluargaToast(error.message, "error");
            }
        });

        document
            .getElementById("hrdKeluargaModal")
            ?.addEventListener("click", (event) => {
                if (event.target.id === "hrdKeluargaModal")
                    closeHrdKeluargaModal();
            });
        document
            .getElementById("hrdKeluargaFormModal")
            ?.addEventListener("click", (event) => {
                if (event.target.id === "hrdKeluargaFormModal")
                    closeHrdKeluargaForm();
            });
        document
            .getElementById("hrdKeluargaDeleteModal")
            ?.addEventListener("click", (event) => {
                if (event.target.id === "hrdKeluargaDeleteModal")
                    closeHrdKeluargaDelete();
            });
    };

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", setup);
    } else {
        setup();
    }
}

window.openHrdKeluargaModal = openHrdKeluargaModal;
window.openHrdKeluargaFormModal = openHrdKeluargaFormModal;
window.editHrdKeluarga = editHrdKeluarga;
window.closeHrdKeluargaModal = closeHrdKeluargaModal;
window.closeHrdKeluargaForm = closeHrdKeluargaForm;
window.closeHrdKeluargaFormModal = closeHrdKeluargaForm;
window.openHrdKeluargaDelete = openHrdKeluargaDelete;
window.closeHrdKeluargaDelete = closeHrdKeluargaDelete;
window.closeHrdKeluargaDeleteModal = closeHrdKeluargaDelete;
window.confirmHrdKeluargaDelete = confirmHrdKeluargaDelete;
window.resetHrdKeluargaForm = resetHrdKeluargaForm;

export {
    initHrdFamily,
    renderHrdKeluargaList,
    openHrdKeluargaModal,
    closeHrdKeluargaModal,
    openHrdKeluargaFormModal,
    closeHrdKeluargaForm,
    editHrdKeluarga,
    confirmHrdKeluargaDelete,
};
