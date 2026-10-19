import { createDataTable } from "../components/dataTable.js";
import { getCandidates, deleteCandidate } from "../api/hrd.js";
import { initHrdForms } from "./hrd/forms.js";
import { initHrdFamily } from "./hrd/family.js";

// Definisi Kolom untuk Tabel HRD
const hrdColumns = [
    {
        key: "no",
        label: "No",
        render: (row, index) => index + 1,
    },
    {
        key: "full_name",
        label: "Nama",
        sortable: true,
        render: (row) => row.full_name || "-",
    },
    {
        key: "nik",
        label: "NIK",
        sortable: true,
        render: (row) => row.nik || "-",
    },
    {
        key: "division",
        label: "Divisi",
        sortable: true,
        render: (row) => row.division || "-",
    },
    {
        key: "foto",
        label: "Foto",
    },
    {
        key: "scan_ktp",
        label: "KTP",
    },
    {
        key: "cv",
        label: "CV",
    },
    {
        key: "status",
        label: "Status",
        sortable: true,
        render: (row) => row.status_label || "-",
    },
    {
        key: "keluarga",
        label: "Keluarga",
    },
    {
        key: "actions",
        label: "Aksi",
    },
];

function buildTableComponent() {
    const baseTable = createDataTable({
        fetchData: getCandidates,
        columns: hrdColumns,
        defaultSort: { field: "created_at", dir: "desc" },
        perPage: 10,
        perPageOptions: [10, 25, 50, 100],
    });

    return {
        ...baseTable,

        dispatchWindowEvent(eventName, detail = {}) {
            if (typeof window === "undefined") return;
            window.dispatchEvent(new CustomEvent(eventName, { detail }));
        },

        openAddModal() {
            this.dispatchWindowEvent("open-modal");
        },

        previewPhoto(url, name) {
            this.dispatchWindowEvent("open-photo-preview", { url, name });
        },

        handleOpenEdit(item) {
            this.dispatchWindowEvent("open-edit-modal", item || {});
        },

        handleOpenPromote(item) {
            const payload = {
                url:
                    item?.url ||
                    item?.action ||
                    item?.promoteUrl ||
                    (item?.id ? `/hrd/${item.id}/promote` : ""),
                nama: item?.nama || item?.full_name || "",
            };
            this.dispatchWindowEvent("open-karyawan-modal", payload);
        },

        handleOpenKeluarga(item) {
            if (typeof window.openHrdKeluargaModal === "function") {
                window.openHrdKeluargaModal(item);
            }
        },

        async handleDelete(candidateId) {
            if (typeof Swal !== "undefined") {
                const result = await Swal.fire({
                    title: "Hapus Data Calon Karyawan?",
                    text: "Data yang dihapus tidak dapat dikembalikan.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#ef4444",
                    cancelButtonColor: "#6b7280",
                    confirmButtonText: "Ya, Hapus!",
                    cancelButtonText: "Batal",
                });
                if (!result.isConfirmed) return;
            } else {
                if (!confirm("Yakin ingin menghapus calon karyawan ini?"))
                    return;
            }

            try {
                await deleteCandidate(candidateId);
                if (typeof Swal !== "undefined") {
                    Swal.fire({
                        toast: true,
                        position: "top-end",
                        icon: "success",
                        title: "Data calon karyawan berhasil dihapus",
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });
                } else {
                    alert("Data calon karyawan berhasil dihapus");
                }
                this.refresh();
            } catch (err) {
                console.error("Error deleting candidate:", err);
                const msg =
                    err?.payload?.message ||
                    err?.message ||
                    "Gagal menghapus calon karyawan";
                if (typeof Swal !== "undefined") {
                    Swal.fire({
                        icon: "error",
                        title: "Gagal",
                        text: msg,
                    });
                } else {
                    alert(msg);
                }
            }
        },
    };
}

// Registrasi komponen Alpine hrdTable secara aman
function registerComponent() {
    if (window.Alpine && typeof window.Alpine.data === "function") {
        window.Alpine.data("hrdTable", buildTableComponent);
    }
}

document.addEventListener("alpine:init", registerComponent);

if (window.Alpine) {
    registerComponent();
}

// Global helpers
window.refreshHrdTable = () => {
    const rootEl = document.querySelector('[x-data="hrdTable"]');
    if (rootEl && window.Alpine) {
        const data = window.Alpine.$data(rootEl);
        if (data && typeof data.refresh === "function") {
            data.refresh();
        }
    }
};

document.addEventListener("DOMContentLoaded", () => {
    initHrdForms();
    initHrdFamily();
});
