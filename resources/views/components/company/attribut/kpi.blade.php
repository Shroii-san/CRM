<div class="pt-2 max-w-7xl mx-auto">
    <!-- KPI Cards Company - Mini Version -->
    <div class="mt-2 mb-0">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">

            <!-- Total Company -->
            <div class="bg-white rounded-lg shadow-sm p-3 border-l-4 border-blue-500 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-building text-blue-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-3 min-w-0 flex-1">
                        <p class="text-[10px] font-medium text-gray-600 truncate uppercase">Total Company</p>
                        <p class="text-xl font-bold text-gray-900">{{ $totalCompanies }}</p>
                    </div>
                </div>
            </div>

            <!-- Jenis Company -->
            <div class="bg-white rounded-lg shadow-sm p-3 border-l-4 border-green-500 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-tags text-green-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-3 min-w-0 flex-1">
                        <p class="text-[10px] font-medium text-gray-600 truncate uppercase">Jenis Company</p>
                        <p class="text-xl font-bold text-gray-900">{{ $jenisCompanies }}</p>
                    </div>
                </div>
            </div>

            <!-- Tier Company -->
            <div class="bg-white rounded-lg shadow-sm p-3 border-l-4 border-yellow-500 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-layer-group text-yellow-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-3 min-w-0 flex-1">
                        <p class="text-[10px] font-medium text-gray-600 truncate uppercase">Tier Company</p>
                        <p class="text-xl font-bold text-gray-900">{{ $tierCompanies }}</p>
                    </div>
                </div>
            </div>

            <!-- Company Active (Green) -->
            <div class="bg-white rounded-lg shadow-sm p-3 border-l-4 border-green-500 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-3 min-w-0 flex-1">
                        <p class="text-[10px] font-medium text-gray-600 truncate uppercase">Company Active</p>
                        <p class="text-xl font-bold text-gray-900">{{ $activeCompanies }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>