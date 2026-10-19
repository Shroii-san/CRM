import { debounce } from "../utils/debounce.js";
import { getNestedValue } from "../utils/getNestedValue.js";

/**
 * Factory untuk membuat dataTable generic yang kompatibel dengan Alpine.js (x-data).
 *
 * @param {Object} options Configuration options
 * @param {Function} options.fetchData Fungsi async untuk mengambil data (menerima query object)
 * @param {Array} options.columns Definisi kolom (key, label, sortable, render)
 * @param {Object} options.defaultSort Initial sort { field: 'id', dir: 'asc' }
 * @param {number} options.perPage Default item per halaman
 * @param {Array} options.perPageOptions Opsi jumlah item per halaman (default: [10, 25, 75, 100])
 * @param {Object} options.initialFilters Initial filter values
 * @returns {Object} Alpine x-data component object
 */
export function createDataTable(options = {}) {
    const {
        fetchData,
        columns = [],
        defaultSort = { field: "id", dir: "asc" },
        perPage = 10,
        perPageOptions = [10, 25, 75, 100],
        initialFilters = {},
    } = options;

    return {
        // State
        search: "",
        page: 1,
        perPage: perPage,
        perPageOptions: perPageOptions,
        sortBy: defaultSort.field || "id",
        sortDir: defaultSort.dir || "asc",
        filters: { ...initialFilters },

        items: [],
        columns: columns,
        loading: false,
        error: null,

        meta: {
            current_page: 1,
            last_page: 1,
            from: 0,
            to: 0,
            total: 0,
            per_page: perPage,
        },

        // Debounced search handler (dibuat saat init)
        _debouncedSearch: null,

        /**
         * Inisialisasi komponen Alpine
         */
        init() {
            this._debouncedSearch = debounce(() => {
                this.page = 1;
                this.load();
            }, 300);

            this.load();
        },

        /**
         * Memuat data dengan menyusun query object dan memanggil fetchData()
         */
        async load() {
            if (typeof fetchData !== "function") {
                console.error("[dataTable] fetchData function is required.");
                return;
            }

            this.loading = true;
            this.error = null;

            const query = {
                search: this.search || undefined,
                page: this.page,
                per_page: this.perPage,
                sort_by: this.sortBy,
                sort_dir: this.sortDir,
                ...this.filters,
            };

            try {
                const response = await fetchData(query);

                let rawData = [];
                let paginationMeta = {};

                if (Array.isArray(response)) {
                    rawData = response;
                } else if (response && Array.isArray(response.data)) {
                    rawData = response.data;
                    paginationMeta = response.meta || {
                        current_page: response.current_page || 1,
                        last_page: response.last_page || 1,
                        from: response.from || (rawData.length ? 1 : 0),
                        to: response.to || rawData.length,
                        total: response.total || rawData.length,
                        per_page: response.per_page || this.perPage,
                    };
                } else if (response && Array.isArray(response.customers)) {
                    rawData = response.customers;
                }

                this.items = rawData;
                this.meta = {
                    current_page: paginationMeta.current_page || this.page,
                    last_page: paginationMeta.last_page || 1,
                    from: paginationMeta.from || (rawData.length ? 1 : 0),
                    to: paginationMeta.to || rawData.length,
                    total: paginationMeta.total || rawData.length,
                    per_page: paginationMeta.per_page || this.perPage,
                };
            } catch (err) {
                console.error("[dataTable] Error loading data:", err);
                this.error = err.message || "Gagal memuat data";
                this.items = [];
            } finally {
                this.loading = false;
            }
        },

        // Event Handlers
        onSearchInput(event) {
            this.search = event.target.value;
            if (this._debouncedSearch) {
                this._debouncedSearch();
            }
        },

        onPerPageChange(event) {
            const newPerPage = parseInt(event.target.value, 10);
            if (!isNaN(newPerPage)) {
                this.perPage = newPerPage;
            }
            this.page = 1;
            this.load();
        },

        onFilterChange(name, value) {
            this.filters[name] = value;
            this.page = 1;
            this.load();
        },

        onSort(key) {
            if (this.sortBy === key) {
                this.sortDir = this.sortDir === "asc" ? "desc" : "asc";
            } else {
                this.sortBy = key;
                this.sortDir = "asc";
            }
            this.load();
        },

        setPage(newPage) {
            if (newPage < 1 || newPage > this.meta.last_page) return;
            this.page = newPage;
            this.load();
        },

        refresh() {
            this.load();
        },

        /**
         * Mengambil nilai sel untuk kolom tertentu dari item row
         */
        renderCell(row, col, index) {
            if (typeof col.render === "function") {
                return col.render(row, index);
            }
            return getNestedValue(row, col.key, "-");
        },
    };
}
