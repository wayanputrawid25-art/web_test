<div>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Create Stock Opname Session</h1>
                <p class="text-gray-600 mt-1">Create a new stock opname session</p>
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

    <form wire:submit="save" class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 space-y-4">
            <!-- Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Session Name <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       wire:model="name"
                       placeholder="e.g., Monthly Stock Opname January 2024"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                              {{ $errors->has('name') ? 'border-red-300' : 'border-gray-300' }}">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea wire:model="description"
                          rows="3"
                          placeholder="Optional description..."
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Start Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                    <input type="date"
                           wire:model="startDate"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- End Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                    <input type="date"
                           wire:model="endDate"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('endDate')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Count Deadline -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Count Deadline</label>
                    <input type="date"
                           wire:model="countDeadline"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <!-- Products Selection -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Select Products <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-2">
                        <button type="button" wire:click="selectAll"
                                class="text-xs text-indigo-600 hover:text-indigo-800">
                            Select All
                        </button>
                        <span class="text-gray-300">|</span>
                        <button type="button" wire:click="clearSelection"
                                class="text-xs text-gray-600 hover:text-gray-800">
                            Clear
                        </button>
                    </div>
                </div>

                @error('selectedProducts')
                    <p class="mb-2 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <div class="border border-gray-300 rounded-lg max-h-64 overflow-y-auto">
                    @forelse($products as $product)
                        <label class="flex items-center p-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0 cursor-pointer">
                            <input type="checkbox"
                                   value="{{ $product['id'] }}"
                                   wire:change="toggleProduct({{ $product['id'] }})"
                                   {{ in_array($product['id'], $selectedProducts) ? 'checked' : '' }}
                                   class="w-4 h-4 text-indigo-600 border-gray-300 rounded">
                            <div class="ml-3 flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-gray-900">{{ $product['sku'] }}</span>
                                    <span class="text-sm text-gray-500">{{ $product['category'] ?? 'Uncategorized' }}</span>
                                </div>
                                <div class="flex items-center justify-between mt-1">
                                    <span class="text-sm text-gray-600">{{ $product['name'] }}</span>
                                    <span class="text-sm font-medium text-gray-900">Stock: {{ $product['quantity'] }}</span>
                                </div>
                            </div>
                        </label>
                    @empty
                        <div class="p-4 text-center text-gray-500">
                            No active products available
                        </div>
                    @endforelse
                </div>

                <p class="mt-2 text-sm text-gray-500">
                    {{ count($selectedProducts) }} products selected
                </p>
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
            <button type="button" wire:click="cancel"
                    class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200">
                Cancel
            </button>
            <button type="submit"
                    wire:loading.attr="disabled"
                    class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 disabled:opacity-50">
                <span wire:loading.remove wire:target="save">Create Session</span>
                <span wire:loading wire:target="save">Processing...</span>
            </button>
        </div>
    </form>
</div>