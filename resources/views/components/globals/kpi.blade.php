<div class="pt-4 px-4 md:px-6 lg:px-6 max-w-7xl mx-auto">
        <!-- KPI Cards with proper margins -->
        <div class="mt-4 mb-2">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                <!-- Total Users Card -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-users text-blue-600 text-lg"></i>
                            </div>
                        </div>
                        <div class="ml-4 min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-600 truncate">Total Users</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalUsers }}</p>
                        </div>
                    </div>
                </div>

                <!-- Total Roles Card -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-user-tag text-green-600 text-lg"></i>
                            </div>
                        </div>
                        <div class="ml-4 min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-600 truncate">Total Roles</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalRoles }}</p>
                        </div>
                    </div>
                </div>

                <!-- Active Permissions Card -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-users text-green-600 text-lg"></i>
                            </div>
                        </div>
                        <div class="ml-4 min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-600 truncate">Online User</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $onlineUsers }}</p>
                        </div>
                    </div>
                </div>

                <!-- Pending Reviews Card -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-list text-green-600 text-lg"></i>
                            </div>
                        </div>
                        <div class="ml-4 min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-600 truncate">Total Menu</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalMenus }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>