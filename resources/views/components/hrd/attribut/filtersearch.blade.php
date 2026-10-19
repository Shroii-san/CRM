@props(['divisions' => [], 'placeholder' => 'Cari nama, NIK, atau divisi...'])

@php
    $defaultDivisions = ['Direktur', 'Keuangan', 'Multimedia', 'IT', 'Administrasi'];
    $allDivisions = collect($divisions)->merge($defaultDivisions)->unique()->filter()->values();
@endphp

<!-- Search and Filter Section -->
<div class="px-6 py-2 bg-gray-50 border-b border-gray-200">
    <div class="flex flex-wrap gap-4 items-center">
        <div class="flex flex-col md:flex-row md:items-center gap-3">

            <!-- Search input -->
            <div class="relative flex-1 md:flex-initial md:w-80">
                <input type="text" id="searchInput" data-search
                    placeholder="{{ $placeholder }}" @input="onSearchInput($event)"
                    :value="search"
                    class="w-full pl-10 pr-4 py-2 text-sm bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all" />
                <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-sm"></i>
            </div>

            <!-- Filter Status -->
            <select @change="onFilterChange('status', $event.target.value)"
                class="px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all bg-white">
                <option value="">Semua Status</option>
                <option value="1">Pending</option>
                <option value="2">Accepted</option>
                <option value="3">Rejected</option>
            </select>

            <!-- Filter Divisi -->
            <select @change="onFilterChange('division', $event.target.value)"
                class="px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all bg-white">
                <option value="">Semua Divisi</option>
                @foreach($allDivisions as $division)
                    <option value="{{ $division }}">{{ $division }}</option>
                @endforeach
            </select>

        </div>
    </div>
</div>