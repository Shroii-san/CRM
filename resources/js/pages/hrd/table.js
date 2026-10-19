const hrdSortState = { column: null, direction: "asc" };

function getFilteredHrdRows() {
    const searchInput = document.getElementById("hrdSearch");
    const statusFilter = document.getElementById("hrdStatusFilter");
    const divisionFilter = document.getElementById("hrdDivisionFilter");
    const rows = Array.from(document.querySelectorAll("tr.hrd-kandidat-row"));
    const query = searchInput?.value.trim().toLocaleLowerCase("id") || "";
    const selectedStatus = statusFilter?.value || "";
    const selectedDivision =
        divisionFilter?.value.toLocaleLowerCase("id") || "";

    return rows.filter((row) => {
        const matchesSearch = (row.dataset.search || "")
            .toLocaleLowerCase("id")
            .includes(query);
        const matchesStatus =
            !selectedStatus || row.dataset.status === selectedStatus;
        const matchesDivision =
            !selectedDivision || row.dataset.division === selectedDivision;
        return matchesSearch && matchesStatus && matchesDivision;
    });
}

function makeHrdPageButton(label, page, options = {}) {
    const button = document.createElement("button");
    button.type = "button";
    button.innerHTML = label;
    button.disabled = Boolean(options.disabled);
    button.setAttribute("aria-label", options.ariaLabel || `Halaman ${page}`);

    const baseClasses =
        "min-w-9 rounded-md border px-3 py-2 text-sm transition-colors";
    button.className = options.active
        ? `${baseClasses} border-indigo-600 bg-indigo-600 text-white`
        : `${baseClasses} border-gray-300 bg-white text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40`;

    if (!button.disabled && !options.active) {
        button.addEventListener("click", () => {
            const currentState = window.hrdCurrentPageState || 1;
            window.hrdCurrentPageState = page;
            renderHrdPagination(currentState);
        });
    }

    return button;
}

function renderHrdPagination() {
    const rows = Array.from(document.querySelectorAll("tr.hrd-kandidat-row"));
    const noDataRow = document.querySelector(".hrd-no-data-row");
    const pageSizeSelect = document.getElementById("hrdPageSize");
    const paginationInfo = document.getElementById("hrdPaginationInfo");
    const pagination = document.getElementById("hrdPagination");
    const filterEmpty = document.getElementById("hrdFilterEmpty");

    const pageSize = Number(pageSizeSelect?.value) || 10;
    const currentPage = Number(window.hrdCurrentPageState || 1);
    const filteredRows = getFilteredHrdRows();
    const totalRows = filteredRows.length;
    const pageCount = Math.max(1, Math.ceil(totalRows / pageSize));
    const safeCurrentPage = Math.min(currentPage, pageCount);
    window.hrdCurrentPageState = safeCurrentPage;

    const startIndex = totalRows ? (safeCurrentPage - 1) * pageSize : 0;
    const endIndex = Math.min(startIndex + pageSize, totalRows);
    const visibleRows = new Set(filteredRows.slice(startIndex, endIndex));

    rows.forEach((row) =>
        row.classList.toggle("hidden", !visibleRows.has(row)),
    );
    if (noDataRow) noDataRow.classList.toggle("hidden", rows.length > 0);
    if (filterEmpty)
        filterEmpty.classList.toggle(
            "hidden",
            totalRows > 0 || rows.length === 0,
        );

    filteredRows.slice(startIndex, endIndex).forEach((row, index) => {
        row.cells[0].textContent = startIndex + index + 1;
    });

    if (paginationInfo) {
        paginationInfo.textContent = totalRows
            ? `Menampilkan ${startIndex + 1} sampai ${endIndex} dari ${totalRows} hasil`
            : "Menampilkan 0 hasil";
    }

    if (!pagination) return;

    pagination.replaceChildren();
    pagination.appendChild(
        makeHrdPageButton(
            '<i class="fas fa-angle-left"></i>',
            Math.max(1, safeCurrentPage - 1),
            {
                disabled: safeCurrentPage === 1,
                ariaLabel: "Halaman sebelumnya",
            },
        ),
    );

    const pageNumbers = new Set([
        1,
        pageCount,
        safeCurrentPage - 1,
        safeCurrentPage,
        safeCurrentPage + 1,
    ]);
    const pages = [...pageNumbers]
        .filter((page) => page >= 1 && page <= pageCount)
        .sort((a, b) => a - b);
    let previousPage = 0;

    pages.forEach((page) => {
        if (previousPage && page - previousPage > 1) {
            const ellipsis = document.createElement("span");
            ellipsis.className = "px-1 text-sm text-gray-400";
            ellipsis.textContent = "...";
            pagination.appendChild(ellipsis);
        }

        pagination.appendChild(
            makeHrdPageButton(String(page), page, {
                active: page === safeCurrentPage,
                ariaLabel: `Halaman ${page}${page === safeCurrentPage ? ", aktif" : ""}`,
            }),
        );
        previousPage = page;
    });

    pagination.appendChild(
        makeHrdPageButton(
            '<i class="fas fa-angle-right"></i>',
            Math.min(pageCount, safeCurrentPage + 1),
            {
                disabled: safeCurrentPage === pageCount,
                ariaLabel: "Halaman berikutnya",
            },
        ),
    );
}

