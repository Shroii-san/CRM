/**
 * Geographic Chart Handler
 * Handles bar and pie chart for tier distribution
 * Author: Your Name
 * Version: 1.0.0
 */

// ==========================
// GLOBAL VARIABLES
// ==========================
let geoChart = null;
let geoFullscreenChart = null;
let geoData = null;

// ==========================
// INITIALIZATION
// ==========================

/**
 * Initialize Geographic Chart (Bar Chart)
 */
async function initGeoChart() {
    try {
        const response = await fetch('/api/geographic/bar/distribution');
        const result = await response.json();
        
        if (result.success) {
            geoData = result;
            
            // Update stats
            updateGeoStats(result.stats);
            
            // Create chart
            createGeoChart(result.data);
        } else {
            console.error('Error loading geo data:', result.message);
            showNotification('Gagal memuat data geografis', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Terjadi kesalahan saat memuat data', 'error');
    }
}

// ==========================
// CHART CREATION
// ==========================

/**
 * Create main geo bar chart
 */
function createGeoChart(data) {
    const ctx = document.getElementById('geo');
    if (!ctx) return;
    
    // Destroy existing chart
    if (geoChart) {
        geoChart.destroy();
    }
    
    geoChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Jumlah Customer',
                data: data.data,
                backgroundColor: data.colors,
                borderColor: data.colors.map(c => c.replace('0.8', '1')),
                borderWidth: 2,
                borderRadius: 8,
                barPercentage: 0.7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    callbacks: {
                        label: function(context) {
                            const value = context.parsed.y;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `Customer: ${value} (${percentage}%)`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 50,
                        font: {
                            size: 11
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    ticks: {
                        font: {
                            size: 11,
                            weight: 'bold'
                        }
                    },
                    grid: {
                        display: false
                    }
                }
            },
            onClick: (event, elements) => {
                if (elements.length > 0) {
                    const index = elements[0].index;
                    const tier = data.labels[index];
                    showTierDetail(tier);
                }
            }
        }
    });
}

/**
 * Create fullscreen chart
 */
