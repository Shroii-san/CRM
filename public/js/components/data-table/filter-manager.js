// resources/js/components/data-table/filter-manager.js

export const createFilterManager = (apiEndpoint, options = {}) => {
    const filters = {};
    let currentQuery = {};
    let debounceTimer = null;

    const flattenParams = (obj, prefix = "") => {
        const result = {};

        for (const [key, value] of Object.entries(obj)) {
            const newKey = prefix ? `${prefix}[${key}]` : key;

            if (value !== null && value !== undefined && value !== "") {
                result[newKey] = value;
            }
        }

        return result;
    };

    const buildQuery = () => {
        const query = {};

        Object.values(filters).forEach((filter) => {
            Object.assign(query, filter.getQuery());
        });

        currentQuery = query;

        return query;
    };

    const fetchData = async () => {
        const query = buildQuery();

        const params = new URLSearchParams(flattenParams(query));

        const url = `${apiEndpoint}?${params.toString()}`;

        try {
            const response = await fetch(url);

            if (!response.ok) {
                throw new Error(`HTTP error: ${response.status}`);
            }

            const data = await response.json();

            options.onDataChanged?.(data);

            return data;
        } catch (error) {
            console.error("Failed to fetch data:", error);
            options.onError?.(error);

            throw error;
        }
    };

    const notifyChange = () => {
        clearTimeout(debounceTimer);

        debounceTimer = setTimeout(() => {
            fetchData();
        }, options.debounceDelay ?? 300);
    };

    const registerFilter = (name, filter) => {
        filters[name] = filter;

        filter.onChange(notifyChange);

        return manager;
    };

    const manager = {
        registerFilter,
        fetchData,
        buildQuery,
        getQuery: () => ({ ...currentQuery }),
    };

    return manager;
};
