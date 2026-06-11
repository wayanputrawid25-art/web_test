<div>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $session->code }}</h1>
                <p class="text-gray-600 mt-1">{{ $session->name }}</p>
                <div class="flex items-center gap-2 mt-2">
                    <span class="px-2.5 py-1 rounded-full text-xs font-medium
                        {{ match($session->status->color()) {
                            'gray' => 'bg-gray-100 text-gray-800',
                            'blue' => 'bg-blue-100 text-blue-800',
                            'yellow' => 'bg-yellow-100 text-yellow-800',
                            'purple' => 'bg-purple-100 text-purple-800',
                            'orange' => 'bg-orange-100 text-orange-800',
                            'green' => 'bg-green-100 text-green-800',
                            default => 'bg-gray-100 text-gray-800'
                        } }}">
                        {{ $session->status->label() }}
                    </span>
                    @if($session->itemCount > 0)
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            {{ $session->countedCount }}/{{ $session->itemCount }} counted
                        </span>
                    @endif
                </div>
            </div>
            <a href="{{ route('stock_opnames.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back
            </a>
        </div>
    </div>

    <!-- Messages -->
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-green-800">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-red-800">{{ session('error') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Progress -->
            @if($session->itemCount > 0)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="text-lg font-semibold text-gray-900">Progress</h2>
                        <span class="text-sm text-gray-500">{{ $session->getProgressPercentage() }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ $session->getProgressPercentage() }}%"></div>
                    </div>
                    <div class="flex justify-between mt-2 text-sm text-gray-500">
                        <span>{{ $session->countedCount }} counted</span>
                        <span>{{ $session->varianceCount }} variances</span>
                    </div>
                </div>
            @endif

            <!-- Variance Summary -->
            @if(count($items) > 0 && $session->status->isReviewable())
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Variance Summary</h2>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <p class="text-2xl font-bold text-green-600">{{ collect($items)->filter(fn($i) => $i['variance'] > 0)->count() }}</p>
                            <p class="text-sm text-gray-600">Surplus</p>
                        </div>
                        <div class="text-center p-4 bg-red-50 rounded-lg">
                            <p class="text-2xl font-bold text-red-600">{{ collect($items)->filter(fn($i) => $i['variance'] < 0)->count() }}</p>
                            <p class="text-sm text-gray-600">Shortage</p>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <p class="text-2xl font-bold text-gray-600">{{ collect($items)->filter(fn($i) => $i['variance'] == 0)->count() }}</p>
                            <p class="text-sm text-gray-600">Matched</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Items Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Items</h2>
                    @can('edit-stock-opnames')
                        @if($session->status->isCountingAllowed())
                            <a href="{{ route('stock_opnames.count', $session->id) }}"
                               class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                                Count Items
                            </a>
                        @endif
                    @endcan
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">System Qty</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Counted</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Variance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($items as $item)
                            <tr class="{{ $item['variance'] && $item['variance'] != 0 ? 'bg-red-50' : '' }}">
                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-900">{{ $item['product_sku'] }}</p>
                                    <p class="text-sm text-gray-500">{{ $item['product_name'] }}</p>
                                </td>
                                <td class="px-6 py-4 text-right text-gray-900">{{ number_format($item['system_quantity'], 2) }}</td>
                                <td class="px-6 py-4 text-right text-gray-900">
                                    {{ $item['counted_quantity'] !== null ? number_format($item['counted_quantity'], 2) : '-' }}
                                </td>
                                <td class="px-6 py-4 text-right font-medium {{ $item['variance'] > 0 ? 'text-green-600' : ($item['variance'] < 0 ? 'text-red-600' : 'text-gray-600') }}">
                                    @if($item['variance'] !== null)
                                        {{ $item['variance'] > 0 ? '+' : '' }}{{ number_format($item['variance'], 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">No items yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Activity Log -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Activity Log</h2>
                
                @can('edit-stock-opnames')
                    <div class="flex gap-2 mb-6">
                        <input type="text" wire:model="newComment" placeholder="Add a comment..."
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <button wire:click="addComment"
                                class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                            Add
                        </button>
                    </div>
                @endcan

                <div class="space-y-3 max-h-64 overflow-y-auto">
                    @forelse($activityLogs as $log)
                        <div class="flex gap-3 p-3 bg-gray-50 rounded-lg">
                            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                <span class="text-xs font-medium text-indigo-600">{{ strtoupper(substr($log['user_name'] ?? '?', 0, 1)) }}</span>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-gray-900">{{ $log['user_name'] ?? 'Unknown' }}</p>
                                    <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($log['created_at'])->format('M d, H:i') }}</p>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">
                                    @switch($log['action'])
                                        @case('status_changed')
                                            Changed status from <strong>{{ ucfirst(str_replace('_', ' ', $log['old_value'])) }}</strong>
                                            to <strong>{{ ucfirst(str_replace('_', ' ', $log['new_value'])) }}</strong>
                                            @if($log['notes']) - {{ $log['notes'] }} @endif
                                            @break
                                        @case('assigned')
                                            Assigned counters
                                            @break
                                        @case('item_counted')
                                            Counted {{ $log['notes'] ?? 'item' }}
                                            @break
                                        @default
                                            {{ $log['notes'] ?? $log['action'] }}
                                    @endswitch
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">No activity yet</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Actions -->
            @can('edit-stock-opnames')
                @if($session->status->value === 'created')
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                        <h3 class="font-semibold text-gray-900 mb-4">Next Action</h3>
                        <a href="{{ route('stock_opnames.assign', $session->id) }}"
                           class="block w-full text-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700">
                            Assign Counters
                        </a>
                    </div>
                @endif

                @if($session->status->value === 'assigned' || $session->status->value === 'counting')
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                        <h3 class="font-semibold text-gray-900 mb-4">Actions</h3>
                        <a href="{{ route('stock_opnames.count', $session->id) }}"
                           class="block w-full text-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 mb-2">
                            Count Items
                        </a>
                        @if($session->countedCount == $session->itemCount && $session->itemCount > 0)
                            <button wire:click="submitForReview"
                                    class="w-full px-4 py-2 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700">
                                Submit for Review
                            </button>
                        @endif
                    </div>
                @endif

                @if($session->status->isReviewable())
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                        <h3 class="font-semibold text-gray-900 mb-4">Review Actions</h3>
                        <button wire:click="approve"
                                class="w-full px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 mb-2">
                            Approve
                        </button>
                        <button wire:click="$set('showRevisionForm', true)"
                                class="w-full px-4 py-2 bg-orange-600 text-white font-medium rounded-lg hover:bg-orange-700">
                            Request Revision
                        </button>
                    </div>
                @endif
            @endcan

            <!-- Details -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <h3 class="font-semibold text-gray-900 mb-4">Details</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm text-gray-500">Created By</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $session->creatorName }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Created</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $session->createdAt?->format('M d, Y H:i') }}</dd>
                    </div>
                    @if($session->countDeadline)
                        <div>
                            <dt class="text-sm text-gray-500">Deadline</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $session->countDeadline->format('M d, Y') }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-sm text-gray-500">Total Items</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $session->itemCount }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Counters -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-900">Counters</h3>
                    @can('edit-stock-opnames')
                        @if($session->status->value === 'created')
                            <a href="{{ route('stock_opnames.assign', $session->id) }}"
                               class="text-sm text-indigo-600 hover:text-indigo-800">Edit</a>
                        @endif
                    @endcan
                </div>
                @forelse($assignments as $assignment)
                    <div class="flex items-center py-2 border-b border-gray-100 last:border-b-0">
                        <div class="h-6 w-6 rounded-full bg-indigo-100 flex items-center justify-center">
                            <span class="text-xs font-medium text-indigo-600">{{ strtoupper(substr($assignment['user_name'] ?? '?', 0, 1)) }}</span>
                        </div>
                        <span class="ml-2 text-sm text-gray-700">{{ $assignment['user_name'] }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No counters assigned</p>
                @endforelse
            </div>
        </div>
    </div>
</div>