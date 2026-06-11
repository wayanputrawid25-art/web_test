<div>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $request->code }}</h1>
                <p class="text-gray-600 mt-1">{{ $request->title }}</p>
                <div class="flex items-center gap-2 mt-2">
                    <span class="px-2.5 py-1 rounded-full text-xs font-medium
                        {{ match($request->status->color()) {
                            'yellow' => 'bg-yellow-100 text-yellow-800',
                            'green' => 'bg-green-100 text-green-800',
                            'red' => 'bg-red-100 text-red-800',
                            'orange' => 'bg-orange-100 text-orange-800',
                            default => 'bg-gray-100 text-gray-800'
                        } }}">
                        {{ $request->status->label() }}
                    </span>
                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        {{ $request->type->label() }}
                    </span>
                </div>
            </div>
            <a href="{{ route('approvals.index') }}"
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
            <!-- Details -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Request Details</h2>
                <dl class="grid grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm text-gray-500">Reference ID</dt>
                        <dd class="text-sm font-medium text-gray-900">#{{ $request->referenceId }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Type</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $request->type->label() }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Requester</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $request->requesterName }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Created</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $request->createdAt?->format('M d, Y H:i') }}</dd>
                    </div>
                    @if($request->approverName)
                        <div>
                            <dt class="text-sm text-gray-500">Approver</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $request->approverName }}</dd>
                        </div>
                    @endif
                    @if($request->processedAt)
                        <div>
                            <dt class="text-sm text-gray-500">Processed At</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $request->processedAt?->format('M d, Y H:i') }}</dd>
                        </div>
                    @endif
                </dl>
                @if($request->description)
                    <div class="mt-4">
                        <dt class="text-sm text-gray-500 mb-1">Description</dt>
                        <dd class="text-sm text-gray-700">{{ $request->description }}</dd>
                    </div>
                @endif
                @if($request->notes)
                    <div class="mt-4">
                        <dt class="text-sm text-gray-500 mb-1">Notes</dt>
                        <dd class="text-sm text-gray-700">{{ $request->notes }}</dd>
                    </div>
                @endif
            </div>

            <!-- Decision -->
            @if($decision)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Decision</h2>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium
                                {{ $decision->decision === 'approved' ? 'bg-green-100 text-green-800' : ($decision->decision === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-orange-100 text-orange-800') }}">
                                {{ ucfirst(str_replace('_', ' ', $decision->decision)) }}
                            </span>
                            <span class="text-sm text-gray-500">{{ $decision->createdAt?->format('M d, Y H:i') }}</span>
                        </div>
                        <p class="text-sm text-gray-700">{{ $decision->approverName }}</p>
                        @if($decision->comments)
                            <p class="text-sm text-gray-600 mt-2">{{ $decision->comments }}</p>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Activity Log -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Activity Log</h2>
                
                <div class="flex gap-2 mb-6">
                    <input type="text" wire:model="newComment" placeholder="Add a comment..."
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <button wire:click="addComment"
                            class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                        Add
                    </button>
                </div>

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
                                        @case('created')
                                            Created approval request
                                            @break
                                        @case('approved')
                                            Approved the request
                                            @break
                                        @case('rejected')
                                            Rejected the request
                                            @break
                                        @case('revision_requested')
                                            Requested revision
                                            @break
                                        @case('commented')
                                            {{ $log['notes'] ?? 'Added a comment' }}
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
            @if($request->isPending() && $canApprove())
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <h3 class="font-semibold text-gray-900 mb-4">Actions</h3>
                    <div class="space-y-2">
                        <button wire:click="openDecisionModal('approve')"
                                class="w-full px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700">
                            Approve
                        </button>
                        <button wire:click="openDecisionModal('reject')"
                                class="w-full px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700">
                            Reject
                        </button>
                        <button wire:click="openDecisionModal('revision')"
                                class="w-full px-4 py-2 bg-orange-600 text-white font-medium rounded-lg hover:bg-orange-700">
                            Request Revision
                        </button>
                    </div>
                </div>
            @endif

            <!-- Reference Link -->
            @if($request->type->value === 'stock_opname')
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <h3 class="font-semibold text-gray-900 mb-4">Reference</h3>
                    <a href="{{ route('stock_opnames.show', $request->referenceId) }}"
                       class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                        View Stock Opname →
                    </a>
                </div>
            @endif

            <!-- Quick Stats -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <h3 class="font-semibold text-gray-900 mb-4">Timeline</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm text-gray-500">Created</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $request->createdAt?->format('M d, Y H:i') }}</dd>
                    </div>
                    @if($request->updatedAt)
                        <div>
                            <dt class="text-sm text-gray-500">Last Updated</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $request->updatedAt?->format('M d, Y H:i') }}</dd>
                        </div>
                    @endif
                    @if($request->processedAt)
                        <div>
                            <dt class="text-sm text-gray-500">Processed</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $request->processedAt?->format('M d, Y H:i') }}</dd>
                        </div>
                    @endif
                </dl>
            </div>
        </div>
    </div>

    <!-- Decision Modal -->
    @if($showDecisionModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeDecisionModal"></div>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            @switch($showDecisionModal)
                                @case('approve')
                                    Approve Request
                                    @break
                                @case('reject')
                                    Reject Request
                                    @break
                                @case('revision')
                                    Request Revision
                                    @break
                            @endswitch
                        </h3>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Comments</label>
                            <textarea wire:model="decisionComments" rows="4"
                                      placeholder="Please provide your decision comments..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                                             {{ $showDecisionModal === 'reject' || $showDecisionModal === 'revision' ? 'border-red-300' : '' }}"></textarea>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        @switch($showDecisionModal)
                            @case('approve')
                                <button wire:click="approve" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 sm:ml-3 sm:w-auto sm:text-sm">
                                    Approve
                                </button>
                                @break
                            @case('reject')
                                <button wire:click="reject" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">
                                    Reject
                                </button>
                                @break
                            @case('revision')
                                <button wire:click="requestRevision" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-orange-600 text-base font-medium text-white hover:bg-orange-700 sm:ml-3 sm:w-auto sm:text-sm">
                                    Request Revision
                                </button>
                                @break
                        @endswitch
                        <button wire:click="closeDecisionModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>