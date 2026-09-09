    <div class="fade-in">
        <div class="flex flex-col lg:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5"></i>
                    <input
                        type="text"
                        id="searchInput"
                        placeholder="Cari customer, PIC, atau kategori..."
                        class="pl-10 pr-4 py-3 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
                </div>
            </div>
            <div class="flex gap-3">
                <select id="categoryFilter" class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Kategori</option>
                    <option value="Rumah Sakit">Rumah Sakit</option>
                    <option value="Hotel">Hotel</option>
                    <option value="Cafe & Restaurant">Cafe & Restaurant</option>
                    <option value="Retail">Retail</option>
                </select>
                <select id="provinceFilter" class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Provinsi</option>
                    <option value="DKI Jakarta">DKI Jakarta</option>
                    <option value="Jawa Barat">Jawa Barat</option>
                    <option value="Jawa Timur">Jawa Timur</option>
                </select>
                <button class="flex items-center gap-2 px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                    Filter Lanjut
                </button>
            </div>
        </div>
    </div>