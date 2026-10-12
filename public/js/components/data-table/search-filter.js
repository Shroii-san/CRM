// resources/js/components/data-table/search-filter.js

export const createSearchFilter = (selector) => {
    let searchTerm = "";
    let onChange = () => {};

    const input = document.querySelector(selector);

    input?.addEventListener("input", (event) => {
        searchTerm = event.target.value;

        onChange();
    });

    return {
        getQuery: () => {
            if (!searchTerm) {
                return {};
            }

            return {
                search: searchTerm,
            };
        },

        onChange: (callback) => {
            onChange = callback;
        },

        setValue: (value) => {
            searchTerm = value;

            if (input) {
                input.value = value;
            }
        },
    };
};
