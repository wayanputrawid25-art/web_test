<div>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $myTasks ? 'My Tasks' : 'Tasks' }}</h1>
                <p class="text-gray-600 mt-1">Manage tasks and track progress</p>
            </div>
            @can('create-tasks')
                <a href="{{ route('tasks.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Create Task
                </a>
            @endcan
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Status Counts -->
    <div class="grid grid-cols-2 md:grid-cols-6 gap-3 mb-6">
        @foreach($statuses as $status)
            <button wire:click="$set('statusFilter', {{ $statusFilter === '$status->value' ? 'null' : \"'$status->value'\" }})"
                    class="p-3 rounded-lg border text-center transition-all hover:shadow-md
                        {{ $statusFilter === $status->value ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 bg-white hover:border-gray-300' }}">
                <p class="text-2xl font-bold text-gray-900">{{ $statusCounts[$status->value] ?? 0 }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $status->label() }}</p>
            </button>
        @endforeach
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Search by title or description..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                <select wire:model.live="priorityFilter"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Priorities</option>
                    @foreach($priorities as $priority)
                        <option value="{{ $priority->value }}">{{ $priority->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <label class="flex items-center">
                    <input type="checkbox" wire:model.live="myTasks" class="w-4 h-4 text-indigo-600 border-gray-300 rounded">
                    <span class="ml-2 text-sm text-gray-700">My Tasks Only</span>
                </label>
            </div>
        </div>
        @if($search || $statusFilter || $priorityFilter || $myTasks)
            <div class="mt-4 flex items-center gap-2">
                <span class="text-sm text-gray-500">Active filters:</span>
                @if($search)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        Search: {{ $search }}
                    </span>
                @endif
                @if($statusFilter)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                        Status: {{ ucfirst(str_replace('_', ' ', $statusFilter)) }}
                    </span>
                @endif
                @if($priorityFilter)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Priority: {{ ucfirst($priorityFilter) }}
                    </span>
                @endif
                @if($myTasks)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                        My Tasks
                    </span>
                @endif
                <button wire:click="resetFilters" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    Clear all
                </button>
            </div>
        @endif
    </div>

    <!-- Tasks Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assignee</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($tasks as $task)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <a href="{{ route('tasks.show', $task->id) }}" class="block">
                                <p class="font-medium text-gray-900 hover:text-indigo-600">{{ $task->title }}</p>
                                @if($task->productSku)
                                    <p class="text-xs text-gray-500 mt-1">Product: {{ $task->productSku }}</p>
                                @endif
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
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
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
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
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-6 w-6 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <span class="text-xs font-medium text-indigo-600">{{ strtoupper(substr($task->assigneeName ?? '?', 0, 1)) }}</span>
                                </div>
                                <span class="ml-2 text-sm text-gray-700">{{ $task->assigneeName }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $task->dueDate?->format('M d, Y') ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('tasks.show', $task->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                            @can('edit-tasks')
                                <a href="{{ route('tasks.edit', $task->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                            @endcan
                            @can('delete-tasks')
                                <button wire:click="delete({{ $task->id }})"
                                        wire:confirm="Are you sure you want to delete this task?"
                                        class="text-red-600 hover:text-red-900">Delete</button>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No tasks found</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating a new task.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($tasks->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $tasks->links() }}
            </div>
        @endif
    </div>
</div>