// sort-filter.js

export const createSortFilter = (fieldSelector, dirSelector, defaults = {}) => {
    let sortBy = defaults.field ?? "created_at";

    let sortDir = defaults.dir ?? "desc";

    let onChange = () => {};

    const fieldInput = document.querySelector(fieldSelector);

    const dirInput = document.querySelector(dirSelector);

    fieldInput?.addEventListener("change", (event) => {
        sortBy = event.target.value;

        onChange();
    });

    dirInput?.addEventListener("change", (event) => {
        sortDir = event.target.value;

        onChange();
    });

    return {
        getQuery: () => ({
            sort_by: sortBy,
            sort_dir: sortDir,
        }),

        onChange: (callback) => {
            onChange = callback;
        },

        setSort: (field, direction) => {
            sortBy = field;
            sortDir = direction;
        },
    };
};