function initHrdTable() {
    const searchInput = document.getElementById("hrdSearch");
    const statusFilter = document.getElementById("hrdStatusFilter");
    const divisionFilter = document.getElementById("hrdDivisionFilter");
    const pageSizeSelect = document.getElementById("hrdPageSize");
    const resetButton = document.getElementById("hrdFilterReset");

    window.hrdCurrentPageState = 1;
    searchInput?.addEventListener("input", () => {
        window.hrdCurrentPageState = 1;
        renderHrdPagination();
    });
    statusFilter?.addEventListener("change", () => {
        window.hrdCurrentPageState = 1;
        renderHrdPagination();
    });
    divisionFilter?.addEventListener("change", () => {
        window.hrdCurrentPageState = 1;
        renderHrdPagination();
    });
    pageSizeSelect?.addEventListener("change", () => {
        window.hrdCurrentPageState = 1;
        renderHrdPagination();
    });
    resetButton?.addEventListener("click", () => {
        if (searchInput) searchInput.value = "";
        if (statusFilter) statusFilter.value = "";
        if (divisionFilter) divisionFilter.value = "";
        window.hrdCurrentPageState = 1;
        renderHrdPagination();
    });

    document.querySelectorAll("[data-sort-column]").forEach((button) => {
        button.addEventListener("click", () => {
            const column = Number(button.dataset.sortColumn);
            const direction =
                hrdSortState.column === column &&
                hrdSortState.direction === "asc"
                    ? "desc"
                    : "asc";
            const tbody = button.closest("table")?.tBodies[0];
            const rows = Array.from(
                tbody?.querySelectorAll("tr.hrd-kandidat-row") || [],
            );

            rows.sort((first, second) => {
                const firstValue =
                    first.cells[column]?.textContent.trim() || "";
                const secondValue =
                    second.cells[column]?.textContent.trim() || "";
                const comparison = firstValue.localeCompare(secondValue, "id", {
                    numeric: true,
                    sensitivity: "base",
                });
                return direction === "asc" ? comparison : -comparison;
            });

            rows.forEach((row) => tbody.appendChild(row));
            hrdSortState.column = column;
            hrdSortState.direction = direction;

            document
                .querySelectorAll("[data-sort-column]")
                .forEach((sortButton) => {
                    const header = sortButton.closest("th");
                    const icon = sortButton.querySelector("i");
                    const active =
                        Number(sortButton.dataset.sortColumn) === column;
                    header.setAttribute(
                        "aria-sort",
                        active
                            ? direction === "asc"
                                ? "ascending"
                                : "descending"
                            : "none",
                    );
                    if (icon) {
                        icon.className = active
                            ? `fas fa-sort-${direction === "asc" ? "up" : "down"} text-blue-600`
                            : "fas fa-sort text-gray-400";
                    }
                });

            window.hrdCurrentPageState = 1;
            renderHrdPagination();
        });
    });

    renderHrdPagination();
}

export { initHrdTable, renderHrdPagination, getFilteredHrdRows };
