<div>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Assign Counters</h1>
                <p class="text-gray-600 mt-1">{{ $session->code }} - {{ $session->name }}</p>
            </div>
            <a href="{{ route('stock_opnames.show', $session->id) }}"
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

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Current Assignments -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Current Counters</h2>
            </div>
            <div class="p-6">
                @forelse($currentAssignments as $assignment)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg mb-2 last:mb-0">
                        <div class="flex items-center">
                            <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                <span class="text-sm font-medium text-indigo-600">{{ strtoupper(substr($assignment['user_name'] ?? '?', 0, 1)) }}</span>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ $assignment['user_name'] }}</p>
                                <p class="text-xs text-gray-500">{{ $assignment['user_email'] }}</p>
                            </div>
                        </div>
                        <button wire:click="removeAssignment({{ $assignment['id'] }})"
                                class="text-red-600 hover:text-red-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-4">No counters assigned yet</p>
                @endforelse
            </div>
        </div>

        <!-- Available Users -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Select Counters</h2>
                <p class="text-sm text-gray-500 mt-1">Select users to assign as counters for this session</p>
            </div>
            <div class="p-6 max-h-96 overflow-y-auto">
                @forelse($availableUsers as $user)
                    @php
                        $isAssigned = collect($currentAssignments)->contains('user_id', $user['id']);
                        $isSelected = in_array($user['id'], $selectedUsers);
                    @endphp
                    <label class="flex items-center p-3 rounded-lg mb-2 last:mb-0 cursor-pointer transition-colors
                                {{ $isAssigned ? 'bg-gray-100 opacity-50' : ($isSelected ? 'bg-indigo-50 border border-indigo-200' : 'bg-gray-50 hover:bg-gray-100') }}">
                        <input type="checkbox"
                               value="{{ $user['id'] }}"
                               wire:change="toggleUser({{ $user['id'] })"
                               {{ $isSelected ? 'checked' : '' }}
                               {{ $isAssigned ? 'disabled' : '' }}
                               class="w-4 h-4 text-indigo-600 border-gray-300 rounded">
                        <div class="ml-3 flex-1">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <span class="text-sm font-medium text-indigo-600">{{ strtoupper(substr($user['name'], 0, 1)) }}</span>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $user['name'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $user['email'] }}</p>
                                </div>
                            </div>
                        </div>
                        @if($isAssigned)
                            <span class="text-xs text-gray-500">Already assigned</span>
                        @endif
                    </label>
                @empty
                    <p class="text-center text-gray-500 py-4">No users available</p>
                @endforelse
            </div>
            <div class="px-6 py-4 border-t border-gray-200">
                <p class="text-sm text-gray-500 mb-3">{{ count($selectedUsers) }} users selected</p>
                <button wire:click="assign"
                        wire:loading.attr="disabled"
                        {{ empty($selectedUsers) ? 'disabled' : '' }}
                        class="w-full px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="assign">Assign Selected Counters</span>
                    <span wire:loading wire:target="assign">Assigning...</span>
                </button>
            </div>
        </div>
    </div>
</div>