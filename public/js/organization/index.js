// import { createFilterManager } from "../components/data-table/filter-manager";
// import { createSearchFilter } from "../components/data-table/search-filter";
// import { createFacetFilter } from "../components/data-table/facet-filter";
// import { createSortFilter } from "../components/data-table/sort-filter";
// import { createPaginationFilter } from "../components/data-table/pagination-filter";
// import { createDataTable } from "../components/data-table/data-table";

// document.addEventListener("DOMContentLoaded", () => {
//     const search = createSearchFilter("[data-search]");

//     const facets = createFacetFilter({
//         industry_id: '[data-filter="industry_id"]',
//         tier: '[data-filter="tier"]',
//         is_active: '[data-filter="is_active"]',
//     });

//     const sort = createSortFilter('[data-sort="by"]', '[data-sort="dir"]', {
//         field: "created_at",
//         dir: "desc",
//     });

//     const pagination = createPaginationFilter(
//         "[data-per-page]",
//         "[data-pagination]",
//     );

//     const table = createDataTable("organizationTable", [
//         {
//             key: "name",
//         },

//         {
//             key: "industry",
//             render: (row) => row.industry?.name ?? "-",
//         },

//         {
//             key: "tier",
//         },

//         {
//             key: "is_active",
//             render: (row) => (row.is_active ? "Aktif" : "Nonaktif"),
//         },
//     ]);

//     const manager = createFilterManager("/api/organizations", {
//         debounceDelay: 300,

//         onDataChanged: (data) => {
//             table.render(data);

//             pagination.renderPagination(data);

//             pagination.setPageData(data.meta);
//         },
//     });

//     manager
//         .registerFilter("search", search)
//         .registerFilter("facets", facets)
//         .registerFilter("sort", sort)
//         .registerFilter("pagination", pagination);

//     manager.fetchData();
// });

const csrfToken =
    document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content") || "";

function getApiToken() {
    return (
        localStorage.getItem("api_token") ||
        sessionStorage.getItem("api_token") ||
        ""
    );
}

function buildApiHeaders(extraHeaders = {}) {
    const token = getApiToken();
    const headers = {
        Accept: "application/json",
        "X-CSRF-TOKEN": csrfToken,
        "X-Requested-With": "XMLHttpRequest",
        ...extraHeaders,
    };
    if (token) headers.Authorization = `Bearer ${token}`;
    return headers;
}

async function loadOrganization() {
    try {
        const response = await fetch("/api/v1/organizations", {
            method: "GET",
            headers: buildApiHeaders(),
            credentials: "same-origin",
        });

        if (!response.ok) throw new Error(`HTTP error: ${response.status}`);

        if (response.status === 401) {
            localStorage.removeItem("api_token");
            window.location.href = "/login";
        }

        const data = await response.json();
        const customerData = Array.isArray(data)
            ? data
            : data.customers || data.data || [];

        displayOrganizationDataTable(customerData);
    } catch (error) {
        console.error("Error fetching organization data:", error);
    }
}

function displayOrganizationDataTable(datas) {
    const tbody = document.getElementById("organizationTableBody");
    if (!tbody) return;

    if (!datas || datas.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="px-3 py-6 text-center text-gray-500">
                    <i class="fas fa-users text-3xl mb-2"></i>
                    <p class="text-xs">Belum ada data organisasi</p>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = datas
        .map((data, index) => {
            const statusClass =
                data.is_active === true
                    ? "background-color: #d1fae5; color: #065f46;"
                    : "background-color: #fee2e2; color: #991b1b;";

            return `<tr style="border-bottom: 1px solid #e5e7eb; transition: background-color 0.15s;" onmouseover="this.style.backgroundColor='#f9fafb'" onmouseout="this.style.backgroundColor='#ffffff'">
                    <td style="padding: 0.5rem 0.75rem; font-size: 0.8125rem; color: #111827; white-space: nowrap;">
                        <span style="font-weight: 500;">${index + 1}</span>
                    </td>
                    <td style="padding: 0.5rem 0.75rem; font-size: 0.8125rem; font-weight: 500; color: #111827;">
                        ${data.name ?? "-"}
                    </td>
                    <td style="padding: 0.5rem 0.75rem; font-size: 0.8125rem; color: #111827;">
                        ${data.industry?.name ?? "-"}
                    </td>
                    <td style="padding: 0.5rem 0.75rem; font-size: 0.8125rem; color: #111827;">
                        ${data.tier ?? "-"}
                    </td>
                    <td style="padding: 0.5rem 0.75rem; font-size: 0.8125rem; color: #111827;">
                        ${data.description ?? "-"}
                    </td>
                    <td style="padding: 0.5rem 0.75rem; white-space: nowrap;">
                        <span style="display: inline-flex; align-items: center; padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 500; ${statusClass}">
                            ${data.is_active === true ? "Active" : "Inactive"}
                        </span>
                    </td>
                    <td style="padding: 0.5rem 0.75rem; text-align: right;">
                        <!-- Action buttons here -->
                    </td>
                </tr>`;
        })
        .join("");
}

document.addEventListener("DOMContentLoaded", function () {
    loadOrganization();
});
