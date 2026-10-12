<!-- KPI Cards - Mini Version -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-6">
    <!-- Total Customers -->
    <div class="bg-white rounded-lg shadow-sm p-3 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] font-medium text-gray-600 uppercase">Total Customers</p>
                <p id="totalCustomers" class="text-xl font-bold text-gray-900 mt-1">0</p>
            </div>
            <div class="bg-blue-100 p-2 rounded-full">
                <i class="fas fa-users text-blue-600 text-sm"></i>
            </div>
        </div>
    </div>

    <!-- Active Customers -->
    <div class="bg-white rounded-lg shadow-sm p-3 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] font-medium text-gray-600 uppercase">Active</p>
                <p id="activeCustomers" class="text-xl font-bold text-gray-900 mt-1">0</p>
            </div>
            <div class="bg-green-100 p-2 rounded-full">
                <i class="fas fa-check-circle text-green-600 text-sm"></i>
            </div>
        </div>
    </div>

    <!-- Leads -->
    <div class="bg-white rounded-lg shadow-sm p-3 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] font-medium text-gray-600 uppercase">Leads</p>
                <p id="leadCustomers" class="text-xl font-bold text-gray-900 mt-1">0</p>
            </div>
            <div class="bg-yellow-100 p-2 rounded-full">
                <i class="fas fa-lightbulb text-yellow-600 text-sm"></i>
            </div>
        </div>
    </div>

    <!-- Prospects -->
    <div class="bg-white rounded-lg shadow-sm p-3 border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] font-medium text-gray-600 uppercase">Prospects</p>
                <p id="prospectCustomers" class="text-xl font-bold text-gray-900 mt-1">0</p>
            </div>
            <div class="bg-purple-100 p-2 rounded-full">
                <i class="fas fa-star text-purple-600 text-sm"></i>
            </div>
        </div>
    </div>
</div>