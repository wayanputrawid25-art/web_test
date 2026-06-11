<div>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $task->title }}</h1>
                <div class="flex items-center gap-2 mt-2">
                    <span class="px-2.5 py-1 rounded-full text-xs font-medium
                        {{ match($task->status->color()) {
                            'gray' => 'bg-gray-100 text-gray-800',
                            'blue' => 'bg-blue-100 text-blue-800',
                            'yellow' => 'bg-yellow-100 text-yellow-800',
                            'purple' => 'bg-purple-100 text-purple-800',
                            'green' => 'bg-green-100 text-green-800',
                            'slate' => 'bg-slate-100 text-slate-800',
                            default => 'bg-gray-100 text-gray-800'
                        } }}">
                        {{ $task->status->label() }}
                    </span>
                    <span class="px-2.5 py-1 rounded-full text-xs font-medium
                        {{ match($task->priority->color()) {
                            'slate' => 'bg-slate-100 text-slate-800',
                            'blue' => 'bg-blue-100 text-blue-800',
                            'orange' => 'bg-orange-100 text-orange-800',
                            'red' => 'bg-red-100 text-red-800',
                            default => 'bg-gray-100 text-gray-800'
                        } }}">
                        {{ $task->priority->label() }}
                    </span>
                </div>
            </div>
            <a href="{{ route('tasks.index') }}"
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
        <!-- Task Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Description -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Description</h2>
                <p class="text-gray-700 whitespace-pre-wrap">{{ $task->description ?: 'No description provided.' }}</p>
            </div>

            <!-- Status Actions -->
            @can('edit-tasks')
                @if(count($task->getNextStatuses()) > 0)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Change Status</h2>
                        <div class="flex flex-wrap gap-2 mb-4">
                            @foreach($task->getNextStatuses() as $nextStatus)
                                <button wire:click="changeStatus('{{ $nextStatus->value }}')"
                                        class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                                    Set to {{ $nextStatus->label() }}
                                </button>
                            @endforeach
                        </div>
                        <div>
                            <input type="text"
                                   wire:model="statusNotes"
                                   placeholder="Add notes for status change (optional)"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                @endif
            @endcan

            <!-- Activity Log -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Activity Log</h2>
                
                <!-- Add Comment -->
                @can('edit-tasks')
                    <div class="flex gap-2 mb-6">
                        <input type="text"
                               wire:model="newComment"
                               placeholder="Add a comment..."
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <button wire:click="addComment"
                                class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                            Add Comment
                        </button>
                    </div>
                @endcan

                <!-- Logs List -->
                <div class="space-y-4 max-h-96 overflow-y-auto">
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
                                            Changed status from 
                                            <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $log['old_value'])) }}</span>
                                            to
                                            <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $log['new_value'])) }}</span>
                                            @if($log['notes'])
                                                <span class="text-gray-400">- "{{ $log['notes'] }}"</span>
                                            @endif
                                            @break
                                        @case('assigned')
                                            Assigned task to user
                                            @break
                                        @case('reassigned')
                                            Reassigned task from user #{{ $log['old_value'] }} to user #{{ $log['new_value'] }}
                                            @break
                                        @case('updated')
                                            Updated task details
                                            @break
                                        @case('commented')
                                            {{ $log['notes'] }}
                                            @break
                                        @default
                                            {{ $log['action'] }}
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
            <!-- Task Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Details</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-500">Assignee</p>
                        <div class="flex items-center mt-1">
                            <div class="h-6 w-6 rounded-full bg-indigo-100 flex items-center justify-center">
                                <span class="text-xs font-medium text-indigo-600">{{ strtoupper(substr($task->assigneeName ?? '?', 0, 1)) }}</span>
                            </div>
                            <span class="ml-2 text-sm font-medium text-gray-900">{{ $task->assigneeName }}</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Created By</p>
                        <p class="text-sm font-medium text-gray-900 mt-1">{{ $task->creatorName ?? 'Unknown' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Due Date</p>
                        <p class="text-sm font-medium text-gray-900 mt-1">{{ $task->dueDate?->format('M d, Y') ?? 'No due date' }}</p>
                    </div>
                    @if($task->productSku)
                        <div>
                            <p class="text-sm text-gray-500">Related Product</p>
                            <p class="text-sm font-medium text-gray-900 mt-1">{{ $task->productSku }}</p>
                        </div>
                    @endif
                    <div>
                        <p class="text-sm text-gray-500">Created</p>
                        <p class="text-sm font-medium text-gray-900 mt-1">{{ $task->createdAt?->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Reassign -->
            @can('edit-tasks')
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Reassign</h3>
                    <p class="text-sm text-gray-500 mb-3">Select a new assignee</p>
                    <select wire:change="assignTo($event.target.value)"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select user...</option>
                        @foreach(\App\Modules\Users\Infrastructure\Models\User::active()->get() as $user)
                            <option value="{{ $user->id }}" {{ $user->id == $task->assigneeId ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endcan
        </div>
    </div>
</div>