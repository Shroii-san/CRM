<div class="bg-white rounded-xl shadow-lg p-6 card-hover fade-in h-full">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-800">Distribusi Geografis Customer</h3>
        <div class="flex space-x-2">
            <button class="text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-gray-100 rounded-lg" onclick="refreshGeoChart()">
                <i class="fas fa-sync-alt text-sm"></i>
            </button>
            <button class="text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-gray-100 rounded-lg" onclick="openGeoFullscreen()">
                <i class="fas fa-expand-alt text-sm"></i>
            </button>
        </div>
    </div>
    
    <!-- Chart Container dengan aspect ratio yang optimal -->
    <div class="relative bg-gray-50 rounded-lg p-4 mb-2" style="height: 320px;">
        <canvas id="geo" class="w-full h-full"></canvas>
    </div>
    
    <!-- Quick Actions Bar -->
    <div class="flex justify-center space-x-3 mb-3">
        <button class="text-xs px-3 py-1 bg-blue-50 text-blue-600 rounded-full hover:bg-blue-100 transition-colors">
            <i class="fas fa-filter mr-1"></i>Filter
        </button>
        <button class="text-xs px-3 py-1 bg-gray-50 text-gray-600 rounded-full hover:bg-gray-100 transition-colors">
            <i class="fas fa-sync mr-1"></i>Refresh
        </button>
        <button class="text-xs px-3 py-1 bg-green-50 text-green-600 rounded-full hover:bg-green-100 transition-colors">
            <i class="fas fa-chart-line mr-1"></i>Trend
        </button>
    </div>
    
    <!-- Summary Stats dengan hover dan click -->
    <div class="grid grid-cols-3 gap-4 pt-3 border-t border-gray-100">
        <div class="text-center stat-item clickable-stat" onclick="showGeoStatDetail('total')">
            <div class="text-xl font-bold text-blue-600 stat-number">490</div>
            <div class="text-xs text-gray-500 stat-label">Total Customer</div>
        </div>
        <div class="text-center stat-item clickable-stat" onclick="showGeoStatDetail('cities')">
            <div class="text-xl font-bold text-green-600 stat-number">5</div>
            <div class="text-xs text-gray-500 stat-label">Kota Aktif</div>
        </div>
        <div class="text-center stat-item clickable-stat" onclick="showGeoStatDetail('highest')">
            <div class="text-xl font-bold text-purple-600 stat-number">150</div>
            <div class="text-xs text-gray-500 stat-label">Tertinggi</div>
        </div>
    </div>
</div>

<!-- Geo Data Popup Modal -->
<div class="data-popup-overlay" id="geoDataPopup">
    <div class="data-popup-content">
        <div class="popup-header">
            <h3 class="popup-title" id="geoPopupTitle">Detail Data Geografis</h3>
            <button class="close-popup" onclick="closeGeoDataPopup()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="popup-body" id="geoPopupBody">
            <!-- Dynamic content will be inserted here -->
        </div>
        <div class="popup-footer">
            <button class="btn-secondary" onclick="closeGeoDataPopup()">Tutup</button>
            <button class="btn-primary" onclick="exportGeoData()">Export Data</button>
        </div>
    </div>
</div>

<!-- Geo Fullscreen Overlay -->
<div class="fullscreen-overlay" id="geoFullscreenOverlay">
    <div class="fullscreen-content">
        <i class="fas fa-times close-fullscreen" onclick="closeGeoFullscreen()"></i>
        <h2 class="text-2xl font-bold mb-6">Distribusi Geografis Customer - Detail View</h2>
        <div class="chart-controls mb-4">
            <button class="control-btn active" onclick="switchGeoView('bar')" id="geo-btn-bar">Bar Chart</button>
            <button class="control-btn" onclick="switchGeoView('pie')" id="geo-btn-pie">Pie Chart</button>
            <button class="control-btn" onclick="switchGeoView('map')" id="geo-btn-map">Map View</button>
        </div>
        <canvas id="geoFullscreenChart" style="max-height: calc(100% - 120px);"></canvas>
    </div>
</div>

<style>
/* Optimasi untuk chart container */
.chart-container {
    position: relative;
    height: 300px;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-radius: 8px;
    padding: 16px;
}

/* Hover effect untuk card */
.card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Smooth transition */
.card-hover {
    transition: all 0.3s ease;
}

/* Button hover states */
.icon-btn {
    padding: 8px;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.icon-btn:hover {
    background-color: #f3f4f6;
    transform: scale(1.05);
}
</style>