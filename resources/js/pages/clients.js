import { createDataTable } from "../components/dataTable.js";
import { getClients, getClient, deleteClient } from "../api/clients.js";
import { getNestedValue } from "../utils/getNestedValue.js";

// Definisi Kolom untuk Tabel Client
const clientColumns = [
    {
        key: "no",
        label: "No",
        render: (row, index) => index + 1,
    },
    {
        key: "client_type",
        label: "tipe Client",
        render: (row) => getNestedValue(row, "client_type.name", "-"),
    },
    {
        key: "name",
        label: "Nama Client",
        sortable: true,
        render: (row) => row.name || "-",
    },
    {
        key: "source",
        label: "source",
        sortable: true,
        render: (row) => row.source || "-",
    },
    {
        key: "is_active",
        label: "Status",
        sortable: true,
        render: (row) => (row.is_active ? "Active" : "Inactive"),
    },
    {
        key: "actions",
        label: "Aksi",
    },
];

function buildTableComponent() {
    const baseTable = createDataTable({
        fetchData: getClients,
        columns: clientColumns,
        defaultSort: { field: "created_at", dir: "desc" },
        perPage: 10,
    });

    return baseTable;
}

// Registrasi komponen Alpine clientTable secara aman
function registerComponent() {
    if (window.Alpine && typeof window.Alpine.data === "function") {
        window.Alpine.data("clientTable", buildTableComponent);
    }
}

document.addEventListener("alpine:init", registerComponent);

if (window.Alpine) {
    registerComponent();
}

// Expose global helper untuk trigger detail dari luar scope Alpine (misal: onclick di PHP)
window.showClientDetail = (id) => {
    const rootEl = document.querySelector('[x-data="clientTable"]');
    if (rootEl && window.Alpine) {
        const data = window.Alpine.$data(rootEl);
        if (data && typeof data.showDetail === "function") {
            data.showDetail(id);
        }
    }
};
