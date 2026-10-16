// data-table.js

export const createDataTable = (containerId, columns) => {
    const container = document.getElementById(containerId);

    return {
        render: (data) => {
            const tbody = container?.querySelector("tbody");

            if (!tbody) {
                return;
            }

            tbody.innerHTML = "";

            data.data?.forEach((row) => {
                const tr = document.createElement("tr");

                columns.forEach((column) => {
                    const td = document.createElement("td");

                    td.innerHTML = column.render
                        ? column.render(row)
                        : (row[column.key] ?? "");

                    tr.appendChild(td);
                });

                tbody.appendChild(tr);
            });
        },
    };
};
