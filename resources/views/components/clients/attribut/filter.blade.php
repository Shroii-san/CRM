<!-- Search and Filter Section -->
<div style="padding: 0.5rem 1.5rem; background-color: #f9fafb; border-bottom: 1px solid #e5e7eb;">
    <div style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: center;">
        <div class="flex flex-col md:flex-row md:items-center gap-3">

            <!-- Search input -->
            <div class="relative flex-1 md:flex-initial md:w-80">
                <input type="text" id="searchInput" data-search
                    placeholder="{{ $placeholder ?? 'Cari data clients...' }}" @input="onSearchInput($event)"
                    :value="search"
                    class="w-full pl-10 pr-4 py-2 text-sm bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all" />
                <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-sm"></i>
            </div>

            <!-- Filter Jenis Client -->
            <select @change="onFilterChange('client_type', $event.target.value)"
                class="px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all bg-white">
                <option value="">Semua Tipe</option>
                <option value="person">Personal</option>
                <option value="organization">Company</option>
            </select>

            <!-- Filter Sumber -->
            <select @change="onFilterChange('source_id', $event.target.value)"
                class="px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all bg-white">
                <option value="">Semua Sumber</option>
                @foreach($sources as $source)
                    <option value="{{ $source->id }}">{{ $source->name }}</option>
                @endforeach
            </select>

            <!-- Filter Status -->
            <select @change="onFilterChange('is_active', $event.target.value)"
                class="px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all bg-white">
                <option value="">Semua Status</option>
                <option value="true">Aktif</option>
                <option value="false">Nonaktif</option>
            </select>

        </div>
    </div>
</div>