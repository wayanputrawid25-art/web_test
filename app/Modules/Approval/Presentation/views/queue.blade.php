<div>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Approval Queue</h1>
                <p class="text-gray-600 mt-1">Review and process pending approval requests</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('approvals.queue.my') }}"
                   class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $queueType === 'my' ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    My Queue
                </a>
                <a href="{{ route('approvals.queue.all') }}"
                   class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $queueType === 'all' ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    All Pending
                </a>
            </div>
        </div>
    </div>

    <!-- Pending Count -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <svg class="h-5 w-5 text-yellow-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <span class="text-yellow-800 font-medium">{{ $pendingCount }} pending request(s) require your attention</span>
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

    <!-- Search -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <input type="text"
               wire:model.live.debounce.300ms="search"
               placeholder="Search requests..."
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    </div>

    <!-- Queue List -->
    <div class="space-y-4">
        @forelse($requests as $request)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <a href="{{ route('approvals.show', $request->id) }}" class="text-lg font-semibold text-gray-900 hover:text-indigo-600">
                                {{ $request->code }}
                            </a>
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                {{ $request->status->label() }}
                            </span>
                        </div>
                        <h3 class="text-gray-900 mb-1">{{ $request->title }}</h3>
                        @if($request->description)
                            <p class="text-sm text-gray-500 mb-2">{{ Str::limit($request->description, 150) }}</p>
                        @endif
                        <div class="flex items-center gap-4 text-sm text-gray-500">
                            <span>{{ $request->type->label() }}</span>
                            <span>•</span>
                            <span>Requested by {{ $request->requesterName }}</span>
                            <span>•</span>
                            <span>{{ $request->createdAt?->diffForHumans() }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 ml-4">
                        <a href="{{ route('approvals.show', $request->id) }}"
                           class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                            Review
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No pending requests</h3>
                <p class="mt-1 text-sm text-gray-500">You're all caught up! No pending approval requests at the moment.</p>
            </div>
        @endforelse
    </div>

    @if($requests->hasPages())
        <div class="mt-6">
            {{ $requests->links() }}
        </div>
    @endif
</div>