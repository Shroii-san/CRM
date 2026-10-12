<!-- Pagination -->
<div data-pagination style="padding: 0.75rem 1.5rem; border-top: 1px solid #e5e7eb; background-color: #f9fafb;">
    <div x-show="meta.total > 0"
        class="flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-600">
        <div class="flex items-center gap-2 flex-wrap">
            <span>Tampilkan</span>
            <select x-model.number="perPage" @change="onPerPageChange($event)"
                class="px-2 py-1 bg-white border border-gray-300 rounded text-xs font-medium text-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all cursor-pointer">
                <template x-for="option in perPageOptions" :key="option">
                    <option :value="option" x-text="option" :selected="option === perPage"></option>
                </template>
            </select>
            <span>data per halaman dari <span class="font-medium" x-text="meta.total"></span> data</span>
        </div>
        <div class="flex items-center gap-2">
            <button @click="setPage(page - 1)" :disabled="page <= 1"
                class="px-3 py-1 border rounded text-xs font-medium disabled:opacity-50 hover:bg-gray-100">
                Prev
            </button>
            <span class="text-xs">
                Halaman <span x-text="meta.current_page"></span> dari <span x-text="meta.last_page"></span>
            </span>
            <button @click="setPage(page + 1)" :disabled="page >= meta.last_page"
                class="px-3 py-1 border rounded text-xs font-medium disabled:opacity-50 hover:bg-gray-100">
                Next
            </button>
        </div>
    </div>
</div>