<div>
    <!-- Loading State -->
    @if($isLoading)
        <div class="flex items-center justify-center h-64">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
        </div>
    @else
        <!-- Page Header with Operator Info -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl p-6 mb-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="h-16 w-16 rounded-full bg-white/20 flex items-center justify-center">
                        <span class="text-2xl font-bold">{{ strtoupper(substr($user['name'] ?? 'O', 0, 1)) }}</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold">{{ $getGreeting() }}, {{ $user['name'] ?? 'Operator' }}!</h1>
                        <p class="text-indigo-100 mt-1">
                            <span class="inline-flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                {{ $user['warehouse'] ?? 'Main Warehouse' }}
                            </span>
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <!-- Sync Status -->
                    <div class="flex items-center gap-2 bg-white/10 px-3 py-2 rounded-lg">
                        <span class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                        </span>
                        <span class="text-sm">Synced</span>
                    </div>
                    <!-- Refresh Button -->
                    <button wire:click="refreshData"
                            class="p-2 bg-white/10 rounded-lg hover:bg-white/20 transition-colors"
                            title="Refresh">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Quick Stats Row -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <!-- Tasks Today -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Tasks Today</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $getUpcomingTasksCount() }}</p>
                    </div>
                    <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Overdue Tasks -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Overdue</p>
                        <p class="text-2xl font-bold {{ $getOverdueTasksCount() > 0 ? 'text-red-600' : 'text-gray-900' }}">
                            {{ $getOverdueTasksCount() }}
                        </p>
                    </div>
                    <div class="h-10 w-10 rounded-lg bg-red-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Items Counted -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Items Counted</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $progress['items_counted'] ?? 0 }}</p>
                    </div>
                    <div class="h-10 w-10 rounded-lg bg-green-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Variance Found -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Variance Found</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $progress['variance_found'] ?? 0 }}</p>
                    </div>
                    <div class="h-10 w-10 rounded-lg bg-yellow-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Tasks and Progress -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Today's Tasks -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <h2 class="text-lg font-semibold text-gray-900">Today's Tasks</h2>
                            @if($getTotalTasks() > 0)
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    {{ $getTotalTasks() }}
                                </span>
                            @endif
                        </div>
                        <a href="{{ route('tasks.my') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                            View All
                        </a>
                    </div>
                    <div class="p-6">
                        @if(count($tasks['upcoming']) > 0 || count($tasks['overdue']) > 0)
                            <div class="space-y-3">
                                <!-- Overdue Tasks -->
                                @foreach($tasks['overdue'] as $task)
                                    <div class="flex items-center gap-4 p-3 bg-red-50 border border-red-100 rounded-lg">
                                        <div class="flex-shrink-0">
                                            <span class="flex h-8 w-8 rounded-full bg-red-100 items-center justify-center">
                                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ $task->title }}</p>
                                            <p class="text-xs text-red-600">Overdue</p>
                                        </div>
                                        <a href="{{ route('tasks.show', $task->id) }}"
                                           class="px-3 py-1 text-xs font-medium text-red-600 bg-red-100 rounded-full hover:bg-red-200">
                                            View
                                        </a>
                                    </div>
                                @endforeach

                                <!-- Upcoming Tasks -->
                                @foreach($tasks['upcoming'] as $task)
                                    <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg">
                                        <div class="flex-shrink-0">
                                            <span class="px-2 py-1 rounded text-xs font-medium
                                                {{ match($task->priority->value) {
                                                    'high' => 'bg-red-100 text-red-800',
                                                    'medium' => 'bg-yellow-100 text-yellow-800',
                                                    'low' => 'bg-green-100 text-green-800',
                                                    default => 'bg-gray-100 text-gray-800'
                                                } }}">
                                                {{ ucfirst($task->priority->value) }}
                                            </span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ $task->title }}</p>
                                            <p class="text-xs text-gray-500">
                                                @if($task->dueDate)
                                                    Due: {{ $task->dueDate->format('M d, H:i') }}
                                                @else
                                                    No due date
                                                @endif
                                            </p>
                                        </div>
                                        <a href="{{ route('tasks.show', $task->id) }}"
                                           class="px-3 py-1 text-xs font-medium text-indigo-600 bg-indigo-100 rounded-full hover:bg-indigo-200">
                                            View
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No tasks assigned</h3>
                                <p class="mt-1 text-sm text-gray-500">You're all caught up! No tasks for today.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Progress Overview -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Progress Overview</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-6">
                            <!-- Task Progress -->
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-700">Task Progress</span>
                                    <span class="text-sm font-bold text-indigo-600">{{ $progress['task_progress_percentage'] ?? 0 }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-indigo-600 h-2 rounded-full"
                                         style="width: {{ $progress['task_progress_percentage'] ?? 0 }}%"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $progress['completed_tasks'] ?? 0 }} of {{ ($progress['completed_tasks'] ?? 0) + ($progress['pending_tasks'] ?? 0) }} completed
                                </p>
                            </div>

                            <!-- Opname Progress -->
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-700">Opname Progress</span>
                                    <span class="text-sm font-bold text-green-600">{{ $activeOpname['progress_percentage'] ?? 0 }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-600 h-2 rounded-full"
                                         style="width: {{ $activeOpname['progress_percentage'] ?? 0 }}%"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $activeOpname['counted_items'] ?? 0 }} of {{ $activeOpname['total_items'] ?? 0 }} items counted
                                </p>
                            </div>
                        </div>

                        <!-- Stats Grid -->
                        <div class="grid grid-cols-3 gap-4 mt-6 pt-6 border-t border-gray-100">
                            <div class="text-center">
                                <p class="text-2xl font-bold text-gray-900">{{ $progress['completed_tasks'] ?? 0 }}</p>
                                <p class="text-xs text-gray-500">Completed Tasks</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-gray-900">{{ $progress['items_counted'] ?? 0 }}</p>
                                <p class="text-xs text-gray-500">Items Counted</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-gray-900">{{ $progress['variance_found'] ?? 0 }}</p>
                                <p class="text-xs text-gray-500">Variances</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Active Opname -->
            <div class="space-y-6">
                <!-- Active Stock Opname -->
                @if($activeOpname)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-semibold text-white">Active Stock Opname</h2>
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-white/20 text-white">
                                    {{ $activeOpname['status_label'] }}
                                </span>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="mb-4">
                                <p class="text-xs text-gray-500">{{ $activeOpname['code'] }}</p>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $activeOpname['name'] }}</h3>
                            </div>

                            <!-- Progress -->
                            <div class="mb-4">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm text-gray-600">Progress</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $activeOpname['progress_percentage'] }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full"
                                         style="width: {{ $activeOpname['progress_percentage'] }}%"></div>
                                </div>
                            </div>

                            <!-- Stats -->
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div class="text-center p-3 bg-gray-50 rounded-lg">
                                    <p class="text-lg font-bold text-gray-900">{{ $activeOpname['counted_items'] }}</p>
                                    <p class="text-xs text-gray-500">Counted</p>
                                </div>
                                <div class="text-center p-3 bg-gray-50 rounded-lg">
                                    <p class="text-lg font-bold text-gray-900">{{ $activeOpname['total_items'] - $activeOpname['counted_items'] }}</p>
                                    <p class="text-xs text-gray-500">Remaining</p>
                                </div>
                            </div>

                            <!-- Variance Alert -->
                            @if($activeOpname['variance_count'] > 0)
                                <div class="mb-4 p-3 bg-yellow-50 border border-yellow-100 rounded-lg">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                        <span class="text-sm text-yellow-800">
                                            {{ $activeOpname['variance_count'] }} variance(s) found
                                        </span>
                                    </div>
                                </div>
                            @endif

                            <!-- Deadline -->
                            @if($activeOpname['deadline'])
                                <div class="mb-4 p-3 {{ $activeOpname['is_overdue'] ? 'bg-red-50 border border-red-100' : 'bg-gray-50' }} rounded-lg">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 {{ $activeOpname['is_overdue'] ? 'text-red-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="text-sm {{ $activeOpname['is_overdue'] ? 'text-red-800 font-medium' : 'text-gray-600' }}">
                                            Deadline: {{ $activeOpname['deadline_label'] }}
                                        </span>
                                    </div>
                                </div>
                            @endif

                            <!-- Action Button -->
                            @if($activeOpname['can_continue'])
                                <a href="{{ route('stock_opnames.count', $activeOpname['id']) }}"
                                   class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Continue Counting
                                </a>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No Active Opname</h3>
                            <p class="mt-1 text-sm text-gray-500">You're not assigned to any stock opname session yet.</p>
                        </div>
                    </div>
                @endif

                <!-- Quick Links -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-2">
                        <a href="{{ route('tasks.index') }}"
                           class="flex items-center gap-3 p-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <span class="text-sm font-medium">My Tasks</span>
                        </a>
                        <a href="{{ route('stock_opnames.my-tasks') }}"
                           class="flex items-center gap-3 p-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                            <span class="text-sm font-medium">Stock Opnames</span>
                        </a>
                        <a href="{{ route('approvals.index') }}"
                           class="flex items-center gap-3 p-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm font-medium">My Approvals</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>