function createGeoFullscreenChart(type) {
    if (!geoData) return;
    
    const ctx = document.getElementById('geoFullscreenChart');
    if (!ctx) return;
    
    if (geoFullscreenChart) {
        geoFullscreenChart.destroy();
    }
    
    const chartConfig = {
        type: type === 'map' ? 'bar' : type,
        data: {
            labels: geoData.data.labels,
            datasets: [{
                label: 'Jumlah Customer',
                data: geoData.data.data,
                backgroundColor: geoData.data.colors,
                borderColor: geoData.data.colors.map(c => c.replace('0.8', '1')),
                borderWidth: 2,
                borderRadius: type === 'bar' ? 10 : 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: type === 'pie',
                    position: 'right',
                    labels: {
                        font: { size: 14 },
                        padding: 15
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 16,
                    titleFont: { size: 16, weight: 'bold' },
                    bodyFont: { size: 14 }
                }
            }
        }
    };
    
    if (type === 'bar') {
        chartConfig.options.scales = {
            y: {
                beginAtZero: true,
                ticks: { font: { size: 13 } }
            },
            x: {
                ticks: { font: { size: 13, weight: 'bold' } }
            }
        };
    }
    
    geoFullscreenChart = new Chart(ctx, chartConfig);
}

// ==========================
// DATA HANDLERS
// ==========================

/**
 * Update statistics display
 */
function updateGeoStats(stats) {
    const statElements = {
        total: document.querySelector('.grid .stat-item:nth-child(1) .stat-number'),
        cities: document.querySelector('.grid .stat-item:nth-child(2) .stat-number'),
        highest: document.querySelector('.grid .stat-item:nth-child(3) .stat-number')
    };
    
    if (statElements.total) {
        statElements.total.textContent = stats.total_customers;
    }
    if (statElements.cities) {
        statElements.cities.textContent = stats.active_tiers;
    }
    if (statElements.highest) {
        statElements.highest.textContent = stats.highest_count;
    }
}

/**
 * Show tier detail popup
 */
async function showTierDetail(tier) {
    try {
        const response = await fetch(`/api/geographic/bar/tier/${tier}`);
        const result = await response.json();
        
        if (result.success) {
            const popup = document.getElementById('geoDataPopup');
            const title = document.getElementById('geoPopupTitle');
            const body = document.getElementById('geoPopupBody');
            
            title.textContent = `Detail Tier: ${tier}`;
            
            let html = `
                <div class="stat-box">
                    <h4 class="text-lg font-semibold mb-2">Ringkasan</h4>
                    <p class="text-gray-600">Total Customer: <span class="font-bold text-blue-600">${result.count}</span></p>
                </div>
                <div class="mt-4">
                    <h4 class="text-lg font-semibold mb-3">Daftar Customer</h4>
                    <div class="max-h-96 overflow-y-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">No</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Nama</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Tier</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
            `;
            
            result.customers.forEach((customer, index) => {
                const date = new Date(customer.created_at).toLocaleDateString('id-ID');
                html += `
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2 text-sm">${index + 1}</td>
                        <td class="px-4 py-2 text-sm">${customer.name || '-'}</td>
                        <td class="px-4 py-2 text-sm"><span class="badge badge-blue">${customer.tier}</span></td>
                        <td class="px-4 py-2 text-sm text-gray-500">${date}</td>
                    </tr>
                `;
            });
            
            html += `
                            </tbody>
                        </table>
                    </div>
                </div>
            `;
            
            body.innerHTML = html;
            popup.classList.add('active');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Gagal memuat detail tier', 'error');
    }
}

/**
 * Show geo stat detail in popup
 */
function showGeoStatDetail(type) {
    if (!geoData) return;
    
    const popup = document.getElementById('geoDataPopup');
    const title = document.getElementById('geoPopupTitle');
    const body = document.getElementById('geoPopupBody');
    
    let html = '';
    
    switch(type) {
        case 'total':
            title.textContent = 'Total Customer';
            html = `
                <div class="stat-box">
                    <h4 class="text-3xl font-bold text-blue-600 mb-2">${geoData.stats.total_customers}</h4>
                    <p class="text-gray-600">Total customer di semua tier</p>
                </div>
                <div class="mt-4">
                    <h5 class="font-semibold mb-2">Breakdown per Tier:</h5>
                    <div class="space-y-2">
            `;
            geoData.details.forEach(item => {
                const percentage = ((item.total / geoData.stats.total_customers) * 100).toFixed(1);
                html += `
                    <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                        <span class="font-medium">${item.tier}</span>
                        <span class="text-gray-600">${item.total} customer (${percentage}%)</span>
                    </div>
                `;
            });
            html += `</div></div>`;
            break;
            
        case 'cities':
            title.textContent = 'Tier Aktif';
            html = `
                <div class="stat-box">
                    <h4 class="text-3xl font-bold text-green-600 mb-2">${geoData.stats.active_tiers}</h4>
                    <p class="text-gray-600">Jumlah tier yang memiliki customer</p>
                </div>
                <div class="mt-4">
                    <h5 class="font-semibold mb-2">Daftar Tier:</h5>
                    <div class="flex flex-wrap gap-2">
            `;
            geoData.details.forEach(item => {
                html += `<span class="badge badge-green">${item.tier}</span>`;
            });
            html += `</div></div>`;
            break;
            
        case 'highest':
            title.textContent = 'Tier Tertinggi';
            html = `
                <div class="stat-box">
                    <h4 class="text-3xl font-bold text-purple-600 mb-2">${geoData.stats.highest_count}</h4>
                    <p class="text-gray-600">Customer di tier: <span class="font-bold">${geoData.stats.highest_tier}</span></p>
                </div>
            `;
            break;
    }
    
    body.innerHTML = html;
    popup.classList.add('active');
}

// ==========================
// UI INTERACTIONS
// ==========================

/**
 * Refresh chart data
 */
async function refreshGeoChart() {
    showNotification('Memuat ulang data...', 'info');
    await initGeoChart();
    showNotification('Data berhasil dimuat ulang', 'success');
}

/**
 * Open fullscreen view
 */
function openGeoFullscreen() {
    const overlay = document.getElementById('geoFullscreenOverlay');
    overlay.classList.add('active');
    
    setTimeout(() => {
        createGeoFullscreenChart('bar');
    }, 100);
}

/**
 * Close fullscreen view
 */
function closeGeoFullscreen() {
    const overlay = document.getElementById('geoFullscreenOverlay');
    overlay.classList.remove('active');
    
    if (geoFullscreenChart) {
        geoFullscreenChart.destroy();
        geoFullscreenChart = null;
    }
}

/**
 * Switch fullscreen chart view type
 */
function switchGeoView(type) {
    // Update active button
    document.querySelectorAll('.control-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    document.getElementById(`geo-btn-${type}`).classList.add('active');
    
    createGeoFullscreenChart(type);
}

/**
 * Close data popup
 */
function closeGeoDataPopup() {
    const popup = document.getElementById('geoDataPopup');
    popup.classList.remove('active');
}

/**
 * Export geo data to CSV
 */
function exportGeoData() {
    window.location.href = '/api/geographic/bar/export';
}

// ==========================
// UTILITY FUNCTIONS
// ==========================

/**
 * Show notification (if notification system exists)
 */
// function showNotification(message, type = 'info') {
//     // Check if notification system exists
//     if (typeof window.showNotification === 'function') {
//         window.showNotification(message, type);
//     } else {
//         // Fallback to console
//         console.log(`[${type.toUpperCase()}] ${message}`);
//     }
// }

// ==========================
// AUTO INITIALIZATION
// ==========================

/**
 * Auto initialize when DOM is ready
 */
document.addEventListener('DOMContentLoaded', function() {
    // Check if geo chart element exists
    if (document.getElementById('geo')) {
        initGeoChart();
    }
});