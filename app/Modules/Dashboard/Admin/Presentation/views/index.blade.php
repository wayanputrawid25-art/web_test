<div>
    @if($isLoading)
        <div class="flex items-center justify-center h-64">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
        </div>
    @else
        <!-- Page Header -->
        <div class="bg-gradient-to-r from-slate-700 to-slate-900 rounded-xl p-6 mb-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="h-16 w-16 rounded-full bg-white/10 flex items-center justify-center">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold">{{ $getGreeting() }}, {{ $getCurrentAdminName() }}</h1>
                        <p class="text-slate-300 mt-1">Admin Dashboard - System Overview</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-slate-300">{{ now()->format('l, F j, Y') }}</span>
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

        <!-- Stats Cards Row -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
            <!-- Pending Approvals -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Pending Approvals</p>
                        <p class="text-2xl font-bold {{ $stats['pending_approvals'] > 0 ? 'text-orange-600' : 'text-gray-900' }}">
                            {{ $stats['pending_approvals'] }}
                        </p>
                    </div>
                    <div class="h-10 w-10 rounded-lg bg-orange-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Active Opnames -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Active Opnames</p>
                        <p class="text-2xl font-bold text-green-600">{{ $stats['active_opnames'] }}</p>
                    </div>
                    <div class="h-10 w-10 rounded-lg bg-green-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Tasks -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Tasks</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $stats['total_tasks'] }}</p>
                    </div>
                    <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Overdue Tasks -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Overdue Tasks</p>
                        <p class="text-2xl font-bold {{ $stats['overdue_tasks'] > 0 ? 'text-red-600' : 'text-gray-900' }}">
                            {{ $stats['overdue_tasks'] }}
                        </p>
                    </div>
                    <div class="h-10 w-10 rounded-lg bg-red-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Low Stock -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Low Stock Items</p>
                        <p class="text-2xl font-bold {{ $stats['low_stock_items'] > 0 ? 'text-yellow-600' : 'text-gray-900' }}">
                            {{ $stats['low_stock_items'] }}
                        </p>
                    </div>
                    <div class="h-10 w-10 rounded-lg bg-yellow-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Active Operators -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Active Operators</p>
                        <p class="text-2xl font-bold text-purple-600">{{ $stats['active_operators'] }}</p>
                    </div>
                    <div class="h-10 w-10 rounded-lg bg-purple-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column (2/3) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Pending Approvals Widget -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h2 class="text-lg font-semibold text-gray-900">Pending Approvals</h2>
                            @if($pendingApprovals['pending_count'] > 0)
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                    {{ $pendingApprovals['pending_count'] }}
                                </span>
                            @endif
                        </div>
                        <a href="{{ route('approvals.queue.all') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                            View All
                        </a>
                    </div>
                    <div class="p-6">
                        @if(count($pendingApprovals['recent_requests']) > 0)
                            <div class="space-y-3">
                                @foreach($pendingApprovals['recent_requests'] as $approval)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center gap-3">
                                            <div class="flex-shrink-0">
                                                <div class="h-10 w-10 rounded-full bg-orange-100 flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $approval->code }}</p>
                                                <p class="text-xs text-gray-500">{{ $approval->title }}</p>
                                                <p class="text-xs text-gray-400">{{ $approval->requesterName ?? 'Unknown' }} • {{ $approval->createdAt?->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                {{ $approval->type->label() }}
                                            </span>
                                            <a href="{{ route('approvals.show', $approval->id) }}"
                                               class="px-3 py-1 text-xs font-medium text-indigo-600 bg-indigo-100 rounded-full hover:bg-indigo-200">
                                                Review
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No pending approvals</h3>
                                <p class="text-sm text-gray-500">All caught up! No approvals waiting.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Active Stock Opnames Widget -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                            <h2 class="text-lg font-semibold text-gray-900">Active Stock Opnames</h2>
                            @if($activeStockOpnames['active_count'] > 0)
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $activeStockOpnames['active_count'] }}
                                </span>
                            @endif
                        </div>
                        <a href="{{ route('stock_opnames.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                            Manage
                        </a>
                    </div>
                    <div class="p-6">
                        @if(count($activeStockOpnames['sessions']) > 0)
                            <div class="space-y-4">
                                @foreach($activeStockOpnames['sessions'] as $session)
                                    <div class="p-4 bg-gray-50 rounded-lg">
                                        <div class="flex items-center justify-between mb-3">
                                            <div>
                                                <p class="text-xs text-gray-500">{{ $session['code'] }}</p>
                                                <h4 class="text-sm font-semibold text-gray-900">{{ $session['name'] }}</h4>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="px-2.5 py-1 rounded-full text-xs font-medium
                                                    {{ $session['is_overdue'] ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                                    {{ $session['status_label'] }}
                                                </span>
                                                <a href="{{ route('stock_opnames.show', $session['id']) }}"
                                                   class="text-xs text-indigo-600 hover:text-indigo-800">
                                                    View →
                                                </a>
                                            </div>
                                        </div>
                                        <!-- Progress Bar -->
                                        <div class="mb-2">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-xs text-gray-500">Progress</span>
                                                <span class="text-xs font-medium text-gray-900">{{ $session['progress_percentage'] }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-green-500 h-2 rounded-full"
                                                     style="width: {{ $session['progress_percentage'] }}%"></div>
                                            </div>
                                        </div>
                                        <!-- Stats -->
                                        <div class="grid grid-cols-4 gap-2 text-center">
                                            <div class="p-2 bg-white rounded">
                                                <p class="text-lg font-bold text-gray-900">{{ $session['counted_items'] }}</p>
                                                <p class="text-xs text-gray-500">Counted</p>
                                            </div>
                                            <div class="p-2 bg-white rounded">
                                                <p class="text-lg font-bold text-gray-900">{{ $session['total_items'] - $session['counted_items'] }}</p>
                                                <p class="text-xs text-gray-500">Remaining</p>
                                            </div>
                                            <div class="p-2 bg-white rounded">
                                                <p class="text-lg font-bold text-gray-900">{{ $session['variance_count'] }}</p>
                                                <p class="text-xs text-gray-500">Variance</p>
                                            </div>
                                            <div class="p-2 bg-white rounded">
                                                <p class="text-lg font-bold text-gray-900">{{ $session['assigned_count'] }}</p>
                                                <p class="text-xs text-gray-500">Assigned</p>
                                            </div>
                                        </div>
                                        @if($session['deadline'])
                                            <p class="text-xs text-gray-500 mt-2">
                                                Deadline: {{ $session['deadline_label'] }}
                                            </p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No active stock opnames</h3>
                                <p class="text-sm text-gray-500">Create a new stock opname session to get started.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Task Summary Widget -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <h2 class="text-lg font-semibold text-gray-900">Task Summary</h2>
                        </div>
                        <a href="{{ route('tasks.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                            View All
                        </a>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-4 gap-4 mb-6">
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <p class="text-2xl font-bold text-gray-900">{{ $taskSummary['total'] }}</p>
                                <p class="text-xs text-gray-500">Total</p>
                            </div>
                            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                                <p class="text-2xl font-bold text-yellow-600">{{ $taskSummary['pending'] }}</p>
                                <p class="text-xs text-gray-500">Pending</p>
                            </div>
                            <div class="text-center p-4 bg-blue-50 rounded-lg">
                                <p class="text-2xl font-bold text-blue-600">{{ $taskSummary['in_progress'] }}</p>
                                <p class="text-xs text-gray-500">In Progress</p>
                            </div>
                            <div class="text-center p-4 bg-green-50 rounded-lg">
                                <p class="text-2xl font-bold text-green-600">{{ $taskSummary['completed'] }}</p>
                                <p class="text-xs text-gray-500">Completed</p>
                            </div>
                        </div>
                        @if($taskSummary['overdue'] > 0)
                            <div class="p-4 bg-red-50 border border-red-100 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="text-sm font-medium text-red-800">{{ $taskSummary['overdue'] }} overdue task(s)</span>
                                    </div>
                                    <a href="{{ route('tasks.index', ['status' => 'overdue']) }}"
                                       class="text-xs text-red-600 hover:text-red-800 font-medium">
                                        View →
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column (1/3) -->
            <div class="space-y-6">
                <!-- Quick Actions Widget -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Quick Actions</h2>
                    </div>
                    <div class="p-4">
                        <div class="grid grid-cols-2 gap-3">
                            @foreach($quickActions as $action)
                                <a href="{{ route($action['route']) }}"
                                   class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                    <div class="h-10 w-10 rounded-lg mb-2 flex items-center justify-center
                                        {{ match($action['color']) {
                                            'green' => 'bg-green-100 text-green-600',
                                            'blue' => 'bg-blue-100 text-blue-600',
                                            'purple' => 'bg-purple-100 text-purple-600',
                                            'orange' => 'bg-orange-100 text-orange-600',
                                            default => 'bg-gray-100 text-gray-600'
                                        } }}">
                                        @switch($action['icon'])
                                            @case('clipboard-check')
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                                </svg>
                                                @break
                                            @case('assignment')
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                                </svg>
                                                @break
                                            @case('users')
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                                </svg>
                                                @break
                                            @case('check-circle')
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                @break
                                        @endswitch
                                    </div>
                                    <span class="text-xs font-medium text-gray-900 text-center">{{ $action['label'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Inventory Summary Widget -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Inventory Summary</h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                    <span class="text-sm text-gray-700">Total Products</span>
                                </div>
                                <span class="text-sm font-bold text-gray-900">{{ $inventorySummary['total_products'] }}</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                    <span class="text-sm text-gray-700">Total Inventory</span>
                                </div>
                                <span class="text-sm font-bold text-gray-900">{{ $inventorySummary['total_inventory'] }}</span>
                            </div>
                            @if($inventorySummary['low_stock_count'] > 0)
                                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                        <span class="text-sm text-yellow-800">Low Stock Items</span>
                                    </div>
                                    <span class="text-sm font-bold text-yellow-800">{{ $inventorySummary['low_stock_count'] }}</span>
                                </div>
                            @endif
                        </div>
                        @if(count($inventorySummary['low_stock_items']) > 0)
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <h4 class="text-xs font-semibold text-gray-500 uppercase mb-2">Low Stock Items</h4>
                                <div class="space-y-2">
                                    @foreach($inventorySummary['low_stock_items'] as $item)
                                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                            <span class="text-sm text-gray-700">{{ $item->productName ?? 'Unknown' }}</span>
                                            <span class="text-xs font-medium text-yellow-600">{{ $item->quantity ?? 0 }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- User Activity Widget -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">User Activity</h2>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm text-gray-500">Active Operators</span>
                            <span class="text-sm font-bold text-purple-600">{{ $userActivity['active_operators'] }}</span>
                        </div>
                        @if(count($userActivity['recent_logins']) > 0)
                            <div class="space-y-3">
                                @foreach($userActivity['recent_logins'] as $user)
                                    <div class="flex items-center gap-3 p-2 rounded hover:bg-gray-50">
                                        <div class="relative">
                                            <div class="h-8 w-8 rounded-full bg-purple-100 flex items-center justify-center">
                                                <span class="text-xs font-medium text-purple-600">{{ strtoupper(substr($user['name'], 0, 1)) }}</span>
                                            </div>
                                            @if($user['is_online'])
                                                <span class="absolute bottom-0 right-0 h-2 w-2 rounded-full bg-green-500 ring-2 ring-white"></span>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ $user['name'] }}</p>
                                            <p class="text-xs text-gray-500">{{ $user['last_login'] ?? 'Never' }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 text-center py-4">No recent activity</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>