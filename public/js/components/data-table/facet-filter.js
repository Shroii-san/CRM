// facet-filter.js

export const createFacetFilter = (selectorMap) => {
    const values = {};
    let onChange = () => {};

    Object.entries(selectorMap).forEach(([field, selector]) => {
        const element = document.querySelector(selector);

        element?.addEventListener("change", (event) => {
            values[field] = event.target.value;

            onChange();
        });
    });

    return {
        getQuery: () => {
            const filters = Object.fromEntries(
                Object.entries(values).filter(
                    ([, value]) =>
                        value !== null && value !== undefined && value !== "",
                ),
            );

            if (Object.keys(filters).length === 0) {
                return {};
            }

            return {
                filter: filters,
            };
        },

        onChange: (callback) => {
            onChange = callback;
        },

        setValues: (newValues) => {
            Object.assign(values, newValues);
        },

        getValues: () => ({
            ...values,
        }),
    };
};
