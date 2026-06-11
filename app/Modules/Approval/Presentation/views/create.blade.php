<div>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">New Approval Request</h1>
                <p class="text-gray-600 mt-1">Submit a new approval request</p>
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

    <form wire:submit="save" class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 space-y-4">
            <!-- Type Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Approval Type <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($types as $type)
                        <label class="flex items-center p-4 border rounded-lg cursor-pointer transition-colors
                                    {{ $type === $type->value ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300 hover:border-gray-400' }}">
                            <input type="radio" wire:model.live="type" value="{{ $type->value }}"
                                   class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ $type->label() }}</p>
                                <p class="text-xs text-gray-500">{{ $type->description() }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Reference Selection -->
            @if($type)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ $referenceLabel ?? 'Select Reference' }} <span class="text-red-500">*</span>
                    </label>
                    @if(count($availableReferences) > 0)
                        <div class="border border-gray-300 rounded-lg max-h-48 overflow-y-auto">
                            @foreach($availableReferences as $ref)
                                <label class="flex items-center p-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0 cursor-pointer">
                                    <input type="radio" wire:model.live="referenceId" value="{{ $ref['id'] }}"
                                           class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                    <div class="ml-3 flex-1">
                                        <p class="text-sm font-medium text-gray-900">{{ $ref['label'] }}</p>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 mt-1">
                                            {{ $ref['status'] }}
                                        </span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 p-3 bg-gray-50 rounded-lg">
                            No available references found for this type.
                        </p>
                    @endif
                    @error('referenceId')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            <!-- Title -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Title <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       wire:model="title"
                       placeholder="e.g., Approval for Monthly Stock Opname"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                              {{ $errors->has('title') ? 'border-red-300' : 'border-gray-300' }}">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea wire:model="description"
                          rows="4"
                          placeholder="Provide additional context for this approval request..."
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            </div>

            <!-- Approver Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Assign Approver (Optional)</label>
                <select wire:model.live="approverId" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Auto-assign (first available)</option>
                    @foreach($approvers as $approver)
                        <option value="{{ $approver['id'] }}">{{ $approver['name'] }} ({{ $approver['email'] }})</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">Leave empty to auto-assign to the first available approver.</p>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                <textarea wire:model="notes"
                          rows="3"
                          placeholder="Any additional notes..."
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
            <button type="button" wire:click="cancel"
                    class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200">
                Cancel
            </button>
            <button type="submit"
                    wire:loading.attr="disabled"
                    {{ !$type || !$referenceId ? 'disabled' : '' }}
                    class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed">
                <span wire:loading.remove wire:target="save">Submit Request</span>
                <span wire:loading wire:target="save">Processing...</span>
            </button>
        </div>
    </form>
</div>