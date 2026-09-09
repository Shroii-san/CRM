@props([
    'tableId',
    'searchFields' => [],
    'showRoleFilter' => false,
    'roles' => [],
    'ajaxUrl' => null, // URL untuk AJAX search
])

<div class="bg-white rounded-xl shadow-sm p-6 mb-4 border border-gray-200">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
        <div class="flex items-center space-x-4">
            <div class="relative">
                <input
                    type="text"
                    id="{{ $tableId }}SearchInput"
                    placeholder="Search..."
                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none"
                />
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>
            @if($showRoleFilter)
            <select id="{{ $tableId }}RoleFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none">
                <option value="">All Roles</option>
                @foreach($roles as $role)
                    <option value="{{ strtolower($role->role_name) }}">{{ $role->role_name }}</option>
                @endforeach
            </select>
            @endif
        </div>
        <div class="flex items-center space-x-2">
            {{ $slot }}
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tableId = '{{ $tableId }}';
    const ajaxUrl = '{{ $ajaxUrl }}';
    const input = document.getElementById(tableId + 'SearchInput');
    const roleFilter = document.getElementById(tableId + 'RoleFilter');
    let debounceTimer;

    if(input) {
        input.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                fetchData(1);
            }, 500); // Delay 500ms setelah user selesai ketik
        });
    }

    if(roleFilter) {
        roleFilter.addEventListener('change', function() {
            fetchData(1);
        });
    }

    // Pagination click handler
    document.addEventListener('click', function(e) {
        if(e.target.matches('.pagination-link')) {
            e.preventDefault();
            const page = e.target.dataset.page;
            fetchData(page);
        }
    });

    function fetchData(page = 1) {
        const searchTerm = input ? input.value : '';
        const selectedRole = roleFilter ? roleFilter.value : '';

        // Show loading
        const tbody = document.querySelector('#' + tableId + ' tbody');
        tbody.innerHTML = '<tr><td colspan="8" class="text-center py-8"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>';

        // Fetch data via AJAX
        fetch(ajaxUrl + '?search=' + encodeURIComponent(searchTerm) + '&role=' + encodeURIComponent(selectedRole) + '&page=' + page)
            .then(response => response.json())
            .then(data => {
                renderTable(data.users, data.pagination);
                renderPagination(data.pagination);
            })
            .catch(error => {
                console.error('Error:', error);
                tbody.innerHTML = '<tr><td colspan="8" class="text-center py-8 text-red-600">Error loading data</td></tr>';
            });
    }

    function renderTable(users, pagination) {
        const tbody = document.querySelector('#' + tableId + ' tbody');
        
        if(users.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center py-8 text-gray-500">No data found</td></tr>';
            return;
        }

        let html = '';
        users.forEach((user, index) => {
            const rowNum = pagination.from + index;
            const roleClass = user.role ? user.role.role_name.toLowerCase() : 'no-role';
            
           html += `
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm text-gray-900">${rowNum}</td>
                <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900">${user.username}</div>
                    <div class="text-sm text-gray-500">${user.email || '-'}</div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-900">${user.phone || '-'}</td>
                <td class="px-6 py-4 text-sm text-gray-900">${user.birth_date || '-'}</td>
                <td class="px-6 py-4 text-sm text-gray-900">${user.address || '-'}</td>
                <td class="px-6 py-4">
                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        ${user.role ? user.role.role_name : 'No Role'}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <span class="px-3 py-1 rounded-full text-xs font-medium ${user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                        ${user.is_active ? 'Active' : 'Inactive'}
                    </span>
                </td>
                <td class="px-6 py-4 flex space-x-2">
                    <button onclick="openEditModal('${user.user_id}', '${user.username}', '${user.email}', '${user.role_id}', ${user.is_active}, '${user.phone || ''}', '${user.birth_date || ''}', \`${user.address || ''}\`, '${user.province_id || ''}', '${user.regency_id || ''}', '${user.district_id || ''}', '${user.village_id || ''}')" class="text-blue-600 hover:text-blue-900 p-2 rounded-lg hover:bg-blue-50">
                        <i class="fas fa-edit"></i>
                    </button>
                    <form action="/users/${user.user_id}" method="POST" onsubmit="return confirm('Yakin mau hapus?')" class="inline">
                        <input type="hidden" name="_token" value="${window.Laravel.csrfToken}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="text-red-600 hover:text-red-900 p-2">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
        `;

        });
        
        tbody.innerHTML = html;
    }

    function renderPagination(pagination) {
        const paginationContainer = document.querySelector('.pagination-container');
        if(!paginationContainer) return;

        let html = '<div class="flex justify-center space-x-2 mt-4">';
        
        // Previous button
        if(pagination.current_page > 1) {
            html += `<button class="pagination-link px-3 py-1 border rounded" data-page="${pagination.current_page - 1}">Previous</button>`;
        }
        
        // Page numbers
        for(let i = 1; i <= pagination.last_page; i++) {
            const active = i === pagination.current_page ? 'bg-blue-600 text-white' : 'bg-white';
            html += `<button class="pagination-link px-3 py-1 border rounded ${active}" data-page="${i}">${i}</button>`;
        }
        
        // Next button
        if(pagination.current_page < pagination.last_page) {
            html += `<button class="pagination-link px-3 py-1 border rounded" data-page="${pagination.current_page + 1}">Next</button>`;
        }
        
        html += '</div>';
        paginationContainer.innerHTML = html;
    }
});
</script>