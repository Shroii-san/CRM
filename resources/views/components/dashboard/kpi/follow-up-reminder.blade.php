<div class="bg-white rounded-xl shadow-lg p-6 card-hover fade-in">

    <div class="flex items-center justify-between mb-6">

        <h3 class="text-lg font-semibold text-gray-800">
            Follow-up Reminders
        </h3>

        <span class="px-3 py-1 bg-red-100 text-red-800 text-xs rounded-full font-medium">
            <i class="fas fa-bell mr-1"></i>
            {{ $urgentCount }} Urgent
        </span>

    </div>

    <div class="space-y-3">

        @forelse ($tasks as $task)

            <div class="flex items-center p-3 bg-red-50 rounded-lg border-l-4 border-red-500">

                <div class="flex-1">

                    <p class="text-sm font-medium text-gray-900">
                        {{ $task->deal->client->name }}
                    </p>

                    <p class="text-xs text-gray-600">
                        {{ $task->title }}

                        @php
                            $now = now();
                            $diffInHours = $now->diffInHours($task->due_at, false);
                        @endphp

                        @if ($diffInHours < 0)

                            <span class="text-red-600">
                                Overdue
                            </span>

                        @elseif ($diffInHours < 24)

                            <span>
                                {{ $diffInHours }} jam lagi
                            </span>

                        @else

                            <span>
                                {{ $task->due_at->diffInDays($now) }} hari lagi
                            </span>

                        @endif
                    </p>

                </div>

                <button class="text-red-600 hover:text-red-700">
                    <i class="fas fa-phone"></i>
                </button>

            </div>

        @empty

            <div class="flex flex-col items-center justify-center py-20 text-center">

                <i class="fas fa-check-circle text-3xl text-gray-300 mb-2"></i>

                <p class="text-sm text-gray-500">
                    Tidak ada follow-up.
                </p>

            </div>

        @endforelse

    </div>

</div>