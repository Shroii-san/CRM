// pagination-filter.js

export const createPaginationFilter = (perPageSelector, paginationSelector) => {
    let currentPage = 1;
    let perPage = 50;

    let onChange = () => {};

    const perPageInput = document.querySelector(perPageSelector);

    const container = document.querySelector(paginationSelector);

    perPageInput?.addEventListener("change", (event) => {
        perPage = parseInt(event.target.value, 10);

        currentPage = 1;

        onChange();
    });

    container?.addEventListener("click", (event) => {
        const button = event.target.closest("[data-page]");

        if (!button) {
            return;
        }

        currentPage = parseInt(button.dataset.page, 10);

        onChange();
    });

    return {
        getQuery: () => ({
            page: currentPage,
            per_page: perPage,
        }),

        onChange: (callback) => {
            onChange = callback;
        },

        setPageData: (meta) => {
            currentPage = meta.current_page;
        },

        renderPagination: (data) => {
            if (!container || !data.meta) {
                return;
            }

            container.innerHTML = "";

            for (let page = 1; page <= data.meta.last_page; page++) {
                const button = document.createElement("button");

                button.textContent = page;
                button.dataset.page = page;

                if (page === data.meta.current_page) {
                    button.classList.add("active");
                }

                container.appendChild(button);
            }
        },
    };
};
