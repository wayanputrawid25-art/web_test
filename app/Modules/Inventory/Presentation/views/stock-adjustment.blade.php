<div>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Stock Adjustment</h1>
                <p class="text-gray-600 mt-1">Adjust inventory stock levels</p>
            </div>
            <a href="{{ route('inventory.ledger') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                View Ledger
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">New Adjustment</h2>
                </div>
                <form wire:submit="store" class="p-6 space-y-4">
                    @if(session('success'))
                        <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                            <p class="text-green-800">{{ session('success') }}</p>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                            <p class="text-red-800">{{ session('error') }}</p>
                        </div>
                    @endif

                    <!-- Product Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Product <span class="text-red-500">*</span></label>
                        @if($selectedProduct)
                            <div class="flex items-center justify-between p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $selectedProduct['name'] }}</p>
                                    <p class="text-sm text-gray-500">{{ $selectedProduct['sku'] }}</p>
                                    <p class="text-xs text-blue-600 mt-1">Current Stock: {{ $currentStock }}</p>
                                </div>
                                <button type="button" wire:click="clearProduct" class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        @else
                            <input type="text"
                                   wire:model.live.debounce.300ms="searchProduct"
                                   placeholder="Search product by name or SKU..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @if($searchProduct && count($products) > 0)
                                <div class="mt-2 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                    @foreach($products as $product)
                                        <button type="button"
                                                wire:click="selectProduct({{ json_encode($product) }})"
                                                class="w-full text-left px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-0">
                                            <p class="font-medium text-gray-900">{{ $product['name'] }}</p>
                                            <p class="text-sm text-gray-500">{{ $product['sku'] }}</p>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        @endif
                        @error('productId')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Adjustment Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Adjustment Type <span class="text-red-500">*</span></label>
                        <div class="flex gap-4">
                            <label class="flex items-center">
                                <input type="radio" wire:model="adjustmentType" value="add"
                                       class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Add Stock</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" wire:model="adjustmentType" value="reduce"
                                       class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Reduce Stock</span>
                            </label>
                        </div>
                        @error('adjustmentType')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Quantity -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantity <span class="text-red-500">*</span></label>
                        <input type="number"
                               wire:model="quantity"
                               min="1"
                               placeholder="Enter quantity to adjust"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                      {{ $errors->has('quantity') ? 'border-red-300' : 'border-gray-300' }}">
                        @error('quantity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if($currentStock > 0 && $adjustmentType === 'reduce')
                            <p class="mt-1 text-sm text-gray-500">
                                New balance will be: {{ $currentStock - $quantity }}
                            </p>
                        @elseif($currentStock > 0 && $adjustmentType === 'add')
                            <p class="mt-1 text-sm text-gray-500">
                                New balance will be: {{ $currentStock + $quantity }}
                            </p>
                        @endif
                    </div>

                    <!-- Reference -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Reference Number</label>
                        <input type="text"
                               wire:model="reference"
                               placeholder="e.g., ADJ-2024-001"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea wire:model="notes"
                                  rows="3"
                                  placeholder="Reason for adjustment..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>

                    <!-- Submit -->
                    <div class="flex justify-end pt-4">
                        <button type="submit"
                                wire:loading.attr="disabled"
                                class="px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50">
                            <span wire:loading.remove wire:target="store">Save Adjustment</span>
                            <span wire:loading wire:target="store">Processing...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Recent Adjustments -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Recent Adjustments</h2>
            </div>
            <div class="p-4 max-h-96 overflow-y-auto">
                @forelse($recentTransactions as $transaction)
                    <div class="py-3 border-b border-gray-100 last:border-0">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-medium text-gray-900">{{ $transaction->productSku }}</p>
                                <p class="text-sm text-gray-500">{{ $transaction->productName }}</p>
                            </div>
                            <span class="font-medium {{ $transaction->quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $transaction->quantity > 0 ? '+' : '' }}{{ $transaction->quantity }}
                            </span>
                        </div>
                        @if($transaction->notes)
                            <p class="text-xs text-gray-400 mt-1">{{ Str::limit($transaction->notes, 50) }}</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-1">{{ $transaction->createdAt?->format('M d, Y H:i') }}</p>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No recent adjustments</p>
                @endforelse
            </div>
        </div>
    </div>
</div>