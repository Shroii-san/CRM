<div class="pt-2 px-4 md:px-6 lg:px-6 max-w-7xl mx-auto">
    <!-- KPI Cards - Mini Version -->
    <div class="mt-2 mb-0">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- Total Users Card -->
            <div class="bg-white rounded-lg shadow-sm p-3 border-l-4 border-blue-500 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-blue-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-3 min-w-0 flex-1">
                        <p class="text-[10px] font-medium text-gray-600 truncate uppercase">Total Users</p>
                        <p class="text-xl font-bold text-gray-900">{{ $totalUsers }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Roles Card -->
            <div class="bg-white rounded-lg shadow-sm p-3 border-l-4 border-green-500 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user-tag text-green-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-3 min-w-0 flex-1">
                        <p class="text-[10px] font-medium text-gray-600 truncate uppercase">Total Roles</p>
                        <p class="text-xl font-bold text-gray-900">{{ $totalRoles }}</p>
                    </div>
                </div>
            </div>

            <!-- Online User Card -->
            <div class="bg-white rounded-lg shadow-sm p-3 border-l-4 border-purple-500 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user-check text-purple-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-3 min-w-0 flex-1">
                        <p class="text-[10px] font-medium text-gray-600 truncate uppercase">Online User</p>
                        <p class="text-xl font-bold text-gray-900">{{ $onlineUsers }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Menu Card -->
            <div class="bg-white rounded-lg shadow-sm p-3 border-l-4 border-yellow-500 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-list text-yellow-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-3 min-w-0 flex-1">
                        <p class="text-[10px] font-medium text-gray-600 truncate uppercase">Total Menu</p>
                        <p class="text-xl font-bold text-gray-900">{{ $totalMenus }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>