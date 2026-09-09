<!-- Filters -->
<div class="bg-white rounded-lg shadow-sm p-4 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <input 
                type="text" 
                id="searchInput"
                placeholder="Cari customer..."
                class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                onkeyup="filterCustomers()"
            />
        </div>
        <div>
            <select id="filterType" onchange="filterCustomers()" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Semua Tipe</option>
                <option value="Personal">Personal</option>
                <option value="Company">Company</option>
            </select>
        </div>
        <div>
            <select id="filterStatus" onchange="filterCustomers()" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Semua Status</option>
                <option value="Lead">Lead</option>
                <option value="Prospect">Prospect</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
            </select>
        </div>
    </div>
</div>