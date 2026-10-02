<!-- Table -->
<div class="bg-white rounded-lg shadow-sm overflow-hidden fade-in">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-3 py-2 text-left">
                        <input
                            type="checkbox"
                            id="selectAll"
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-3.5 h-3.5"
                        />
                    </th>
                    <th class="px-3 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-tight">
                        No
                    </th>
                    <th class="px-3 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-tight">
                        Nama
                    </th>
                    <th class="px-3 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-tight">
                        Tipe
                    </th>
                    <th class="px-3 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-tight">
                        Email
                    </th>
                    <th class="px-3 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-tight">
                        Telepon
                    </th>
                    <th class="px-3 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-tight">
                        Status
                    </th>
                    <th class="px-3 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-tight">
                        PIC
                    </th>
                    <th class="px-3 py-2 text-right text-[10px] font-medium text-gray-500 uppercase tracking-tight">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody id="customerTableBody" class="divide-y divide-gray-200">
                <!-- Data will be populated by JavaScript -->
            </tbody>
        </table>
    </div>
</div>

<style>
.fade-in { 
    animation: fadeIn 0.3s ease-in; 
}

@keyframes fadeIn { 
    from { opacity: 0; } 
    to { opacity: 1; } 
}
</style>