<div class="bg-white rounded-xl shadow-lg p-6 card-hover fade-in">

    <h3 class="text-lg font-semibold text-gray-800 mb-6">
        Komunikasi Hari Ini
    </h3>

    <div class="space-y-4">

        @forelse ($communications as $communication)

            @php
                $icons = [
                    'call' => 'fa-phone',
                    'email' => 'fa-envelope',
                    'meeting' => 'fa-calendar-check',
                ];
                $icon = $icons[$communication->type] ?? 'fa-comment';
            @endphp

            <button
                class="flex items-center justify-between w-full p-3 rounded-xl border border-gray-200 hover:bg-gray-50 transition">

                <div class="flex items-center space-x-3">

                    <div class="w-10 h-10 rounded-lg bg-gray-100 text-gray-600 flex items-center justify-center">
                        <i class="fas {{ $icon }}"></i>
                    </div>

                    <p class="text-sm font-medium text-gray-900">
                        {{ ucfirst($communication->type) }}
                    </p>

                </div>

                <span class="text-lg font-bold text-gray-800">
                    {{ $communication->total }}
                </span>

            </button>

        @empty

            <div class="flex flex-col items-center justify-center py-20 text-center">

                <i class="fas fa-comments text-3xl text-gray-300 mb-2"></i>

                <p class="text-sm text-gray-500">
                    Belum ada komunikasi hari ini.
                </p>

            </div>

        @endforelse

    </div>

</div>
