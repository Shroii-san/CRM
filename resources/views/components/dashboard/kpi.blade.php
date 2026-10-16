<!-- KPI Cards - Mini Version -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-6">
    <!-- Total Customer -->
    <div class="bg-white rounded-lg shadow-sm p-3 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] font-medium text-gray-600 uppercase">Total Client</p>
                <p id="totalCustomer" class="text-xl font-bold text-gray-900 mt-1">{{ $stats['total_clients'] }}</p>
            </div>
            <div class="bg-blue-100 p-2 rounded-full">
                <i class="fas fa-users text-blue-600 text-sm"></i>
            </div>
        </div>
    </div>

    <!-- Leads Aktif -->
    <div class="bg-white rounded-lg shadow-sm p-3 border-l-4 border-orange-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] font-medium text-gray-600 uppercase">Open Deals</p>
                <p id="activeLeads" class="text-xl font-bold text-gray-900 mt-1">{{$stats['total_open_deals']}}</p>
            </div>
            <div class="bg-orange-100 p-2 rounded-full">
                <i class="fas fa-user-plus text-orange-600 text-sm"></i>
            </div>
        </div>
    </div>

    <!-- Follow UP -->
    <div class="bg-white rounded-lg shadow-sm p-3 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] font-medium text-gray-600 uppercase">Won Deals</p>
                <p id="proposalSent" class="text-xl font-bold text-gray-900 mt-1">{{$stats['total_won_deals']}}</p>
            </div>
            <div class="bg-purple-100 p-2 rounded-full">
                <i class="fas fa-file-alt text-green-600 text-sm"></i>
            </div>
        </div>
    </div>

    <!-- Total Pendapatan -->
    <div class="bg-white rounded-lg shadow-sm p-3 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] font-medium text-gray-600 uppercase">Total Pendapatan</p>
                <p id="totalRevenue" class="text-xl font-bold text-gray-900 mt-1">Rp {{$stats['total_revenue']}}</p>
            </div>
            <div class="bg-yellow-100 p-2 rounded-full">
                <i class="fas fa-dollar-sign text-yellow-600 text-sm"></i>
            </div>
        </div>
    </div>
</div>