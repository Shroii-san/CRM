<!-- Bulk Actions Bar -->
<div id="bulkActions" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <span class="text-sm font-medium text-blue-900">
                <span id="selectedCount">0</span> item dipilih
            </span>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="bulkDeleteCustomers()" 
                class="flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                <i class="fas fa-trash"></i>
                Hapus Terpilih
            </button>
            <button onclick="document.querySelectorAll('.customer-checkbox').forEach(cb => cb.checked = false); handleCheckboxChange()" 
                class="text-sm text-gray-600 hover:text-gray-900">
                Batal
            </button>
        </div>
    </div>
</div>

<script>
// Update selected count
function handleCheckboxChange() {
    const checkboxes = document.querySelectorAll('.customer-checkbox:checked');
    const count = checkboxes.length;
    
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');
    
    if (bulkActions) {
        bulkActions.classList.toggle('hidden', count === 0);
    }
    
    if (selectedCount) {
        selectedCount.textContent = count;
    }
}
</script>