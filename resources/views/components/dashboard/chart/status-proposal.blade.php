<div class="bg-white rounded-xl shadow-lg p-6 card-hover fade-in">
    <!-- Header -->
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-800">Sales Performance</h3>
    </div>

    <!-- Chart Container -->
    <div class="relative h-80">
        <!-- Loading Indicator -->
        <div class="loading-indicator absolute inset-0 flex items-center justify-center" style="display: none;">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
        </div>
        
        <!-- Canvas -->
        <canvas id="salesPerformance"></canvas>
    </div>

    <!-- Statistics Section -->
    <div class="mt-6 grid grid-cols-3 gap-4 text-center">
        <div class="bg-blue-50 rounded-lg p-3">
            <div class="text-lg font-bold text-blue-600" id="totalCompanyVisited">0</div>
            <div class="text-sm text-gray-600">Company Visited</div>
        </div>
        <div class="bg-green-50 rounded-lg p-3">
            <div class="text-lg font-bold text-green-600" id="totalDeal">0</div>
            <div class="text-sm text-gray-600">Deal</div>
        </div>
        <div class="bg-red-50 rounded-lg p-3">
            <div class="text-lg font-bold text-red-600" id="totalFails">0</div>
            <div class="text-sm text-gray-600">Fails</div>
        </div>
    </div>

    <!-- Info Text -->
    <div class="mt-4 text-center">
        <p class="text-xs text-gray-500">Click on any bar to view detailed performance</p>
    </div>
</div>

<style>
    .card-hover {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>