<nav class="gradient-bg shadow-lg fixed top-0 left-0 right-0 z-50">
    <div class="container-expanded mx-auto px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Left: Logo & Hamburger -->
            <div class="flex items-center">
                <button id="sidebarToggle" class="text-white hover:text-indigo-300 mr-4 transition-colors">
                    <i class="fas fa-bars text-xl"></i>
                </button>

                <div class="flex-shrink-0 flex items-center">
                    <img class="h-8 w-auto" src="{{ asset('img/LOGO INOTAL UNTUK BG GELAP.png') }}" alt="Logo">
                </div>
            </div>

            <!-- Right: Notifications & User -->
            <div class="flex items-center space-x-4">
                <!-- Notifications -->
                <div class="relative">
                    <button class="text-white text-lg hover:text-yellow-300 transition-colors" onclick="toggleNotifications()">
                        <i class="fas fa-bell"></i>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center">
                            3
                        </span>
                    </button>

                    <div id="notificationDropdown" class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg z-50 hidden">
                        <div class="py-2">
                            <div class="px-4 py-2 border-b border-gray-200">
                                <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
                            </div>
                            <div class="max-h-64 overflow-y-auto">
                                <div class="px-4 py-3 hover:bg-gray-50 bg-blue-50">
                                    <p class="text-sm text-gray-900">New customer registered</p>
                                    <p class="text-xs text-gray-500 mt-1">2 minutes ago</p>
                                </div>
                                <div class="px-4 py-3 hover:bg-gray-50 bg-blue-50">
                                    <p class="text-sm text-gray-900">Proposal submitted for review</p>
                                    <p class="text-xs text-gray-500 mt-1">1 hour ago</p>
                                </div>
                                <div class="px-4 py-3 hover:bg-gray-50">
                                    <p class="text-sm text-gray-900">Monthly report generated</p>
                                    <p class="text-xs text-gray-500 mt-1">3 hours ago</p>
                                </div>
                            </div>
                            <div class="px-4 py-2 border-t border-gray-200">
                                <a href="#" class="text-sm text-indigo-600 hover:text-indigo-800">
                                    View all notifications
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Info -->
                <div class="flex items-center space-x-3">
                    <div class="hidden sm:flex items-center space-x-2">
                        <img class="h-8 w-8 rounded-full border-2 border-white"
                            src="{{ asset('img/logo.png') }}"
                            alt="Profile">
                        <div>
                            <span class="text-white font-medium block">
                                {{ Auth::user()->name }}
                            </span>
                            <span class="text-blue-200 text-xs block">
                                {{ Auth::user()->role->name }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<style>
    .gradient-bg {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .container-expanded {
        max-width: 1400px;
    }
</style>

<script>
function toggleNotifications() {
    const dropdown = document.getElementById('notificationDropdown');
    dropdown.classList.toggle('hidden');
}

document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('notificationDropdown');
    const notificationButton = event.target.closest('.fa-bell')?.parentElement;

    if (!notificationButton && !dropdown.contains(event.target)) {
        dropdown.classList.add('hidden');
    }
});
</script>
